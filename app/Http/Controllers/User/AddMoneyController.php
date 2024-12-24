<?php

namespace App\Http\Controllers\User;

use Exception;
use App\Models\UserWallet;
use App\Models\Transaction;
use Illuminate\Http\Request;
use App\Models\TemporaryData;
use App\Constants\GlobalConst;
use App\Models\Admin\Currency;
use App\Models\UserNotification;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Models\Admin\BasicSettings; 
use App\Models\Admin\PaymentGateway;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\RedirectResponse;
use App\Constants\PaymentGatewayConst;
use App\Events\User\NotificationEvent;
use App\Models\Admin\CryptoTransaction;
use App\Traits\ControlDynamicInputFields;
use Illuminate\Support\Facades\Validator;
use App\Models\Admin\PaymentGatewayCurrency;
use App\Notifications\User\AddMoney\ManualMail;
use App\Http\Helpers\PaymentGateway as PaymentGatewayHelper;


class AddMoneyController extends Controller
{
    use ControlDynamicInputFields;

    public function index() { 
        $page_title = __("Add Money");
        $user_wallets = UserWallet::auth()->get();
        $user_currencies = Currency::whereIn('id',$user_wallets->pluck('currency_id')->toArray())->get(); 
        $payment_gateways = PaymentGateway::addMoney()->active()->with('currencies')->has("currencies")->get();
        $transactions = Transaction::with('gateway_currency')->addMoney()->where('user_id',auth()->user()->id)->latest()->take(10)->get(); 

        return view('user.sections.add-money.index',compact('page_title','user_wallets','payment_gateways','transactions'));
    }

    public function submit(Request $request, PaymentGatewayCurrency $gateway_currency) {
        $validated = Validator::make($request->all(),[
            'amount'            => 'required|numeric|gt:0',
            'request_currency'  => 'required|string',
            'gateway_currency'  => 'required|string|exists:'.$gateway_currency->getTable().',alias',
        ])->validate();
        $request->merge(['currency' => $validated['gateway_currency']]);
        $request->merge(['wallet_currency' => $validated['request_currency']]);

        try{
            $instance = PaymentGatewayHelper::init($request->all())->type(PaymentGatewayConst::TYPEADDMONEY)->setProjectCurrency(PaymentGatewayConst::PROJECT_CURRENCY_MULTIPLE)->gateway()->render();
           
            if($instance instanceof RedirectResponse === false && isset($instance['gateway_type']) && $instance['gateway_type'] == PaymentGatewayConst::MANUAL) {
                $manual_handler = $instance['distribute'];
                return $this->$manual_handler($instance);
            } 
        }catch(Exception $e) {
            return back()->with(['error' => [$e->getMessage()]]);
        }
        return $instance;
    }

    public function success(Request $request, $gateway){
        try{
            $token = PaymentGatewayHelper::getToken($request->all(),$gateway);
            $temp_data = TemporaryData::where("type",PaymentGatewayConst::TYPEADDMONEY)->where("identifier",$token)->first();
            
            if(Transaction::where('callback_ref', $token)->exists()) {
                if(!$temp_data) return redirect()->route('user.add.money.index')->with(['success' => ['Transaction request sended successfully!']]);;
            }else {
                if(!$temp_data) return redirect()->route('user.add.money.index')->with(['error' => ['Transaction failed. Record didn\'t saved properly. Please try again.']]);
            }

            $update_temp_data = json_decode(json_encode($temp_data->data),true);
            $update_temp_data['callback_data']  = $request->all();
            $temp_data->update([
                'data'  => $update_temp_data,
            ]);
            $temp_data = $temp_data->toArray();
            $instance = PaymentGatewayHelper::init($temp_data)->type(PaymentGatewayConst::TYPEADDMONEY)->setProjectCurrency(PaymentGatewayConst::PROJECT_CURRENCY_MULTIPLE)->responseReceive();
            if($instance instanceof RedirectResponse) return $instance;
        }catch(Exception $e) { 
            return back()->with(['error' => [$e->getMessage()]]);
        }
        return redirect()->route("user.add.money.index")->with(['success' => [__('Successfully added money')]]);
    }

