<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Frontend\SiteController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
Route::controller(SiteController::class)->group(function(){ 
    Route::get('/','home')->name('index');
    Route::get('developer','developer')->name('developer'); 
    Route::get('about-us','aboutUs')->name('aboutUs');
    Route::get('services','services')->name('services'); 
    Route::get('web-journal','webJournal')->name('webJournal'); 

    Route::get('web-journal/details/{id}/{slug}','webJournalDetails')->name('webJournal.details');
    Route::get('web-journal/category/{id}','webJournalByCategory')->name('webJournal.by.category');
    
    Route::get('contact-us','contactUs')->name('contactUs');
    Route::post('contact/store','contactStore')->name('contact.store');

    Route::get('page/{slug}','pageView')->name('page.view');
    Route::get('faq','faq')->name('faq'); 

    Route::get('success','walletiumPaymentSuccess')->name('walletiumPaymentSuccess'); 
    Route::get('cancel','walletiumPaymentCancel')->name('walletiumPaymentCancel'); 
});