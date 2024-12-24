<?php

namespace App\Http\Controllers\User;

use Exception;
use App\Models\UserWallet;
use App\Models\Transaction;
use Jenssegers\Agent\Agent;
use Illuminate\Http\Request;
use App\Constants\GlobalConst;
use App\Models\Admin\Currency;
use App\Models\UserNotification;
use Illuminate\Support\Facades\DB;
use App\Models\Admin\BasicSettings;
use App\Constants\NotificationConst;
use App\Http\Controllers\Controller;
use App\Models\Admin\PaymentGateway;
use Illuminate\Support\Facades\Auth;
use App\Events\User\NotificationEvent;
use App\Constants\PaymentGatewayConst; 
use App\Models\Admin\AdminNotification;
use App\Traits\ControlDynamicInputFields;
use Illuminate\Support\Facades\Validator;
use App\Models\Admin\PaymentGatewayCurrency;
use App\Notifications\User\Withdraw\WithdrawMail;

class WithdrawMoneyController extends Controller
{
    use ControlDynamicInputFields;
    public function index() {
        $page_title =  __("Withdraw Money"); 
        $user_wallets = UserWallet::auth()->get();
        $user_currencies = Currency::whereIn('id',$user_wallets->pluck('currency_id')->toArray())->get(); 
        $payment_gateways = PaymentGateway::moneyOut()->active()->with('currencies')->has("currencies")->get();
        $transactions = Transaction::with('gateway_currency')->moneyOut()->where('user_id',auth()->user()->id)->latest()->take(10)->get(); 
        return view('user.sections.withdraw-money.index',compact('page_title','user_wallets','payment_gateways','transactions'));
    }
    public function submit(Request $request) {
        $request->validate([
            'amount' => 'required|numeric|gt:0',
            'gateway_currency' => 'required',
            'request_currency' => 'required'
        ]);
        $user = auth()->user();
        $request_currency = Currency::where('code', $request->request_currency)->first();
        $userWallet = UserWallet::where(['user_id' => $user->id, 'currency_id' => $request_currency->id, 'status' => 1])->first(); 
        $gate =PaymentGatewayCurrency::whereHas('gateway', function ($gateway) {
            $gateway->where('slug', PaymentGatewayConst::money_out_slug());
            $gateway->where('status', 1);
        })->where('alias',$request->gateway_currency)->first();
        if (!$gate) {
            return back()->with(['error' => ['Invalid Gateway']]);
        }
        $amount = $request->amount;  
        $exchange_rate =  (1/$request_currency->rate)*$gate->rate;

        $min_limit =  $gate->min_limit / $exchange_rate;
        $max_limit =  $gate->max_limit / $exchange_rate;
        if($amount < $min_limit || $amount > $max_limit) {
            return back()->with(['error' => [__('Please follow the transaction limit')]]);
        }
        //gateway charge  
        $fixedCharge = $gate->fixed_charge;
        $percent_charge =  ($amount*$exchange_rate)*($gate->percent_charge/100);
        $charge = $fixedCharge + $percent_charge; //gateway currency charge
        
        $conversion_amount = $amount * $exchange_rate;  
        $will_get = $conversion_amount -  $charge; //this amount convarted in gateway currency 
        //base_cur_charge
        $baseFixedCharge = $gate->fixed_charge *  $request_currency->rate;
        $basePercent_charge = ($amount / 100) * $gate->percent_charge;
        $base_total_charge = $baseFixedCharge + $basePercent_charge;
        // $reduceAbleTotal = $amount + $base_total_charge;
        $reduceAbleTotal = $amount;
        if( $reduceAbleTotal > $userWallet->balance){
            return back()->with(['error' => [__('Insuficiant Balance')]]);
        }
        $data['user_id']= $user->id;
        $data['gateway_name']= $gate->gateway->name;
        $data['gateway_type']= $gate->gateway->type;
        $data['wallet_id']= $userWallet->id;
        $data['trx_id']= 'MO'.getTrxNum();
        $data['amount'] =  $amount;
        $data['base_cur_charge'] = $base_total_charge;
        $data['base_cur_rate'] = $request_currency->rate;
        $data['gateway_id'] = $gate->gateway->id;
        $data['gateway_currency_id'] = $gate->id;
        $data['gateway_currency'] = strtoupper($gate->currency_code);
        $data['gateway_percent_charge'] = $percent_charge;
        $data['gateway_fixed_charge'] = $fixedCharge;
        $data['gateway_charge'] = $charge;
        $data['gateway_rate'] = $gate->rate;
        $data['conversion_amount'] = $conversion_amount;
        $data['request_currency'] = $request_currency->code;
        $data['exchange_rate'] = $exchange_rate;
        $data['will_get'] = $will_get;
        $data['payable'] = $reduceAbleTotal;
        session()->put('moneyoutData', $data);
        return redirect()->route('user.withdraw.money.preview');
   }
   //show money out privew
   public function preview() {
    $moneyOutData = (object)session()->get('moneyoutData');
    $moneyOutDataExist = session()->get('moneyoutData');
    if($moneyOutDataExist  == null){
        return redirect()->route('user.withdraw.money.index');
    }
    $request_currency = Currency::where('code', $moneyOutData->request_currency)->first(); 
    $gateway = PaymentGateway::where('id', $moneyOutData->gateway_id)->first(); 
    $page_title = "Money Out Via ".$gateway->name;
    $digitShow = $request_currency->type == "CRYPTO" ? 6 : 2 ; 
    return view('user.sections.withdraw-money.preview',compact('page_title','gateway','moneyOutData','digitShow'));  
   }
   //money out confirm
   public function confirmMoneyOut(Request $request) { 
    $basic_setting = BasicSettings::first();
    $moneyOutData = (object)session()->get('moneyoutData');
    $gateway = PaymentGateway::where('id', $moneyOutData->gateway_id)->first();
    $payment_fields = $gateway->input_fields ?? [];
    
    $this->file_store_location = "transaction";
    $validation_rules = $this->generateValidationRules($payment_fields);
    $payment_field_validate = Validator::make($request->all(),$validation_rules)->validate();
    $get_values = $this->placeValueWithFields($payment_fields,$payment_field_validate);
        try{
            //send notifications
            $user = auth()->user();
            $inserted_id = $this->insertRecordManual($moneyOutData,$gateway,$get_values);
            $this->insertDeviceManual($moneyOutData,$inserted_id);
            session()->forget('moneyoutData');
            try {
                if( $basic_setting->email_notification == true){
                    $user->notify(new WithdrawMail($user,$moneyOutData));
                }
            } catch (\Throwable $th) {
                //throw $th;
            }
          
        }catch(Exception $e) {
            return back()->with(['error' => [$e->getMessage()]]);
        } 
        
        return redirect()->route("user.withdraw.money.index")->with(['success' => [__('Money out request send to admin Successfully')]]);
   } 
   public function insertRecordManual($moneyOutData,$gateway,$get_values) {
        $basic_setting = BasicSettings::first();
        if($moneyOutData->gateway_type == "AUTOMATIC"){
            $status = 1;
        }else{
            $status = 2;
        }
        if(Auth::guard(get_auth_guard())->check()){
            $user = auth()->guard(get_auth_guard())->user();
        }
        $trx_id = $moneyOutData->trx_id ??'MO'.getTrxNum();
        $authWallet = UserWallet::where('id',$moneyOutData->wallet_id)->where('user_id',$moneyOutData->user_id)->first();
        $availableBalance = $authWallet->balance - $moneyOutData->amount;
        $user_type = auth()->user()->type == "personal" ?  GlobalConst::USER : GlobalConst::MERCHANT ;
        DB::beginTransaction();
        try{
            $id = DB::table("transactions")->insertGetId([
                'type'                          => PaymentGatewayConst::TYPEWITHDRAW,
                'trx_id'                        => $trx_id,
                'user_type'                     => $user_type,
                'user_id'                       => auth()->user()->id,
                'wallet_id'                     => $moneyOutData->wallet_id,
                'payment_gateway_currency_id'   => $moneyOutData->gateway_currency_id,
                'request_amount'                => $moneyOutData->amount,
                'request_currency'              => $moneyOutData->request_currency,
                'exchange_rate'                 => $moneyOutData->exchange_rate,
                'percent_charge'                => $moneyOutData->gateway_percent_charge,
                'fixed_charge'                  => $moneyOutData->gateway_fixed_charge,
                'total_charge'                  =>  $moneyOutData->gateway_charge,
                'total_payable'                 => $moneyOutData->will_get,
                'receive_amount'                =>$moneyOutData->will_get,
                'receiver_type'                 => $user_type,
                'receiver_id'                   => auth()->user()->id,
                'available_balance'             => $availableBalance,
                'payment_currency'              => $moneyOutData->gateway_currency,
                'details'                       => json_encode(['input_values' => $get_values]),
                'status'                        => PaymentGatewayConst::STATUSPENDING,
                'created_at'                    => now(),
            ]);
            $this->updateWalletBalanceManual($authWallet,$availableBalance); 
            DB::commit();
            //notification
            $notification_content = [
                'title'         => "Money Out",
                'message'       => "Your money out request send to admin " .$moneyOutData->amount.' '.$moneyOutData->request_currency." Successfully",
                'image'         => files_asset_path('profile-default'),
            ]; 
            UserNotification::create([
                'type'      => NotificationConst::MONEY_OUT,
                'user_id'  =>  $user->id,
                'message'   => $notification_content,
            ]);
            $notification_content = [
                'title'         => "Money Out",
                'message'       => "Money out request " .$moneyOutData->amount.' '.$moneyOutData->request_currency,
                'image'         => files_asset_path('profile-default'),
            ];
            AdminNotification::create([
                'type'      => NotificationConst::MONEY_OUT,
                'admin_id'  => 1,
                'message'   => $notification_content,
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
        }catch(Exception $e) {
            DB::rollBack(); 
            throw new Exception($e->getMessage());
        }
        return $id;
    } 
    public function updateWalletBalanceManual($authWalle,$availableBalance) {
        $authWalle->update([
            'balance'   => $availableBalance,
        ]);
    }
    public function insertDeviceManual($output,$id) {
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