    public function cancel(Request $request, $gateway) { 
        if($request->has('token')) {
            $identifier = $request->token;
            if($temp_data = TemporaryData::where('identifier', $identifier)->first()) {
                $temp_data->delete();
            }
        }

        return redirect()->route('user.add.money.index');
    }
    public function postSuccess(Request $request, $gateway)
    {  
        try{
            $token = PaymentGatewayHelper::getToken($request->all(),$gateway);
            $temp_data = TemporaryData::where("type",PaymentGatewayConst::TYPEADDMONEY)->where("identifier",$token)->first();
            Auth::guard($temp_data->data->creator_guard)->loginUsingId($temp_data->data->creator_id);
        }catch(Exception $e) {
            return redirect()->route('user.add.money.index')->with(['error' => [__("Something went wrong! Please try again")]]);
        }

        return $this->success($request, $gateway);
    }

    public function postCancel(Request $request, $gateway)
    {
        try{
            $token = PaymentGatewayHelper::getToken($request->all(),$gateway);
            $temp_data = TemporaryData::where("type",PaymentGatewayConst::TYPEADDMONEY)->where("identifier",$token)->first();
            Auth::guard($temp_data->data->creator_guard)->loginUsingId($temp_data->data->creator_id);
        }catch(Exception $e) {
            return redirect()->route('user.add.money.index')->with(['error' => [__("Something went wrong! Please try again")]]);
        }

        return $this->cancel($request, $gateway);
    }
    public function callback(Request $request,$gateway) { 
        $callback_token = $request->get('token');
        $callback_data = $request->all();

        try{
            PaymentGatewayHelper::init([])->type(PaymentGatewayConst::TYPEADDMONEY)->setProjectCurrency(PaymentGatewayConst::PROJECT_CURRENCY_MULTIPLE)->handleCallback($callback_token,$callback_data,$gateway);
        }catch(Exception $e) {
            // handle Error
            logger($e);
        }
    }

    public function handleManualPayment($payment_info) { 
        // Insert temp data
        $data = [
            'type'          => PaymentGatewayConst::TYPEADDMONEY,
            'identifier'    => generate_unique_string("temporary_datas","identifier",16),
            'data'          => [
                'gateway_currency_id'    => $payment_info['currency']->id,
                'amount'                 => $payment_info['amount'],
                'wallet_id'              => $payment_info['wallet']->id,
            ],
        ];

        try{
            TemporaryData::create($data);
        }catch(Exception $e) {
            return redirect()->route('user.add.money.index')->with(['error' => [__('Failed to save data. Please try again')]]);
        }
        return redirect()->route('user.add.money.manual.form',$data['identifier']);
    }

    public function showManualForm($token) {
        $tempData = TemporaryData::search($token)->first();
        if(!$tempData || $tempData->data == null || !isset($tempData->data->gateway_currency_id)) return redirect()->route('user.add.money.index')->with(['error' => ['Invalid request']]);
        $gateway_currency = PaymentGatewayCurrency::find($tempData->data->gateway_currency_id);
        if(!$gateway_currency || !$gateway_currency->gateway->isManual()) return redirect()->route('user.add.money.index')->with(['error' => ['Selected gateway is invalid']]);
        $gateway = $gateway_currency->gateway;
        if(!$gateway->input_fields || !is_array($gateway->input_fields)) return redirect()->route('user.add.money.index')->with(['error' => ['This payment gateway is under constructions. Please try with another payment gateway']]);
        $amount = $tempData->data->amount;  
        $request_currency = Currency::where('code',$amount->sender_cur_code)->first();
        $digitShow = 2; 
        $page_title = "Payment Instructions";
        return view('user.sections.add-money.manual.preview',compact("gateway","page_title","token","amount","digitShow"));
    }

