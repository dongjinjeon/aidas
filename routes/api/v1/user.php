<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\User\ProfileController;
use App\Http\Controllers\Api\V1\User\AddMoneyController;
use App\Http\Controllers\Api\V1\User\DashboardController;
use App\Http\Controllers\Api\V1\User\MyVoucherController;
use App\Http\Controllers\Api\V1\User\SendMoneyController;
use App\Http\Controllers\Api\V1\User\ReceipientController;
use App\Http\Controllers\Api\V1\User\TransactionController;
use App\Http\Controllers\Api\V1\User\RequestMoneyController;
use App\Http\Controllers\Api\V1\User\MoneyExchangeController;
use App\Http\Controllers\Api\V1\User\WithdrawMoneyController;

Route::prefix("user")->name("api.user.")->group(function(){

    Route::controller(ProfileController::class)->prefix('profile')->group(function(){
        Route::get('info','profileInfo');
        Route::post('info/update','profileInfoUpdate')->middleware('app.mode');
        Route::post('password/update','profilePasswordUpdate')->middleware('app.mode');
    });

    // Logout Route
    Route::post('logout',[ProfileController::class,'logout']);
    Route::post('delete/account',[ProfileController::class,'deleteAccount'])->middleware('app.mode');

    // // Add Money Routes
    Route::controller(AddMoneyController::class)->prefix("add-money")->name('add.money.')->group(function(){
        Route::get("payment-gateways","getPaymentGateways")->middleware('kyc.verification.api');

        // Submit with automatic gateway
        Route::post("automatic/submit","automaticSubmit")->middleware('kyc.verification.api');

        // Automatic Gateway Response Routes
        Route::get('success/response/{gateway}','success')->withoutMiddleware(['auth:api'])->name("payment.success");
        Route::get("cancel/response/{gateway}",'cancel')->withoutMiddleware(['auth:api'])->name("payment.cancel");
        // POST Route For Unauthenticated Request
        Route::post('success/response/{gateway}', 'postSuccess')->name('payment.success')->withoutMiddleware(['auth:api']);
        Route::post('cancel/response/{gateway}', 'postCancel')->name('payment.cancel')->withoutMiddleware(['auth:api']);
        //redirect with Btn Pay
        Route::get('redirect/btn/checkout/{gateway}', 'redirectBtnPay')->name('payment.btn.pay')->withoutMiddleware(['auth:api']);

        Route::get('manual/input-fields','manualInputFields'); 
        // Submit with manual gateway
        Route::post("manual/submit","manualSubmit");

        // Automatic gateway additional fields
        Route::get('payment-gateway/additional-fields','gatewayAdditionalFields');

        Route::prefix('payment')->name('payment.')->group(function() {
            Route::get('crypto/address/{trx_id}','cryptoPaymentAddress')->name('crypto.address');
            Route::post('crypto/confirm/{trx_id}','cryptoPaymentConfirm')->name('crypto.confirm');
        });

    });
    Route::controller(WithdrawMoneyController::class)->middleware('kyc.verification.api')->prefix('withdraw-money')->name('withdraw.money.')->group(function() { 
        Route::get('/index','index');
        Route::post('submit','submit');
        Route::post('manual/confirm','moneyOutManualConfirmed')->name('manual.confirmed');
    });

    // // Dashboard, Notification, 
    Route::controller(DashboardController::class)->group(function(){
        Route::get("dashboard","dashboard");
        Route::get("notifications","notifications");
    });
    Route::controller(SendMoneyController::class)->middleware('kyc.verification.api')->prefix('send-money')->name('send.money.')->group(function() { 
        Route::get('/index','index');
        Route::post('submit','submit'); 
        Route::post('recipient-submit','sendMoneyConfirm');   
    });
    Route::controller(RequestMoneyController::class)->middleware('kyc.verification.api')->prefix('request-money')->name('request.money.')->group(function() { 
        Route::get('/index','index');
        Route::post('submit','submit');  
        Route::get('/information','information');
        Route::post('payment-submit','requestMoneyPaymentConfirm')->name('payment.confirm');
    });
    Route::controller(MyVoucherController::class)->middleware('kyc.verification.api')->prefix('my-voucher')->name('my-voucher.')->group(function(){
        Route::get('/index','index'); 
        Route::post('submit','submit');  
 
        Route::post('redeem-submit','voucherRedeemSubmit');
        Route::get('cancel/{identifier}','cancel');
    });
    Route::controller(MoneyExchangeController::class)->middleware('kyc.verification.api')->prefix('exchange-money')->name('exchange.money.')->group(function() { 
        Route::get('/index','index');
        Route::post('submit','moneyExchangeSubmit'); 
    });
    // // Transaction
    Route::controller(TransactionController::class)->prefix("transaction")->group(function(){
        Route::get("log","log");
        Route::get("request-money","requestMoney");
        Route::get("voucher-money","voucherMoney");
    });
    //Recipient
    Route::controller(ReceipientController::class)->middleware('kyc.verification.api')->prefix('recipient')->name('receipient.')->group(function(){
        Route::get('/my-recipient','index');
        Route::get('/user-search','userSearch');
        Route::post('/store-recipient','storeReceipient');
        
        Route::post('update','updateReceipient');
        Route::delete('delete','deleteReceipient');
    });
});