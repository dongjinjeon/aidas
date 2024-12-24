@extends('developer.layouts.master') 
@section('content') 
<div class="developer-main-wrapper">
    <h1 class="heading-title mb-20">Support</h1>
    <p class="pb-10">If you encounter any issues or need assistance, please reach out to our dedicated developer support team <a href="{{ setRoute("contactUs") }}" class="text-decoration-underline fw-bold">Contact Us</a></p>
    <p>Thank you for choosing {{ @$basic_settings->site_name }}  Payment Gateway Solutions! We look forward to seeing your integration thrive and provide a seamless payment experience for your valued customers.</p>
</div>
<div class="page-change-area">
    <div class="navigation-wrapper">
        <a href="{{ setRoute("developer.faq") }}" class="left"><i class="las la-arrow-left me-1"></i>  FAQ</a>
    </div>
</div>
@endsection