    public function manualSubmit(Request $request,$token) {
        $basic_setting = BasicSettings::first();
        $request->merge(['identifier' => $token]);
        $tempDataValidate = Validator::make($request->all(),[
            'identifier'        => "required|string|exists:temporary_datas",
        ])->validate();

        $tempData = TemporaryData::search($tempDataValidate['identifier'])->first();
        if(!$tempData || $tempData->data == null || !isset($tempData->data->gateway_currency_id)) return redirect()->route('user.add.money.index')->with(['error' => ['Invalid request']]);
        $gateway_currency = PaymentGatewayCurrency::find($tempData->data->gateway_currency_id);
        if(!$gateway_currency || !$gateway_currency->gateway->isManual()) return redirect()->route('user.add.money.index')->with(['error' => ['Selected gateway is invalid']]);
        $gateway = $gateway_currency->gateway;
        $amount = $tempData->data->amount ?? null;
        if(!$amount) return redirect()->route('user.add.money.index')->with(['error' => [__('Transaction Failed. Failed to save information. Please try again')]]);
        $wallet = UserWallet::find($tempData->data->wallet_id ?? null);
        if(!$wallet) return redirect()->route('user.add.money.index')->with(['error' => [__('Your wallet is invalid!')]]);

        $this->file_store_location = "transaction";
        $dy_validation_rules = $this->generateValidationRules($gateway->input_fields);

        $validated = Validator::make($request->all(),$dy_validation_rules)->validate();
        $get_values = $this->placeValueWithFields($gateway->input_fields,$validated);
        
        // Make Transaction
        $trx_id = 'AM'.getTrxNum();
        DB::beginTransaction();
        try{
            $id = DB::table("transactions")->insertGetId([
                'type'                          => PaymentGatewayConst::TYPEADDMONEY,
                'trx_id'                        => $trx_id,
                'user_type'                     => GlobalConst::USER,
                'user_id'                       => $wallet->user->id,
                'wallet_id'                     => $wallet->id,
                'payment_gateway_currency_id'   => $gateway_currency->id,
                'request_amount'                => $amount->requested_amount,
                'request_currency'              => $wallet->currency->code,
                'exchange_rate'                 => $amount->exchange_rate,
                'percent_charge'                => $amount->percent_charge,
                'fixed_charge'                  => $amount->fixed_charge,
                'total_charge'                  => $amount->total_charge,
                'total_payable'                 => $amount->total_amount,
                'receive_amount'                => $amount->will_get,
                'receiver_type'                 => GlobalConst::USER,
                'receiver_id'                   => $wallet->user->id,
                'available_balance'             => $wallet->balance,
                'payment_currency'              => $gateway_currency->currency_code,
                'details'                       => json_encode(['input_values' => $get_values]),
                'status'                        => PaymentGatewayConst::STATUSPENDING,
                'created_at'                    => now(),
            ]);

            DB::table("temporary_datas")->where("identifier",$token)->delete();
            DB::commit();
            $notification_content = [
                'title'         => "Add Money",
                'message'       => "Your Add Money request send to admin successfully. Request amount is ".$amount->requested_amount.' '. $wallet->currency->code,
                'image'         => files_asset_path('profile-default'),
            ]; 
            UserNotification::create([
                'type'      => "Add Money",
                'user_id'  =>  $wallet->user->id,
                'message'   => $notification_content,
            ]);
            //Push Notifications
            try{
                if( $basic_setting->email_notification == true){
                    $wallet->user->notify(new ManualMail($wallet->user,$tempData->data,$gateway_currency,$trx_id));
                }
                if( $basic_setting->push_notification == true){
                    event(new NotificationEvent($notification_content,$wallet->user));
                    send_push_notification(["user-".$wallet->user->id],[
                        'title'     => $notification_content['title'],
                        'body'      => $notification_content['message'],
                        'icon'      => $notification_content['image'],
                    ]);
                }
            }catch(Exception $e) {
                
            } 
        }catch(Exception $e) {
            DB::rollBack();
            return redirect()->route('user.add.money.manual.form',$token)->with(['error' => [__('Something went wrong! Please try again')]]);
        }
        return redirect()->route('user.add.money.index')->with(['success' => [__('Transaction Success. Please wait for admin confirmation')]]);
    }

