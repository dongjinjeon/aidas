<?php

namespace App\Http\Controllers\Api\V1\User;
 
use Exception;
use App\Models\UserWallet; 
use Jenssegers\Agent\Agent;
use Illuminate\Http\Request;
use App\Models\TemporaryData;
use App\Constants\GlobalConst;
use App\Http\Helpers\Response;
use App\Models\Admin\Currency;
use Illuminate\Support\Facades\DB; 
use App\Http\Controllers\Controller;
use App\Models\Admin\PaymentGateway;
use Illuminate\Support\Facades\Auth;
use App\Constants\PaymentGatewayConst;
use App\Traits\ControlDynamicInputFields;
use Illuminate\Support\Facades\Validator;
use App\Models\Admin\PaymentGatewayCurrency;

class WithdrawMoneyController extends Controller
{
    use ControlDynamicInputFields;

    public function index(){

        $user = auth()->user();
           // user wallet
           $userWallet = UserWallet::with('currency')->where('user_id',$user->id)->get()->map(function($data){
                return[
                    'name'                  => $data->currency->name,
                    'balance'               => $data->balance,
                    'currency_code'         => $data->currency->code,
                    'currency_symbol'       => $data->currency->symbol,
                    'currency_type'         => $data->currency->type,
                    'rate'                  => $data->currency->rate,
                    'flag'                  => $data->currency->flag,
                    'image_path'            => get_files_path('currency-flag'),
                ];
            });
            //add money payment gateways currencys
            $gatewayCurrencies = PaymentGatewayCurrency::whereHas('gateway', function ($gateway) {
                $gateway->where('slug', PaymentGatewayConst::money_out_slug());
                $gateway->where('status', 1);
            })->get()->map(function($data){
                return[
                    'id'                 => $data->id,
                    'payment_gateway_id' => $data->payment_gateway_id,
                    'type'               => $data->gateway->type,
                    'name'               => $data->name,
                    'alias'              => $data->alias,
                    'currency_code'      => $data->currency_code,
                    'currency_symbol'    => $data->currency_symbol,
                    'min_limit'          => getAmount($data->min_limit, 8),
                    'max_limit'          => getAmount($data->max_limit, 8),
                    'percent_charge'     => getAmount($data->percent_charge, 8),
                    'fixed_charge'       => getAmount($data->fixed_charge, 8),
                    'rate'               => getAmount($data->rate, 8),
                    'image'              => $data->image,
                    'image_path'         => get_files_path('payment-gateways'),
                    'created_at'         => $data->created_at,
                    'updated_at'         => $data->updated_at,
                ];
            }); 
            $data =[ 
                'user_wallet'                => $userWallet,
                'gateway_currencies'         => $gatewayCurrencies, 
                'base_url'                  => url('/'),
            ]; 
            return Response::success([__('Money Out Information!')], $data); 
    }
    public function submit(Request $request){
        $validator = Validator::make($request->all(), [
            'amount' => 'required|numeric|gt:0',
            'sender_currency' => 'required',
            'gateway_currency' => 'required',
        ]);
        if($validator->fails()) return Response::error($validator->errors()->all(),[]);  
        $user = auth()->user(); 

        $sender_currency = Currency::where('code', $request->sender_currency)->first();
        $userWallet = UserWallet::where(['user_id' => $user->id, 'currency_id' => $sender_currency->id, 'status' => 1])->first(); 
        $gate =PaymentGatewayCurrency::whereHas('gateway', function ($gateway) {
            $gateway->where('slug', PaymentGatewayConst::money_out_slug());
            $gateway->where('status', 1);
        })->where('alias',$request->gateway_currency)->first();
        if (!$gate) { 
            return Response::error([__('Invalid Gateway!')],[],400);
        }

        if (!$sender_currency) { 
            return Response::error([__('Currency Not Setup Yet!')],[],400);
        }
        $amount = $request->amount; 
        $exchange_rate =  (1/$sender_currency->rate)*$gate->rate;

        $min_limit =  $gate->min_limit / $exchange_rate;
        $max_limit =  $gate->max_limit / $exchange_rate;
  
        if($amount < $min_limit || $amount > $max_limit) { 
            return Response::error([__('Please follow the transaction limit')],[],400);
        }
        //gateway charge
        $fixedCharge = $gate->fixed_charge;
        $percent_charge =  ($amount*$exchange_rate)*($gate->percent_charge/100);
        $charge = $fixedCharge + $percent_charge;

        $conversion_amount = $amount * $exchange_rate; 
        $will_get = $conversion_amount -  $charge;
        //base_cur_charge
        $baseFixedCharge = $gate->fixed_charge *  $sender_currency->rate;
        $basePercent_charge = ($amount / 100) * $gate->percent_charge;
        $base_total_charge = $baseFixedCharge + $basePercent_charge;
        $reduceAbleTotal = $amount;

        if( $reduceAbleTotal > $userWallet->balance){ 
            return Response::error([__('Insufficient Balance!')],[],400); 
        }

        $insertData = [
            'user_id'                => $user->id,
            'gateway_name'           => strtolower($gate->gateway->name),
            'gateway_type'           => $gate->gateway->type,
            'wallet_id'              => $userWallet->id,
            'trx_id'                 => 'MO'.getTrxNum(),
            'amount'                 => $amount,
            'base_cur_charge'        => $base_total_charge,
            'base_cur_rate'          => $sender_currency->rate,
            'gateway_id'             => $gate->gateway->id,
            'gateway_currency_id'    => $gate->id,
            'gateway_currency'       => strtoupper($gate->currency_code),
            'gateway_percent_charge' => $percent_charge,
            'gateway_fixed_charge'   => $fixedCharge,
            'gateway_charge'         => $charge,
            'gateway_rate'           => $gate->rate,
            'exchange_rate'           => $exchange_rate,
            'conversion_amount'      => $conversion_amount,
            'sender_currency'      => $request->sender_currency,
            'will_get'               => $will_get,
            'payable'                => $reduceAbleTotal,
        ];
        $identifier = generate_unique_string("transactions","trx_id",16);
        $inserted = TemporaryData::create([
            'user_id'       => Auth::guard(get_auth_guard())->user()->id,
            'type'          => PaymentGatewayConst::TYPEWITHDRAW,
            'identifier'    => $identifier,
            'data'          => $insertData,
        ]);
        if($inserted){
            $payment_gateway = PaymentGateway::where('id',$gate->payment_gateway_id)->first();
            $payment_informations =[
                'trx' =>  $identifier,
                'gateway_currency_name' =>  $gate->name,
                'request_amount' => get_amount($request->amount,$request->sender_currency),
                'exchange_rate' => "1".' '.$request->sender_currency.' = '.get_amount($exchange_rate,$gate->currency_code,3),
                'conversion_amount' =>  get_amount($conversion_amount,$gate->currency_code),
                'total_charge' => get_amount($charge,$gate->currency_code),
                'will_get' => get_amount($will_get,$gate->currency_code),
                'payable' => get_amount($reduceAbleTotal,$request->sender_currency),

            ];
            $url = route('api.user.withdraw.money.manual.confirmed');
            $data =[
                    'payment_informations' => $payment_informations,
                    'gateway_type' => $payment_gateway->type,
                    'gateway_currency_name' => $gate->name,
                    'alias' => $gate->alias,
                    'details' => strip_tags($payment_gateway->desc) ?? null,
                    'input_fields' => $payment_gateway->input_fields??null,
                    'url' => $url??'',
                    'method' => "post",
            ]; 
            return Response::success([__('Money out Inserted Successfully')], $data);  
        }else{ 
            return Response::error([__('Something is wrong')],[],400);  
        }
    }
      //manual confirmed
      public function moneyOutManualConfirmed(Request $request){
        $validator = Validator::make($request->all(), [
            'trx'  => "required",
        ]);
        if($validator->fails()) return Response::error($validator->errors()->all(),[]); 
        $track = TemporaryData::where('identifier',$request->trx)->where('type',PaymentGatewayConst::TYPEWITHDRAW)->first();
        if(!$track){ 
            return Response::error([__('Sorry, your payment information is invalid')],[],400);   
        }
        $moneyOutData =  $track->data;
        $gateway = PaymentGateway::where('id', $moneyOutData->gateway_id)->first();
        if($gateway->type != "MANUAL"){ 
            return Response::error([__('Invalid request, it is not manual gateway request')],[],400);   

        }
        $payment_fields = $gateway->input_fields ?? [];
        $validation_rules = $this->generateValidationRules($payment_fields);
        $validator2 = Validator::make($request->all(), $validation_rules);
        if ($validator2->fails()) { 
            return Response::error([$validator2->errors()->all()],[],400);   

        }
        $validated = $validator2->validate();
        $get_values = $this->placeValueWithFields($payment_fields, $validated);
        $trx_id = $moneyOutData->trx_id ??'MO'.getTrxNum();
        $authWallet = UserWallet::where('id',$moneyOutData->wallet_id)->where('user_id',$moneyOutData->user_id)->first();
        $availableBalance = $authWallet->balance - $moneyOutData->amount;
        DB::beginTransaction();
        try{
          $id = DB::table("transactions")->insertGetId([
                'type'                          => PaymentGatewayConst::TYPEWITHDRAW,
                'trx_id'                        => $trx_id,
                'user_type'                     => GlobalConst::USER,
                'user_id'                       => auth()->user()->id,
                'wallet_id'                     => $moneyOutData->wallet_id,
                'payment_gateway_currency_id'   => $moneyOutData->gateway_currency_id,
                'request_amount'                => $moneyOutData->amount,
                'request_currency'              => $moneyOutData->sender_currency,
                'exchange_rate'                 => $moneyOutData->exchange_rate,
                'percent_charge'                => $moneyOutData->gateway_percent_charge,
                'fixed_charge'                  => $moneyOutData->gateway_fixed_charge,
                'total_charge'                  =>  $moneyOutData->gateway_charge,
                'total_payable'                 => $moneyOutData->will_get,
                'receive_amount'                =>$moneyOutData->will_get,
                'receiver_type'                 => GlobalConst::USER,
                'receiver_id'                   => auth()->user()->id,
                'available_balance'             => $availableBalance,
                'payment_currency'              => $moneyOutData->gateway_currency,
                'details'                       => json_encode(['input_values' => $get_values]),
                'status'                        => PaymentGatewayConst::STATUSPENDING,
                'created_at'                    => now(),
            ]);
            $this->updateWalletBalanceManual($authWallet,$availableBalance);
            DB::commit();  
            $this->insertDeviceManual($moneyOutData,$id);
            $track->delete();  
        }catch(Exception $e) { 
            DB::rollBack();
            dd($e->getMessage());
            return Response::error([__('Something went wrong! Please try again')],[],400);  
        }    
        return Response::success([__('Money out request send to admin successfully')], []); 
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
            return Response::error([__('Something went wrong! Please try again')],[],400);  

        }
    }
}
