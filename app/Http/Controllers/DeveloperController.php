<?php

namespace App\Http\Controllers;

use App\Models\Admin\BasicSettings;
  
class DeveloperController extends Controller
{
    public function prerequisites() {
        $page_title = setPageTitle("Prerequisites");
        $basic_settings = BasicSettings::first();
        return view('developer.pages.prerequisites',compact('page_title','basic_settings'));
    }
    public function authentication() {
        $basic_settings = BasicSettings::first();
        $page_title = setPageTitle("Authentication");
        return view('developer.pages.authentication',compact('page_title','basic_settings'));
    }
    public function baseUrl() {
        $page_title = setPageTitle("Base Url");
        return view('developer.pages.base-url',compact('page_title'));
    }
    public function accessToken() {
        $page_title = setPageTitle("Access Token");
        return view('developer.pages.access-token',compact('page_title'));
    }
    public function initiatePayment() {
        $page_title = setPageTitle("Initiate Payment");
        return view('developer.pages.initiate-payment',compact('page_title'));
    }
    public function checkPaymentStatus() {
        $page_title = setPageTitle("Check Payment Status");
        return view('developer.pages.check-payment-status',compact('page_title'));
    }
    public function responseCodes() {
        $basic_settings = BasicSettings::first();
        $page_title = setPageTitle("Response Codes");
        return view('developer.pages.response-codes',compact('page_title','basic_settings'));
    }
    public function errorHandling() {
        $page_title = setPageTitle("Error Handling");
        return view('developer.pages.error-handling',compact('page_title'));
    }
    public function bestPractices() {
        $page_title = setPageTitle("Best Practices");
        return view('developer.pages.best-practices',compact('page_title'));
    }
    public function examples() {
        $page_title = setPageTitle("Examples");
        return view('developer.pages.examples',compact('page_title'));
    }
    public function faq() {
        $basic_settings = BasicSettings::first();
        $page_title = setPageTitle("FAQ");
        return view('developer.pages.faq',compact('page_title','basic_settings'));
    }
    public function support() {
        $basic_settings = BasicSettings::first();
        $page_title = setPageTitle("Support");
        return view('developer.pages.support',compact('page_title','basic_settings'));
    }
}
