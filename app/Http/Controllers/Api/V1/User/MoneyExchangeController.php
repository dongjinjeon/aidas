<?php

namespace App\Http\Controllers\Api\V1\User;
 
use Exception;
use App\Models\UserWallet; 
use Illuminate\Http\Request;
use App\Constants\GlobalConst;
use App\Http\Helpers\Response;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Constants\PaymentGatewayConst;
use App\Models\Admin\TransactionSetting;
use Illuminate\Support\Facades\Validator;

class MoneyExchangeController extends Controller
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
        $charges = TransactionSetting::where("slug",GlobalConst::EXCHANGE)->first();
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
            'userWallet'    => $userWallet,
            'charges'       => $chargesData, 
            'base_url'      => url('/'),
        ];
        return Response::success([__('Money Exchange Information')], $data);
    }
    public function moneyExchangeSubmit(Request $request) {
        $validator = Validator::make($request->all(),[
            'exchange_from_amount'   => 'required|numeric|gt:0',
            'exchange_from_currency' => 'required|string|exists:currencies,code',
            'exchange_to_amount'     => 'nullable|numeric|gt:0',
            'exchange_to_currency'   => 'required|string|exists:currencies,code',
        ]);
        if($validator->fails()) return Response::error($validator->errors()->all(),[]);
        $validated = $validator->validate();
        $user_from_wallet = UserWallet::where('user_id', auth()->user()->id)->whereHas("currency",function($q) use ($validated){
            $q->where("code",$validated['exchange_from_currency']);
        })->first(); 
        if(!$user_from_wallet) return Response::error(['From wallet('.$validated['exchange_from_currency'].') doesn\'t exists'],[],400); 
        $user_to_wallet = UserWallet::where('user_id', auth()->user()->id)->whereHas("currency",function($q) use ($validated){
            $q->where("code",$validated['exchange_to_currency']);
        })->first(); 
        if(!$user_to_wallet) return Response::error(['To exchange wallet('.$validated['exchange_to_currency'].') doesn\'t exists'],[],400);
        $charges = TransactionSetting::where("slug",GlobalConst::EXCHANGE)->first();
        if(!$charges) return Response::error([__('Exchange money isn\'t available right now')],[],400);
        if($user_from_wallet->id === $user_to_wallet->id) { 
            return Response::error([__('Can\'t exchange money with same currency')],[],400);  
        } 
        $chargeCalculate = $this->exchangeChargeCalc($validated['exchange_from_amount'],$charges,$user_from_wallet,$user_to_wallet); 
        // Check transaction limit 
        $sender_currency_rate = $user_from_wallet->currency->rate;
        $min_amount           = $charges->min_limit * $sender_currency_rate;
        $max_amount           = $charges->max_limit * $sender_currency_rate;
        if($validated['exchange_from_amount'] < $min_amount || $validated['exchange_from_amount'] > $max_amount)  return Response::error(['Please follow the transaction limit'],[],400);
        if($chargeCalculate->payable > $chargeCalculate->from_wallet_balance)  return Response::error(['You don\'t have sufficient balance'],[],400);
        if($validated['exchange_from_amount'] < $min_amount || $validated['exchange_from_amount'] > $max_amount) { 
            return Response::error(['Please follow the transaction limit. (Min '.$min_amount . ' ' . $user_from_wallet->currency->code .' - Max '.$max_amount. ' ' . $user_from_wallet->currency->code . ')'],[],400);
        }
        // Transaction Start
        DB::beginTransaction();
        try{
            $id = DB::table("transactions")->insertGetId([
                'type'                          => PaymentGatewayConst::TYPEMONEYEXCHANGE,
                'trx_id'                        => 'ME'.getTrxNum(),
                'user_type'                     => GlobalConst::USER,
                'user_id'                       => auth()->user()->id,
                'wallet_id'                     => $user_from_wallet->id,
                'request_amount'                => $chargeCalculate->request_amount,
                'request_currency'              => $validated['exchange_from_currency'],
                'exchange_rate'                 => $chargeCalculate->exchange_rate,
                'available_balance'             => $user_from_wallet->balance - $chargeCalculate->payable,
                'percent_charge'                => $chargeCalculate->percent_charge,
                'fixed_charge'                  => $chargeCalculate->fixed_charge,
                'total_charge'                  => $chargeCalculate->total_charge,
                'total_payable'                 => $chargeCalculate->payable,
                'receive_amount'                => $chargeCalculate->exchange_amount,
                'payment_currency'              => $validated['exchange_to_currency'],
                'receiver_type'                 => GlobalConst::USER,
                'receiver_id'                   => auth()->user()->id,
                'details'                       => json_encode(['charges' => $chargeCalculate]),
                'status'                        => PaymentGatewayConst::STATUSSUCCESS,
                'created_at'                    => now(),
            ]);
            $user_from_wallet->balance -= $chargeCalculate->payable;
            $user_from_wallet->save();
            $user_to_wallet->balance += $chargeCalculate->exchange_amount;
            $user_to_wallet->save();
            DB::commit();
        }catch(Exception $e) {
            DB::rollBack();
            return Response::error([__('Something went wrong! Please try again')],[],400); 
        } 
        return Response::success([__('Money exchange successful, Please Go Back Your App')], []);
    }
    public function exchangeChargeCalc($enter_amount,$charges,$from_wallet,$to_wallet) {
        $exchange_rate         = $to_wallet->currency->rate / $from_wallet->currency->rate;
        $data['exchange_rate'] = $exchange_rate;
        //request amount
        $data['request_amount']    = $enter_amount;
        $data['request_currency']  = $from_wallet->currency->code;
        $data['exchange_currency'] = $to_wallet->currency->code;
        //exchange charge calculate
        $percent_charge         = $charges->percent_charge ?? 0;
        $data['percent_charge'] = ($enter_amount / 100) * $percent_charge;
        $fixed_charge           = $charges->fixed_charge ?? 0;
        $data['fixed_charge']   = $from_wallet->currency->rate * $fixed_charge;
        $data['total_charge']   = $data['percent_charge'] + $data['fixed_charge'];
        //user wallet check
        $data['from_wallet_balance'] = $from_wallet->balance;
        $data['to_wallet_balance']   = $to_wallet->balance;
        //exchange amount
        $data['payable']                 = $enter_amount + $data['total_charge'];
        $data['exchange_amount']         = $enter_amount * $data['exchange_rate'];
        $data['default_currency_amount'] = ($enter_amount / $from_wallet->currency->rate);
        $data['sender_currency_rate']    = $from_wallet->currency->rate;

        return (object) $data;
    }
}
