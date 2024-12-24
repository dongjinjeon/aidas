<?php

namespace App\Http\Controllers\User;

use Exception;
use App\Models\User;
use App\Models\Receipient;
use App\Models\UserWallet;
use App\Models\Transaction;
use Illuminate\Http\Request;
use App\Models\TemporaryData;
use App\Constants\GlobalConst;
use App\Models\Admin\Currency;
use Illuminate\Support\Carbon;
use App\Models\UserNotification;
use Illuminate\Support\Facades\DB;
use App\Models\Admin\BasicSettings;
use App\Constants\NotificationConst;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Constants\PaymentGatewayConst;
use App\Events\User\NotificationEvent;
use App\Models\Admin\TransactionSetting;
use Illuminate\Support\Facades\Validator;
use App\Notifications\User\SendMoney\SendMoneyMail;
use App\Notifications\User\SendMoney\ReceivedMoneyMail;

class SendMoneyController extends Controller
{
    public function index() {
        $page_title = "Send Money";
        $user_wallets = UserWallet::auth()->get();
        $receiver_wallets  = Currency::active()->get();
        $charges = TransactionSetting::where("slug",GlobalConst::TRANSFER)->first();
        $userId = auth()->user()->id; 
        $transactions = Transaction::where('type', PaymentGatewayConst::TYPETRANSFERMONEY)->where(function ($query) use ($userId) {
            $query->where('user_id', $userId)
                ->orWhere('receiver_id', $userId);
        })->latest()->take(10)->get();
        return view('user.sections.send-money.index',compact('page_title','user_wallets','receiver_wallets','charges','transactions'));
    }
    public function submit(Request $request) {
        $validated = Validator::make($request->all(),[
            'sender_amount'     => "required|numeric|gt:0",
            'sender_currency'   => "required|string|exists:currencies,code",
            'receiver_amount'   => "nullable|numeric|gt:0",
            'receiver_currency' => "required|string|exists:currencies,code",
        ])->validate();

        $sender_wallet = UserWallet::auth()->whereHas("currency",function($q) use ($validated) {
            $q->where("code",$validated['sender_currency'])->active();
        })->active()->first();

        if(!$sender_wallet) return back()->with(['error' => ['Your wallet isn\'t available with currency ('.$validated['sender_currency'].')']]);

        $receiver_currency = Currency::receiver()->active()->where('code',$validated['receiver_currency'])->first();
        if(!$receiver_currency) return back()->with(['error' => ['Currency ('.$validated['receiver_currency'].') isn\'t available for receive any remittance']]);
        
        $trx_charges = TransactionSetting::where("slug",GlobalConst::TRANSFER)->first();
        $charges = $this->transferCharges($validated['sender_amount'],$trx_charges,$sender_wallet,$receiver_currency);
        
        

        // Check transaction limit
        $sender_currency_rate = $sender_wallet->currency->rate;
        $min_amount = $trx_charges->min_limit * $sender_currency_rate;
        $max_amount = $trx_charges->max_limit * $sender_currency_rate;
        if($charges['sender_amount'] < $min_amount || $charges['sender_amount'] > $max_amount) {
            return back()->with(['error' => ['Please follow the transaction limit. (Min '.$min_amount . ' ' . $sender_wallet->currency->code .' - Max '.$max_amount. ' ' . $sender_wallet->currency->code . ')']]);
        }

        if($charges['payable'] > $sender_wallet->balance) return back()->with(['error' => [__('Your wallet balance is insufficient')]]);

        $identifier = generate_unique_string("transactions","trx_id",16);
        $data = [
            'requestData' => $validated,
            'sender_wallet' => $sender_wallet,
            'charges' => $charges,
        ];
        DB::beginTransaction();
        try{
            TemporaryData::create([
                'type'       => "transfer",
                'identifier' => $identifier,
                'data'       => $data,
            ]);
            DB::commit();
        }catch(Exception $e) {
            DB::rollBack();
            return redirect()->back()->withInput()->with(['error' => [__('Something went wrong! Please try again')]]);
        }
        
        return redirect()->route('user.send.money.select.recipient',$identifier);
    } 
    //chatge calcualtion
    public function transferCharges($sender_amount,$charges,$sender_wallet,$receiver_currency) {
        $exchange_rate = $receiver_currency->rate / $sender_wallet->currency->rate;

        $data['exchange_rate']              = $exchange_rate;
        $data['sender_amount']              = $sender_amount;
        $data['sender_currency']            = $sender_wallet->currency->code;
        $data['receiver_amount']            = $sender_amount * $exchange_rate;
        $data['receiver_currency']          = $receiver_currency->code;
        $data['percent_charge']             = ($sender_amount / 100) * $charges->percent_charge ?? 0;
        $data['fixed_charge']               = $sender_wallet->currency->rate * $charges->fixed_charge ?? 0;
        $data['total_charge']               = $data['percent_charge'] + $data['fixed_charge'];
        $data['sender_wallet_balance']      = $sender_wallet->balance;
        $data['payable']                    = $sender_amount + $data['total_charge'];
        $data['default_currency_amount']    = ($sender_amount / $sender_wallet->currency->rate);
        $data['sender_currency_rate']       = $sender_wallet->currency->rate;
        return $data;
    }
    public function selectRecipient($identifier) {  
        $page_title = "Select Recipient";
        $receipients = Receipient::auth()->with('receiver')->orderByDesc("id")->paginate(12);
        return view('user.sections.send-money.recipient-select',compact('page_title','receipients','identifier'));
    }
    public function recipientSubmit(Request $request) {
        $validated = Validator::make($request->all(),[
            'identifier' => "required|string",
            'recipient'  => "required",
        ])->validate(); 
        $checkTempData = TemporaryData::where('identifier',$validated['identifier'])->first();  
        if ($checkTempData == null) {
            return redirect()->route('user.send.money.index')->with(['error' => [__('Something went wrong! Please try again')]]);
        }
        $receiver_currency = Currency::receiver()->active()->where('code',$checkTempData->data->requestData->receiver_currency)->first();
        if(!$receiver_currency) return back()->with(['error' => ['Currency ('.$validated['receiver_currency'].') isn\'t available for receive any remittance']]);

        $receiver = User::notAuth()->where('id',$validated['recipient'])->active()->first();
        if(!$receiver) return back()->with(['error' => [__('Receiver doesn\'t exists or Receiver is temporary banned')]]);

        $receiver_wallet = UserWallet::where("user_id",$receiver->id)->whereHas("currency",function($q) use ($receiver_currency){
            $q->receiver()->where("code",$receiver_currency->code);
        })->first();

        if(!$receiver_wallet) return back()->with(['error' => [__('Receiver wallet not available')]]);
        $data = json_decode(json_encode($checkTempData->data),true);
        $data['receiver_wallet'] = $receiver_wallet->toArray();
        $data['receiver'] = $receiver->toArray();

        $update_data['data']  = $data;
        DB::beginTransaction();
        try{
            $checkTempData->update($update_data);
            DB::commit();
        }catch(Exception $e) {  
            DB::rollBack();
            return redirect()->back()->withInput()->with(['error' => [__('Something went wrong! Please try again')]]);
        }
        return redirect()->route('user.send.money.sending.preview',$checkTempData->identifier);
    }
    public function sendingPreview($identifier) { 
        $checkTempData = TemporaryData::where('identifier',$identifier)->first();  
        if ($checkTempData == null) {
            return redirect()->route('user.send.money.index')->with(['error' => [__('Something went wrong! Please try again')]]);
        }
        // dd($checkTempData);
        return view('user.sections.send-money.sending-preview',compact('identifier','checkTempData'));
    }
    public function sendMoneyConfirm(Request $request) {
        $validated = Validator::make($request->all(),[
            'identifier' => "required|string", 
        ])->validate(); 
        if(Auth::guard(get_auth_guard())->check()){
            $user = auth()->guard(get_auth_guard())->user();
        }
        $basic_setting = BasicSettings::first();
        $checkTempData = TemporaryData::where('identifier',$validated['identifier'])->first();  
        if ($checkTempData == null) {
            return redirect()->route('user.send.money.index')->with(['error' => [__('Something went wrong! Please try again')]]);
        }
        $oldData = $checkTempData->data;
        $validated['sender_currency'] = $oldData->requestData->sender_currency;
        $sender_wallet = UserWallet::auth()->whereHas("currency",function($q) use ($validated) {
            $q->where("code",$validated['sender_currency'])->active();
        })->active()->first();
        $receiver_currency = Currency::receiver()->active()->where('code',$oldData->requestData->receiver_currency)->first();
        $receiver_wallet = UserWallet::where("user_id",$oldData->receiver_wallet->user_id)->whereHas("currency",function($q) use ($receiver_currency){
            $q->receiver()->where("code",$receiver_currency->code);
        })->first();
 
        $trx_id = 'SM'.getTrxNum();
        DB::beginTransaction();
        try{
            $id = DB::table("transactions")->insertGetId([
                'type'              => PaymentGatewayConst::TYPETRANSFERMONEY,
                'attribute'         => PaymentGatewayConst::SEND,
                'trx_id'            => $trx_id,
                'user_type'         => GlobalConst::USER,
                'user_id'           => auth()->user()->id,
                'wallet_id'         => $oldData->sender_wallet->id,
                'request_amount'    => $oldData->requestData->sender_amount,
                'request_currency'  => $oldData->requestData->sender_currency,
                'exchange_rate'     => $oldData->charges->exchange_rate,
                'available_balance' => $oldData->charges->sender_wallet_balance-$oldData->charges->payable,
                'percent_charge'    => $oldData->charges->percent_charge,
                'fixed_charge'      => $oldData->charges->fixed_charge,
                'total_charge'      => $oldData->charges->total_charge,
                'total_payable'     => $oldData->charges->payable,
                'receive_amount'    => $oldData->charges->receiver_amount,
                'payment_currency'  => $oldData->charges->receiver_currency,
                'receiver_type'     => GlobalConst::USER,
                'receiver_id'       => $oldData->receiver_wallet->user_id,
                'details'           => json_encode(['sendMoneyData' => $oldData]), 
                'status'            => PaymentGatewayConst::STATUSSUCCESS,
                'created_at'        => now(),
            ]);
            $sender_wallet->balance -= $oldData->charges->payable;
            $sender_wallet->save();

            $receiver_wallet->balance += $oldData->charges->receiver_amount;
            $receiver_wallet->save(); 
            $this->removeTempDataStripe($validated['identifier']);
            DB::commit();
            //User notification
            $receiver = User::findOrFail($oldData->receiver_wallet->user_id);
            $notification_content = [
                'title'   => "Send Money",
                'message' => "Send Money From ".$oldData->requestData->sender_amount. ' ' .$oldData->requestData->sender_currency." to ".$oldData->charges->payable.' '. $oldData->charges->receiver_currency." Successfully",
                'time'    => Carbon::now()->diffForHumans(),
                'image'   => files_asset_path('profile-default'),
            ]; 
            UserNotification::create([
                'type'    => NotificationConst::TRANSFER_MONEY,
                'user_id' => $user->id,
                'message' => $notification_content,
            ]);
            $notification_content = [
                'title'   => "Received Money",
                'message' => "Receved Money From ".$oldData->requestData->sender_amount. ' ' .$oldData->requestData->sender_currency." to ".$oldData->charges->payable.' '. $oldData->charges->receiver_currency." Successfully",
                'time'    => Carbon::now()->diffForHumans(),
                'image'   => files_asset_path('profile-default'),
            ]; 
            UserNotification::create([
                'type'    => NotificationConst::TRANSFER_MONEY,
                'user_id' => $receiver->id,
                'message' => $notification_content,
            ]);
        
            //Push Notifications
            try{ 
                //mail notification 
                if( $basic_setting->email_notification == true){
                    $receiver->notify(new ReceivedMoneyMail($receiver,$oldData,$trx_id));
                    $user->notify(new SendMoneyMail($user,$oldData,$trx_id));
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
        }catch(Exception $e) { 
            DB::rollBack(); 
            return redirect()->back()->withInput()->with(['error' => [__('Something went wrong! Please try again')]]);
        }
        
        return redirect()->route('user.send.money.payment.confirm.preview')->with(['success' => [__('Send Money Success')]]);
    }
    public function paymentConfirmedPreview() {
        $page_title = "Payment Completed";
        return view('user.sections.send-money.payment-confirmed',compact('page_title'));
    }
    public function removeTempDataStripe($identifier) { 
        TemporaryData::where("identifier",$identifier)->delete();
    }
}
