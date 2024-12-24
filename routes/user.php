<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Request;
use App\Http\Controllers\GlobalController; 
use App\Providers\Admin\BasicSettingsProvider;
use App\Http\Controllers\User\ApiKeyController;
use App\Http\Controllers\User\WalletController;
use Pusher\PushNotifications\PushNotifications;
use App\Http\Controllers\User\ProfileController;
use App\Http\Controllers\User\AddMoneyController;
use App\Http\Controllers\User\SecurityController;
use App\Http\Controllers\User\DashboardController;
use App\Http\Controllers\User\MyVoucherController;
use App\Http\Controllers\User\SendMoneyController;
use App\Http\Controllers\User\PaymentLogController;
use App\Http\Controllers\User\ReceipientController;
use App\Http\Controllers\User\TransactionController;
use App\Http\Controllers\User\DeveloperApiController;
use App\Http\Controllers\User\RequestMoneyController;
use App\Http\Controllers\User\MoneyExchangeController;
use App\Http\Controllers\User\SupportTicketController;
use App\Http\Controllers\User\WithdrawMoneyController;

Route::prefix("user")->name("user.")->group(function(){
    Route::post("info",[GlobalController::class,'userInfo'])->name('info');
    Route::controller(DashboardController::class)->group(function(){
        Route::get('dashboard','index')->name('dashboard');
        Route::post('logout','logout')->name('logout');
        Route::post('delete/account','deleteAccount')->name('delete.account')->middleware('app.mode');
    });
    Route::controller(ProfileController::class)->prefix("profile")->name("profile.")->group(function(){
        Route::get('/','index')->name('index');
        Route::put('password/update','passwordUpdate')->name('password.update')->middleware('app.mode');
        Route::put('update','update')->name('update')->middleware('app.mode');
    });
    Route::controller(SupportTicketController::class)->prefix("support-ticket")->name("support.ticket.")->group(function () {
        Route::get('/', 'index')->name('index');
        Route::get('create', 'create')->name('create');
        Route::post('store', 'store')->name('store');
        Route::get('conversation/{encrypt_id}','conversation')->name('conversation');
        Route::post('message/send','messageSend')->name('messaage.send');
    });
    Route::controller(AddMoneyController::class)->prefix('add-money')->name('add.money.')->group(function() {
        Route::get('/','index')->name('index')->middleware('kyc.verification.guard');
        Route::post('submit','submit')->name('submit')->middleware('kyc.verification.guard');

        Route::get('success/response/{gateway}','success')->name('payment.success');
        Route::get("cancel/response/{gateway}",'cancel')->name('payment.cancel');
        Route::post("callback/response/{gateway}",'callback')->name('payment.callback')->withoutMiddleware(['web','auth','verification.guard','user.google.two.factor']);

        // POST Route For Unauthenticated Request
        Route::post('success/response/{gateway}', 'postSuccess')->name('payment.success')->withoutMiddleware(['auth','verification.guard','user.google.two.factor']);
        Route::post('cancel/response/{gateway}', 'postCancel')->name('payment.cancel')->withoutMiddleware(['auth','verification.guard','user.google.two.factor']);

        // redirect with HTML form route
        Route::get('redirect/form/{gateway}', 'redirectUsingHTMLForm')->name('payment.redirect.form')->withoutMiddleware(['auth','verification.guard','user.google.two.factor']);

        //redirect with Btn Pay
        Route::get('redirect/btn/checkout/{gateway}', 'redirectBtnPay')->name('payment.btn.pay')->withoutMiddleware(['auth','verification.guard','user.google.two.factor']);

        Route::get('manual/{token}','showManualForm')->name('manual.form');
        Route::post('manual/submit/{token}','manualSubmit')->name('manual.submit');

        Route::prefix('payment')->name('payment.')->group(function() {
            Route::get('crypto/address/{trx_id}','cryptoPaymentAddress')->name('crypto.address');
            Route::post('crypto/confirm/{trx_id}','cryptoPaymentConfirm')->name('crypto.confirm');
        });
    });
    Route::controller(SendMoneyController::class)->middleware('kyc.verification.guard')->prefix('send-money')->name('send.money.')->group(function() { 
        Route::get('/','index')->name('index');
        Route::post('submit','submit')->name('submit');
        Route::get('/select-recipient/{identifier}','selectRecipient')->name('select.recipient');
        Route::post('recipient-submit','recipientSubmit')->name('recipient.submit');
        Route::get('/sending-preview/{identifier}','sendingPreview')->name('sending.preview');
 
        Route::post('confirm','sendMoneyConfirm')->name('confirm.submit');
        Route::get('payment-confirmed','paymentConfirmedPreview')->name('payment.confirm.preview');
    });
    Route::controller(RequestMoneyController::class)->middleware('kyc.verification.guard')->prefix('request-money')->name('request.money.')->group(function() { 
        Route::get('/','index')->name('index');
        Route::post('submit','submit')->name('submit'); 
        Route::get('share/{identifier}','share')->name('share');

        Route::get('payment/{identifier}', 'requestMoneyPaymentPreview');
        Route::post('payment-submit','requestMoneyPaymentConfirm')->name('payment.confirm');
        Route::get('payment-confirmed','paymentConfirmedPreview')->name('payment.confirm.preview');
    });
    Route::controller(WithdrawMoneyController::class)->middleware('kyc.verification.guard')->prefix('withdraw-money')->name('withdraw.money.')->group(function() { 
        Route::get('/','index')->name('index');
        Route::post('submit','submit')->name('submit');
        Route::get('preview','preview')->name('preview');
        Route::post('confirm','confirmMoneyOut')->name('confirm');
    });
    Route::controller(MoneyExchangeController::class)->middleware('kyc.verification.guard')->prefix('exchange-money')->name('exchange.money.')->group(function() { 
        Route::get('/','index')->name('index');
        Route::post('submit','moneyExchangeSubmit')->name('submit'); 
    });
    //Recipient
    Route::controller(ReceipientController::class)->middleware('kyc.verification.guard')->prefix('recipient')->name('receipient.')->group(function(){
        Route::get('/my-recipient','index')->name('index');
        Route::get('/add-new-recipient','addReceipient')->name('create');
        Route::post('/store-recipient','storeReceipient')->name('store');
        
        Route::get('edit/{id}','editReceipient')->name('edit');
        Route::put('update','updateReceipient')->name('update');
        Route::delete('delete','deleteReceipient')->name('delete'); 
    });
    Route::controller(MyVoucherController::class)->middleware('kyc.verification.guard')->prefix('my-voucher')->name('my-voucher.')->group(function(){
        Route::get('/','index')->name('index'); 
        Route::post('submit','submit')->name('submit'); 
        Route::get('redeem-code/{identifier}','redeemCode')->name('redeem.code');
 
        Route::post('redeem-submit','voucherRedeemSubmit')->name('redeem.submit');
        Route::get('cancel/{identifier}','cancel')->name('cancel');
    });
    Route::controller(TransactionController::class)->prefix("transactions")->name("transactions.")->group(function(){
        Route::get('/{slug?}','index')->name('index')->whereIn('slug',['add-money-log','money-out-log','send-money-log','money-exchange-log','withdraw-log','make-payment']);
        Route::post('search','search')->name('search');

        Route::get('request-money-log','requestMoney')->name('request.money');
        Route::post('request-money-log/search','searchrequestMoney')->name('search.request.money');
        Route::get('voucher-log','voucherLog')->name('voucher.log');
        Route::post('voucher-log/search','searchVoucherMoney')->name('search.voucher.money');
    });
    Route::controller(SecurityController::class)->prefix("security")->name('security.')->group(function(){
        Route::get('google/2fa','google2FA')->name('google.2fa');
        Route::post('google/2fa/status/update','google2FAStatusUpdate')->name('google.2fa.status.update')->middleware('app.mode');
    });
    Route::controller(WalletController::class)->prefix("wallets")->name("wallets.")->group(function(){ 
        Route::post("balance","balance")->name("balance");
    });
    Route::controller(PaymentLogController::class)->middleware('businessAccountType')->prefix('payment-log')->name('payment.log.')->group(function(){
        Route::get('/','index')->name('index');
    });
    Route::controller(DeveloperApiController::class)->prefix('developer/api')->name('developer.api.')->group(function(){
        Route::get('/','index')->name('index');
        Route::post('mode/update','updateMode')->name('mode.update');
    });
    Route::controller(ApiKeyController::class)->middleware('businessAccountType')->prefix("api")->name('api.')->group(function(){
        Route::get('key','apiKey')->name('key'); 
    });
});
Route::get('user/pusher/beams-auth', function (Request $request) {
    if(Auth::check() == false) {
        return response(['Inconsistent request'], 401);
    }
    $userID = Auth::user()->id;

    $basic_settings = BasicSettingsProvider::get();
    if(!$basic_settings) {
        return response('Basic setting not found!', 404);
    }

    $notification_config = $basic_settings->push_notification_config;

    if(!$notification_config) {
        return response('Notification configuration not found!', 404);
    }

    $instance_id    = $notification_config->instance_id ?? null;
    $primary_key    = $notification_config->primary_key ?? null;
    if($instance_id == null || $primary_key == null) {
        return response('Sorry! You have to configure first to send push notification.', 404);
    }
    $beamsClient = new PushNotifications(
        array(
            "instanceId" => $notification_config->instance_id,
            "secretKey" => $notification_config->primary_key,
        )
    );
    $publisherUserId = "user-".$userID;
    try{
        $beamsToken = $beamsClient->generateToken($publisherUserId);
    }catch(Exception $e) {
        return response(['Server Error. Failed to generate beams token.'], 500);
    }

    return response()->json($beamsToken);
})->name('user.pusher.beams.auth');