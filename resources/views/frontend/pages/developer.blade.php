@extends('developer.layouts.master') 
@section('content')  
<!--~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
    Start Developer page
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~-->
<div class="developer-main-wrapper ">
    <h1 class="heading-title text-center mb-20">{{ @$basic_settings->site_name }} - Advanced Digital Mobile Wallet Developer API Documentation</h1>
    
    <p>Unlock the full potential of your applications with the {{ @$basic_settings->site_name }} Developer API. Seamlessly integrate our powerful digital wallet solution into your software, allowing you to accept payments, manage transactions, and enhance financial capabilities effortlessly. With robust documentation and flexible endpoints, our API offers developers the tools they need to create innovative solutions and provide users with a seamless financial experience. Join the {{ @$basic_settings->site_name }} ecosystem today and revolutionize the way you do business.</p>
    <h1 class="heading-title mb-20 mt-40">1. Introduction</h1>
    <p>The {{ @$basic_settings->site_name }} Developer API allows you to seamlessly integrate {{ @$basic_settings->site_name }} Payment Gateway Solutions into your website, enabling secure and efficient debit and credit card transactions. With our API, you can initiate payments, check payment statuses, and even process refunds, all while ensuring a smooth and streamlined payment experience for your customers.</p>
</div>
<div class="page-change-area">
     
    <div class="navigation-wrapper">
        <a href="{{ setRoute("developer.prerequisites") }}" class="right">Prerequisites <i class="las la-arrow-right ms-1"></i></a>
    </div>
     
</div>
<!--~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
    End Developer page
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~-->
@endsection