@extends('user.layouts.master') 
@section('breadcrumb')
    @include('user.components.breadcrumb',['breadcrumbs' => [
        [
            'name'  => __("Dashboard"),
            'url'   => setRoute("user.dashboard"),
        ]
    ], 'active' => __($page_title)])
@endsection 
@section('content')
<div class="row justify-content-center">
    <div class="col-xl-6 col-lg-7 col-md-10">
        <div class="payment-area text-center mt-40">
            <div class="payment-loader pb-40">
                <svg class="checkmark" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 52 52">
                    <circle class="checkmark__circle" cx="26" cy="26" r="25" fill="none" />
                    <path class="checkmark__check" fill="none" d="M14.1 27.2l7.1 7.2 16.7-16.8" />
                </svg>
            </div>
            <h4 class="title">{{ __("Send Money Successfully Completed") }}</h4>
            <div class="conformation-footer pt-4">
                <div class="payment-conformation-footer">
                    <a href="{{ setRoute('user.dashboard') }}" class="btn--base w-100">{{ __("Go To Dashboard") }}</a>
                </div>
            </div>
        </div>
    </div>
</div> 
@endsection