<?php

namespace App\Http\Controllers\User;

use Exception;
use App\Models\User;
use App\Models\Voucher;
use App\Models\UserWallet;
use Jenssegers\Agent\Agent;
use Illuminate\Http\Request;
use App\Constants\GlobalConst;
use Illuminate\Support\Carbon;
use App\Models\UserNotification;
use Illuminate\Support\Facades\DB;
use App\Models\Admin\BasicSettings;
use App\Constants\NotificationConst;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Constants\PaymentGatewayConst;
use App\Events\User\NotificationEvent;
use App\Models\Admin\AdminNotification;
use App\Models\Admin\TransactionSetting;
use Illuminate\Support\Facades\Validator;
use App\Notifications\User\Voucher\RedeemVoucherMail;

class MyVoucherController extends Controller
{
    public function index() {
        $page_title = "My Voucher"; 
        $user_wallets = UserWallet::auth()->get();
        $charges = TransactionSetting::where("slug",GlobalConst::VOUCHER)->first();
        $userId = auth()->user()->id; 
        $transactions = Voucher::with('transaction')
        ->where(function ($query) {
            $query->where('user_id', auth()->user()->id)
                ->orWhereHas('transaction', function ($subquery) {
                    $subquery->where('receiver_id', auth()->user()->id);
                });
        })->latest()->take(10)->get(); 
        return view('user.sections.my-voucher.index',compact('page_title','user_wallets','charges','transactions'));
    }
    public function submit(Request $request) {
        $validated = Validator::make($request->all(),[
            'amount'           => "required|numeric|gt:0",
            'request_currency' => "required|string|exists:currencies,code", 
        ])->validate();
        if(Auth::guard(get_auth_guard())->check()){
            $user = auth()->guard(get_auth_guard())->user();
        }
        $sender_wallet = UserWallet::auth()->whereHas("currency",function($q) use ($validated) {
            $q->where("code",$validated['request_currency'])->active();
        })->active()->first();
        if(!$sender_wallet) return back()->with(['error' => ['Your wallet isn\'t available with currency ('.$validated['sender_currency'].')']]);
        // charge calculation
        $trx_charges = TransactionSetting::where("slug",GlobalConst::VOUCHER)->first();
        $percent_charge = ($validated['amount'] / 100) * $trx_charges->percent_charge ?? 0;
        $fixed_charge = $sender_wallet->currency->rate*$trx_charges->fixed_charge ?? 0;
        $total_charge = $fixed_charge + $percent_charge;
        $total_payable = $validated['amount'] + $total_charge;

        // Check transaction limit
        $sender_currency_rate = $sender_wallet->currency->rate;
        $min_amount = $trx_charges->min_limit * $sender_currency_rate;
        $max_amount = $trx_charges->max_limit * $sender_currency_rate;
        if($validated['amount'] < $min_amount || $validated['amount'] > $max_amount) {
            return back()->with(['error' => ['Please follow the transaction limit. (Min '.$min_amount . ' ' . $sender_wallet->currency->code .' - Max '.$max_amount. ' ' . $sender_wallet->currency->code . ')']]);
        }
        if($total_payable > $sender_wallet->balance){
            return back()->with(['error' => [__('Insuficiant Balance')]]);
        }
        $identifier = generate_unique_string("vouchers","code",16);
        
        DB::beginTransaction();
        try {
            Voucher::create([ 
                'user_id'          => auth()->user()->id,
                'code'       => $identifier,
                'request_amount'   => $validated['amount'],
                'request_currency' => $validated['request_currency'],
                'exchange_rate'    => $sender_currency_rate,
                'percent_charge'   => $percent_charge,
                'fixed_charge'     => $fixed_charge,
                'total_charge'     => $total_charge,
                'total_payable'    => $total_payable,
                'status'           => GlobalConst::PENDING,
            ]);
            $sender_wallet->balance -=  $total_payable;
            $sender_wallet->save();
            DB::commit();

            $notification_content = [
                'title'   => "Redeem Code Generated",
                'message' => "You have Generated a Redeem Code for ".get_amount($validated['amount'],$validated['request_currency'],2),
                'time'    => Carbon::now()->diffForHumans(),
                'image'   => files_asset_path('profile-default'),
            ]; 
            UserNotification::create([
                'type'    => NotificationConst::VOUCHER_GENERATE,
                'user_id' => $user->id,
                'message' => $notification_content,
            ]);
            
        } catch (Exception $e) {
            DB::rollBack();  
            return redirect()->back()->withInput()->with(['error' => [__('Something went wrong! Please try again')]]);
        } 
        return redirect()->route('user.my-voucher.redeem.code',$identifier)->with(['success' => [__('Voucher Code Generated Successfully')]]);
    }
    public function redeemCode($identifier) {
        $page_title = "Share Redeem Code";
        $voucherData = Voucher::where('code',$identifier)->first();
        return view('user.sections.my-voucher.redeem-code-share',compact('page_title','voucherData'));
    }
    public function voucherRedeemSubmit(Request $request) {
        $validated = Validator::make($request->all(),[ 
            'code' => "required|string",
        ])->validate();
        if(Auth::guard(get_auth_guard())->check()){
            $user = auth()->guard(get_auth_guard())->user();
        }
        $basic_setting = BasicSettings::first();
        $voucherData = Voucher::where('code',$validated['code'])->where('status',GlobalConst::PENDING)->first();
        if($voucherData == null) return redirect()->back()->with(['error' => ['Invalid request']]);
        if ($voucherData->user_id == auth()->user()->id) {
            return redirect()->back()->with(['error' => [__('Something went wrong! Please try again')]]);
        }
        $receiver_wallet = UserWallet::auth()->whereHas("currency",function($q) use ($voucherData) {
            $q->where("code",$voucherData->request_currency)->active();
        })->active()->first();

        $sender_wallet = UserWallet::where("user_id",$voucherData->user_id)->whereHas("currency",function($q) use ($voucherData){
            $q->where("code",$voucherData->request_currency);
        })->first(); 

        $trx_id = 'RD'.getTrxNum();
        DB::beginTransaction();
        try {
            $id = DB::table("transactions")->insertGetId([
                'type'              => PaymentGatewayConst::REDEEMVOUCHER,
                'attribute'         => PaymentGatewayConst::VOUCHER,
                'trx_id'            => $trx_id,
                'user_type'         => GlobalConst::USER,
                'user_id'           => $sender_wallet->user_id,
                'wallet_id'         => $sender_wallet->id,
                'voucher_id'        => $voucherData->id,
                'request_amount'    => $voucherData->request_amount,
                'request_currency'  => $voucherData->request_currency,
                'exchange_rate'     => 1,
                'available_balance' => $sender_wallet->balance,
                'percent_charge'    => $voucherData->percent_charge,
                'fixed_charge'      => $voucherData->fixed_charge,
                'total_charge'      => $voucherData->total_charge,
                'total_payable'     => $voucherData->total_payable,
                'receive_amount'    => $voucherData->request_amount,
                'payment_currency'  => $voucherData->request_currency,
                'receiver_type'     => GlobalConst::USER,
                'receiver_id'       => auth()->user()->id,
                'details'           => json_encode(['voucherData' => $voucherData]),
                'status'            => PaymentGatewayConst::STATUSSUCCESS,
                'created_at'        => now(),
            ]);
            $receiver_wallet->balance += $voucherData->request_amount;
            $receiver_wallet->save();
            $voucherData->status = GlobalConst::APPROVED;
            $voucherData->save();
            DB::commit();

            $notification_content = [
                'title'   => "Voucher Received",
                'message' => "Voucher Received Successfully. Amount is  ".get_amount($voucherData->request_amount,$voucherData->request_currency,2),
                'time'    => Carbon::now()->diffForHumans(),
                'image'   => files_asset_path('profile-default'),
            ]; 
            UserNotification::create([
                'type'    => "Voucher Received",
                'user_id' => $user->id,
                'message' => $notification_content,
            ]);
            $notification_content = [
                'title'   => "Voucher Received",
                'message' => "Voucher Received Successfully. Amount is  ".get_amount($voucherData->request_amount,$voucherData->request_currency,2),
                'time'    => Carbon::now()->diffForHumans(),
                'image'   => files_asset_path('profile-default'),
            ]; 
            UserNotification::create([
                'type'    => "Voucher Received",
                'user_id' => $voucherData->user_id,
                'message' => $notification_content,
            ]); 
            AdminNotification::create([
                'type'      => "Voucher Received",
                'admin_id'  => 1,
                'message'   => $notification_content,
            ]);
            $this->insertDevice($voucherData,$id);
            $voucherOwner = User::findOrFail($voucherData->user_id);

            //Push Notifications
            try{ 
                if( $basic_setting->email_notification == true){
                    $user->notify(new RedeemVoucherMail($user,$voucherData,$trx_id));
                    $voucherOwner->notify(new RedeemVoucherMail($voucherOwner,$voucherData,$trx_id));
                }
                if($basic_setting->push_notification == true){
                    event(new NotificationEvent($notification_content,$user));
                    send_push_notification(["user-".$user->id],[
                        'title'     => $notification_content['title'],
                        'body'      => $notification_content['message'],
                        'icon'      => $notification_content['image'],
                    ]);
                }
            }catch(Exception $e) {
                
            } 
        } catch (Exception $e) { 
            DB::rollBack();  
            return redirect()->back()->withInput()->with(['error' => [__('Something went wrong! Please try again')]]);
        } 
        return redirect()->route('user.my-voucher.index')->with(['success' => [__('Redeem Code Successfully Applied')]]);
    }
    public function insertDevice($data,$id) {
        $client_ip = request()->ip() ?? false;
        $location = geoip()->getLocation($client_ip);
        $agent = new Agent(); 
        $mac = "";
    
        DB::beginTransaction();
        try{
            DB::table("transaction_devices")->insert([
                'transaction_id'=> $id,
                'ip'            => $client_ip,
                'mac'           => $mac,
                'city'          => $location['city'] ?? "",
                'country'       => $location['country'] ?? "",
                'longitude'     => $location['lon'] ?? "",
                'latitude'      => $location['lat'] ?? "",
                'timezone'      => $location['timezone'] ?? "",
                'browser'       => $agent->browser() ?? "",
                'os'            => $agent->platform() ?? "",
            ]);
            DB::commit();
        }catch(Exception $e) {
            DB::rollBack();
            throw new Exception($e->getMessage());
        }
    }
    //voucher cancel 
    public function cancel($identifier) {
        $voucherData = Voucher::where('code',$identifier)->first();
        if($voucherData == null) return redirect()->back()->with(['error' => [__('Voucher not found')]]);
        $userWallet = UserWallet::where('currency_id',$voucherData->currency->id)->where('user_id',$voucherData->user_id)->first();
        
        DB::beginTransaction();
        try{
            $voucherData->status = PaymentGatewayConst::STATUSREJECTED;
            $userWallet->balance = $userWallet->balance+$voucherData->request_amount;
            $userWallet->save();
            $voucherData->save();
            DB::commit();
        }catch(Exception $e) {
            DB::rollBack();
            throw new Exception($e->getMessage());
        }  
        return redirect()->back()->with(['success' => [__('Voucher Canceled Successfully')]]);
    }
}