    public function cryptoPaymentAddress(Request $request, $trx_id) { 
        $page_title = "Crypto Payment Address";
        $transaction = Transaction::where('trx_id', $trx_id)->firstOrFail();

        if($transaction->gateway_currency->gateway->isCrypto() && $transaction->details?->payment_info?->receiver_address ?? false) {
            return view('user.sections.add-money.payment.crypto.address', compact(
                'transaction',
                'page_title',
            ));
        } 
        return abort(404);
    }

    public function cryptoPaymentConfirm(Request $request, $trx_id) 
    {
        $transaction = Transaction::where('trx_id',$trx_id)->where('status', PaymentGatewayConst::STATUSWAITING)->firstOrFail();

        $dy_input_fields = $transaction->details->payment_info->requirements ?? [];
        $validation_rules = $this->generateValidationRules($dy_input_fields);

        $validated = [];
        if(count($validation_rules) > 0) {
            $validated = Validator::make($request->all(), $validation_rules)->validate();
        }

        if(!isset($validated['txn_hash'])) return back()->with(['error' => [__('Transaction hash is required for verify')]]);

        $receiver_address = $transaction->details->payment_info->receiver_address ?? "";

        // check hash is valid or not
        $crypto_transaction = CryptoTransaction::where('txn_hash', $validated['txn_hash'])
                                                ->where('receiver_address', $receiver_address)
                                                ->where('asset',$transaction->gateway_currency->currency_code)
                                                ->where(function($query) {
                                                    return $query->where('transaction_type',"Native")
                                                                ->orWhere('transaction_type', "native");
                                                })
                                                ->where('status',PaymentGatewayConst::NOT_USED)
                                                ->first();
                                                
        if(!$crypto_transaction) return back()->with(['error' => [__('Transaction hash is not valid! Please input a valid hash')]]);

        if($crypto_transaction->amount >= $transaction->total_payable == false) {
            if(!$crypto_transaction) return back()->with(['error' => [__('Insufficient amount added. Please contact with system administrator')]]);
        }

        DB::beginTransaction();
        try{

            // Update user wallet balance
            DB::table($transaction->creator_wallet->getTable())
                ->where('id',$transaction->creator_wallet->id)
                ->increment('balance',$transaction->receive_amount);

            // update crypto transaction as used
            DB::table($crypto_transaction->getTable())->where('id', $crypto_transaction->id)->update([
                'status'        => PaymentGatewayConst::USED,
            ]);

            // update transaction status
            $transaction_details = json_decode(json_encode($transaction->details), true);
            $transaction_details['payment_info']['txn_hash'] = $validated['txn_hash'];

            DB::table($transaction->getTable())->where('id', $transaction->id)->update([
                'details'       => json_encode($transaction_details),
                'status'        => PaymentGatewayConst::STATUSSUCCESS,
            ]);

            DB::commit();

        }catch(Exception $e) {
            DB::rollback();
            return back()->with(['error' => [__('Something went wrong! Please try again')]]);
        }

        return back()->with(['success' => [__('Payment Confirmation Success!')]]);
    }
    public function redirectUsingHTMLForm(Request $request, $gateway)
    {
        $temp_data = TemporaryData::where('identifier', $request->token)->first();
        if(!$temp_data || $temp_data->data->action_type != PaymentGatewayConst::REDIRECT_USING_HTML_FORM) return back()->with(['error' => ['Request token is invalid!']]);
        $redirect_form_data = $temp_data->data->redirect_form_data;
        $action_url         = $temp_data->data->action_url;
        $form_method        = $temp_data->data->form_method;

        return view('payment-gateway.redirect-form', compact('redirect_form_data', 'action_url', 'form_method'));
    }

    /**
     * Redirect Users for collecting payment via Button Pay (JS Checkout)
     */
    public function redirectBtnPay(Request $request, $gateway)
    {
        try{
            return PaymentGatewayHelper::init([])->setProjectCurrency(PaymentGatewayConst::PROJECT_CURRENCY_SINGLE)->handleBtnPay($gateway, $request->all());
        }catch(Exception $e) {
            return redirect()->route('user.add.money.index')->with(['error' => [$e->getMessage()]]);
        }
    }
}
