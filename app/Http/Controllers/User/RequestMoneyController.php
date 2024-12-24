<?php

namespace App\Http\Controllers\User;

use Exception;
use App\Models\UserWallet;
use Jenssegers\Agent\Agent;
use App\Models\RequestMoney;
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

class RequestMoneyController extends Controller
{
    public function index() {
        $page_title = "Request Money";
        $user_wallets = UserWallet::auth()->get();
        $charges = TransactionSetting::where("slug",GlobalConst::REQUEST)->first();
        $userId = auth()->user()->id; 
        $transactions = RequestMoney::with('transaction')
        ->where(function ($query) {
            $query->where('user_id', auth()->user()->id)
                ->orWhereHas('transaction', function ($subquery) {
                    $subquery->where('user_id', auth()->user()->id);
                });
        })->latest()->take(10)->get();  
        return view('user.sections.request-money.index',compact('page_title','user_wallets','charges','transactions'));
    }
    public function submit(Request $request) {
        $basic_setting = BasicSettings::first();
        $validated = Validator::make($request->all(),[
            'amount'           => "required|numeric|gt:0",
            'request_currency' => "required|string|exists:currencies,code",
            'remark'           => "nullable|string",
        ])->validate();
        if(Auth::guard(get_auth_guard())->check()){
            $user = auth()->guard(get_auth_guard())->user();
        }
        $sender_wallet = UserWallet::auth()->whereHas("currency",function($q) use ($validated) {
            $q->where("code",$validated['request_currency'])->active();
        })->active()->first();
        if(!$sender_wallet) return back()->with(['error' => ['Your wallet isn\'t available with currency ('.$validated['sender_currency'].')']]);
        //charge calculation
        $trx_charges = TransactionSetting::where("slug",GlobalConst::REQUEST)->first();
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
        $identifier = generate_unique_string("request_money","identifier",16);
        $generateLink = url('/').'/user/request-money/payment/'.$identifier;
        
        DB::beginTransaction();
        try {
            RequestMoney::create([ 
                'user_id'          => auth()->user()->id,
                'identifier'       => $identifier,
                'request_amount'   => $validated['amount'],
                'request_currency' => $validated['request_currency'],
                'exchange_rate'    => $sender_currency_rate,
                'percent_charge'   => $percent_charge,
                'fixed_charge'     => $fixed_charge,
                'total_charge'     => $total_charge,
                'total_payable'    => $total_payable,
                'link'             =>$generateLink,
                'remark'           => $validated['remark'],
                'status'           => GlobalConst::PENDING,
            ]);
            DB::commit();

            $notification_content = [
                'title'   => "Request Money",
                'message' => "Request Money Link Generated for ".get_amount($validated['amount'],$validated['request_currency'],2),
                'time'    => Carbon::now()->diffForHumans(),
                'image'   => files_asset_path('profile-default'),
            ]; 
            UserNotification::create([
                'type'    => NotificationConst::REQUEST_MONEY,
                'user_id' => $user->id,
                'message' => $notification_content,
            ]);
            //Push Notifications
            try{ 
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
            // return $e->getMessage();
            return redirect()->back()->withInput()->with(['error' => [__('Something went wrong! Please try again')]]);
        } 
        return redirect()->route('user.request.money.share',$identifier);
    }
    public function share($identifier) {
        $page_title = "Share Request Money";
        $requestMoneyData = RequestMoney::where('identifier',$identifier)->first();
        return view('user.sections.request-money.share-link',compact('page_title','requestMoneyData'));
    }

    //request money payment
    public function requestMoneyPaymentPreview($identifier) {
        $page_title = "Request Money Payment";
        $requestMoneyData = RequestMoney::where('identifier',$identifier)->where('status',GlobalConst::PENDING)->first(); 
        if($requestMoneyData == null) return redirect()->route('user.request.money.index')->with(['error' => ['Invalid request']]);
        if ($requestMoneyData->user_id == auth()->user()->id) {
            return redirect()->route('user.request.money.index')->with(['error' => [__('Something went wrong! Please try again')]]);
        }
        return view('user.sections.request-money.payment-preview',compact('page_title','requestMoneyData')); 
    }
    public function requestMoneyPaymentConfirm(Request $request) {
        $validated = Validator::make($request->all(),[ 
            'identifier' => "required|string",
        ])->validate();
        if(Auth::guard(get_auth_guard())->check()){
            $user = auth()->guard(get_auth_guard())->user();
        }
        $requestMoneyData = RequestMoney::where('identifier',$validated['identifier'])->where('status',GlobalConst::PENDING)->first();
        if($requestMoneyData == null) return redirect()->route('user.request.money.index')->with(['error' => ['Invalid request']]);

        $sender_wallet = UserWallet::auth()->whereHas("currency",function($q) use ($requestMoneyData) {
            $q->where("code",$requestMoneyData->request_currency)->active();
        })->active()->first();
        if($requestMoneyData->total_payable > $sender_wallet->balance){
            return back()->with(['error' => ['Insuficiant Balance']]);
        }
        $receiver_wallet = UserWallet::where("user_id",$requestMoneyData->user_id)->whereHas("currency",function($q) use ($requestMoneyData){
            $q->receiver()->where("code",$requestMoneyData->request_currency);
        })->first();

        $trx_id = 'RQ'.getTrxNum(); 
        DB::beginTransaction();
        try {
            $id = DB::table("transactions")->insertGetId([
                'type'              => PaymentGatewayConst::REQUESTMONEY,
                'attribute'         => PaymentGatewayConst::REQUEST,
                'trx_id'            => $trx_id,
                'user_type'         => GlobalConst::USER,
                'user_id'           => auth()->user()->id,
                'wallet_id'         => $sender_wallet->id,
                'request_money_id'  => $requestMoneyData->id,
                'request_amount'    => $requestMoneyData->request_amount,
                'request_currency'  => $requestMoneyData->request_currency,
                'exchange_rate'     => 1,
                'available_balance' => $sender_wallet->balance,
                'percent_charge'    => $requestMoneyData->percent_charge,
                'fixed_charge'      => $requestMoneyData->fixed_charge,
                'total_charge'      => $requestMoneyData->total_charge,
                'total_payable'     => $requestMoneyData->total_payable,
                'receive_amount'    => $requestMoneyData->request_amount,
                'payment_currency'  => $requestMoneyData->request_currency,
                'receiver_type'     => GlobalConst::USER,
                'receiver_id'       => $requestMoneyData->user_id,
                'details'           => json_encode(['requestMoneyData' => $requestMoneyData]),
                'remark'            => $requestMoneyData->remark,
                'status'            => PaymentGatewayConst::STATUSSUCCESS,
                'created_at'        => now(),
            ]);
            $sender_wallet->balance -=  $requestMoneyData->total_payable;
            $sender_wallet->save();
            $receiver_wallet->balance += $requestMoneyData->request_amount;
            $receiver_wallet->save();
            $requestMoneyData->status = GlobalConst::APPROVED;
            $requestMoneyData->save();
            DB::commit();

            $notification_content = [
                'title'   => "Request Money Payment",
                'message' => "Request Money Payment Successful. Amount is  ".get_amount($requestMoneyData->request_amount,$requestMoneyData->request_currency,2),
                'time'    => Carbon::now()->diffForHumans(),
                'image'   => files_asset_path('profile-default'),
            ]; 
            UserNotification::create([
                'type'    => NotificationConst::REQUEST_MONEY,
                'user_id' => $user->id,
                'message' => $notification_content,
            ]);
            AdminNotification::create([
                'type'      => "Request Money Payment Received",
                'admin_id'  => 1,
                'message'   => $notification_content,
            ]);
            $notification_content = [
                'title'   => "Request Money Payment Received",
                'message' => "You have received the payment Successful. Amount is  ".get_amount($requestMoneyData->request_amount,$requestMoneyData->request_currency,2),
                'time'    => Carbon::now()->diffForHumans(),
                'image'   => files_asset_path('profile-default'),
            ]; 
            UserNotification::create([
                'type'    => NotificationConst::REQUEST_MONEY,
                'user_id' => $requestMoneyData->user_id,
                'message' => $notification_content,
            ]);

            $this->insertDevice($requestMoneyData,$id);
        } catch (Exception $e) { 
            DB::rollBack(); 
            return $e->getMessage();
            return redirect()->back()->withInput()->with(['error' => [__('Something went wrong! Please try again')]]);
        } 
        return redirect()->route('user.request.money.payment.confirm.preview');
    }
    public function paymentConfirmedPreview() {
        $page_title = "Payment Completed";
        return view('user.sections.request-money.payment-confirmed',compact('page_title'));
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
}
