<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DeveloperController;
use App\Http\Controllers\Frontend\IndexController;

Route::name('frontend.')->group(function() {
    Route::controller(IndexController::class)->group(function() {
        Route::post("subscribe","subscribe")->name("subscribe");
        Route::post("contact/message/send","contactMessageSend")->name("contact.message.send");
        Route::post('languages/switch','languageSwitch')->name('languages.switch'); 
    });
});
//developer introductions routes
Route::prefix('developer')->name('developer.')->group(function() {
    Route::controller(DeveloperController::class)->group(function() {
        Route::get('prerequisites','prerequisites')->name('prerequisites');
        Route::get('authentication','authentication')->name('authentication');
        Route::get('base-url','baseUrl')->name('baseUrl');
        Route::get('access-token','accessToken')->name('accessToken');
        Route::get('initiate-payment','initiatePayment')->name('initiatePayment');
        Route::get('check-payment-status','checkPaymentStatus')->name('checkPaymentStatus');
        Route::get('response-codes','responseCodes')->name('responseCodes');
        Route::get('error-handling','errorHandling')->name('errorHandling');
        Route::get('best-practices','bestPractices')->name('bestPractices');
        Route::get('examples','examples')->name('examples');
        Route::get('faq','faq')->name('faq');
        Route::get('support','support')->name('support');
    });
});