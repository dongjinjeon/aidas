<?php

namespace App\Http\Controllers\Api\V1\User;

use Exception;
use App\Models\UserWallet;
use Jenssegers\Agent\Agent;
use App\Models\RequestMoney;
use Illuminate\Http\Request;
use App\Constants\GlobalConst;
use App\Http\Helpers\Response;
use Illuminate\Support\Carbon;
use App\Models\UserNotification;
use Illuminate\Support\Facades\DB;
use App\Constants\NotificationConst;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Constants\PaymentGatewayConst;
use App\Models\Admin\TransactionSetting;
use Illuminate\Support\Facades\Validator;

class RequestMoneyController extends Controller
{
    public function index() { 
        $user = auth()->user();
        // user wallet
        $userWallet = UserWallet::with('currency')->where('user_id',$user->id)->get()->map(function($data){
            return[
                'name'            => $data->currency->name,
                'balance'         => $data->balance,
                'currency_code'   => $data->currency->code,
                'currency_symbol' => $data->currency->symbol,
                'currency_type'   => $data->currency->type,
                'rate'            => $data->currency->rate,
                'flag'            => $data->currency->flag,
                'image_path'      => get_files_path('currency-flag'),
            ];
        });
        $charges = TransactionSetting::where("slug",GlobalConst::REQUEST)->first();
        $chargesData = [
            'title'           => $charges->title,
            'fixed_charge'    => $charges->fixed_charge,
            'percent_charge'  => $charges->percent_charge,
            'min_limit'       => $charges->min_limit,
            'max_limit'       => $charges->max_limit,
            'rate'       => get_default_currency_rate(),
            'currency_code'   => get_default_currency_code(),
            'currency_symbol' => get_default_currency_symbol(),
        ];

        $data =[
            'user_wallet' => $userWallet,
            'charges'     => $chargesData,
            'base_url'    => url('/'),
        ]; 
        return Response::success([__('Request Money info fetch successfully!')], $data);
    }
    public function submit(Request $request) {
        $validator = Validator::make($request->all(),[
            'amount'           => "required|numeric|gt:0",
            'request_currency' => "required|string|exists:currencies,code",
            'remark'           => "nullable|string",
        ]);
        if($validator->fails()) return Response::error($validator->errors()->all(),[]);
        $validated = $validator->validate();
        if(Auth::guard(get_auth_guard())->check()){
            $user = auth()->guard(get_auth_guard())->user();
        }
        $sender_wallet = UserWallet::auth()->whereHas("currency",function($q) use ($validated) {
            $q->where("code",$validated['request_currency'])->active();
        })->active()->first();
        if(!$sender_wallet) return Response::error(['Your wallet isn\'t available with currency ('.$validated['sender_currency'].')'],[],400);
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
            return Response::error(['Please follow the transaction limit. (Min '.$min_amount . ' ' . $sender_wallet->currency->code .' - Max '.$max_amount. ' ' . $sender_wallet->currency->code . ')'],[],400); 
        }
        $identifier = generate_unique_string("request_money","identifier",16);
        $generateLink = url('/').'/user/request-money/payment/'.$identifier;
        
        DB::beginTransaction();
        try {
            $requestMoneyData = RequestMoney::create([ 
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
                'message' => "Request Money Link Generated for ".get_amount($validated['amount'],$validated['request_currency'],2)." Link is: ".$generateLink,
                'time'    => Carbon::now()->diffForHumans(),
                'image'   => files_asset_path('profile-default'),
            ]; 
            UserNotification::create([
                'type'    => NotificationConst::REQUEST_MONEY,
                'user_id' => $user->id,
                'message' => $notification_content,
            ]);
            
        } catch (Exception $e) {
            DB::rollBack();  
            return Response::error([__('Something went wrong! Please try again')],[],400); 
        } 
        $data =[
            'request_amount'   => $requestMoneyData->request_amount,
            'request_currency' => $requestMoneyData->request_currency,
            'exchange_rate'    => $requestMoneyData->exchange_rate,
            'percent_charge'   => $requestMoneyData->percent_charge,
            'fixed_charge'     => $requestMoneyData->fixed_charge,
            'total_charge'     => $requestMoneyData->total_charge,
            'total_payable'    => $requestMoneyData->total_payable,
            'link'             =>$requestMoneyData->link,
            'remark'           => $requestMoneyData->remark,
            'status'           => GlobalConst::PENDING,
        ]; 
        return Response::success([__('Request Money Submitted successfully!')], $data); 
    }
    public function information(Request $request) {
        $validator = Validator::make($request->all(),[  
            'token'           => "required|string",
        ]);
        if($validator->fails()) return Response::error($validator->errors()->all(),[]);
        $validated = $validator->validate();

        $requestMoneyData = RequestMoney::where('identifier',$validated['token'])->first(); 
        if($requestMoneyData == null) return Response::error([__('Invalid request')],[],400);
        if ($requestMoneyData->user_id == auth()->user()->id) { 
            return Response::error([__('Something went wrong! Please try again')],[],400);
        }
        $request_money_info = [
            'token'            => $requestMoneyData->identifier,
            'receiver_email'            => $requestMoneyData->user->email,
            'request_amount'   => $requestMoneyData->request_amount,
            'request_currency' => $requestMoneyData->request_currency,
            'exchange_rate'    => $requestMoneyData->exchange_rate,
            'total_charge'     => $requestMoneyData->total_charge,
            'total_payable'    => $requestMoneyData->total_payable,
            'remark'           => $requestMoneyData->remark,
            'status'           => $requestMoneyData->status,
        ];
        $data =[
            'request_money_info' => $request_money_info, 
            'base_url'    => url('/'),
        ]; 
        return Response::success([__('Request Money info fetch successfully!')], $data);
        return "hello";
    }
    public function requestMoneyPaymentConfirm(Request $request) {
        $validator = Validator::make($request->all(),[  
            'token'           => "required|string",
        ]);
        if($validator->fails()) return Response::error($validator->errors()->all(),[]);
        $validated = $validator->validate();
        
        if(Auth::guard(get_auth_guard())->check()){
            $user = auth()->guard(get_auth_guard())->user();
        }
        $requestMoneyData = RequestMoney::where('identifier',$validated['token'])->where('status',GlobalConst::PENDING)->first();
        if($requestMoneyData == null) return Response::error([__('Invalid request')],[],400);

        $sender_wallet = UserWallet::auth()->whereHas("currency",function($q) use ($requestMoneyData) {
            $q->where("code",$requestMoneyData->request_currency)->active();
        })->active()->first();
        if($requestMoneyData->total_payable > $sender_wallet->balance){ 
            return Response::error([__('Insuficiant Balance')],[],400);
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
            return Response::error([__('Something went wrong! Please try again')],[],400); 
        } 
        return Response::success([__('Request Money Submitted successfully!')], []);  
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
