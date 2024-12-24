@extends('user.layouts.master') 
@section('breadcrumb')
    @include('user.components.breadcrumb',['breadcrumbs' => [
        [
            'name'  => __("Dashboard"),
            'url'   => setRoute("user.dashboard"),
        ]
    ], 'active' => __("Sending Preview")])
@endsection 
@section('content')
@php
    $previewData = $checkTempData->data;
@endphp
<div class="row justify-content-center">
    <div class="col-xl-7 col-lg-8 col-md-10 mb-20">
       <form action="{{ setRoute('user.send.money.confirm.submit') }}" method="post">
        @csrf
            <input type="hidden" name="identifier" value="{{ $identifier }}">
            <div class="custom-card mt-10">
                <div class="card-body">
                    <div class="dashboard-header-wrapper">
                        <h4 class="title">{{ __("Sending Preview") }}</h4>
                    </div>
                    <div class="preview-list-wrapper">
                        <div class="preview-list-item">
                            <div class="preview-list-left">
                                <div class="preview-list-user-wrapper">
                                    <div class="preview-list-user-icon">
                                        <i class="menu-icon las la-paper-plane"></i>
                                    </div>
                                    <div class="preview-list-user-content">
                                        <span>{{ __("Sender Amount") }}</span>
                                    </div>
                                </div>
                            </div>
                            <div class="preview-list-right">
                                <span class="text--success">{{ get_amount($previewData->requestData->sender_amount,$previewData->requestData->sender_currency,2) }}</span>
                            </div>
                        </div>
                        <div class="preview-list-item">
                            <div class="preview-list-left">
                                <div class="preview-list-user-wrapper">
                                    <div class="preview-list-user-icon">
                                        <i class="las la-receipt"></i>
                                    </div>
                                    <div class="preview-list-user-content">
                                        <span>{{ __("Receiver Amount") }}</span>
                                    </div>
                                </div>
                            </div>
                            <div class="preview-list-right">
                                <span class="text--warning">{{ get_amount($previewData->charges->receiver_amount,$previewData->charges->receiver_currency,2) }}</span>
                            </div>
                        </div>
                        <div class="preview-list-item">
                            <div class="preview-list-left">
                                <div class="preview-list-user-wrapper">
                                    <div class="preview-list-user-icon">
                                        <i class="las la-battery-half"></i>
                                    </div>
                                    <div class="preview-list-user-content">
                                        <span>{{ __("Receiver Email") }}</span>
                                    </div>
                                </div>
                            </div>
                            <div class="preview-list-right">
                                <span class="text--warning">{{ $previewData->receiver->email }}</span>
                            </div>
                        </div>
                        <div class="preview-list-item">
                            <div class="preview-list-left">
                                <div class="preview-list-user-wrapper">
                                    <div class="preview-list-user-icon">
                                        <i class="las la-exchange-alt"></i>
                                    </div>
                                    <div class="preview-list-user-content">
                                        <span>{{ __("Exchange Rate") }}</span>
                                    </div>
                                </div>
                            </div>
                            <div class="preview-list-right"> 
                                <span class="text--warning">{{ "1 ".$previewData->charges->sender_currency ." = ". get_amount($previewData->charges->exchange_rate,$previewData->charges->receiver_currency,4) }}</span>
                            </div>
                        </div>
                        <div class="preview-list-item">
                            <div class="preview-list-left">
                                <div class="preview-list-user-wrapper">
                                    <div class="preview-list-user-icon">
                                        <i class="las la-battery-half"></i>
                                    </div>
                                    <div class="preview-list-user-content">
                                        <span>{{ __("Total Fees & Charges") }}</span>
                                    </div>
                                </div>
                            </div>
                            <div class="preview-list-right">
                                <span class="text--warning">{{ get_amount($previewData->charges->total_charge,$previewData->charges->sender_currency,2) }}</span>
                            </div>
                        </div>
                        <div class="preview-list-item">
                            <div class="preview-list-left">
                                <div class="preview-list-user-wrapper">
                                    <div class="preview-list-user-icon">
                                        <i class="lab la-get-pocket"></i>
                                    </div>
                                    <div class="preview-list-user-content">
                                        <span>{{ __("Will Get") }}</span>
                                    </div>
                                </div>
                            </div>
                            <div class="preview-list-right">
                                <span class="text--danger">{{ get_amount($previewData->charges->receiver_amount,$previewData->charges->receiver_currency,2) }}</span>
                            </div>
                        </div>
                        <div class="preview-list-item">
                            <div class="preview-list-left">
                                <div class="preview-list-user-wrapper">
                                    <div class="preview-list-user-icon">
                                        <i class="las la-money-check-alt"></i>
                                    </div>
                                    <div class="preview-list-user-content">
                                        <span class="last">{{ __("Total Payable Amount") }}</span>
                                    </div>
                                </div>
                            </div>
                            <div class="preview-list-right">
                                <span class="text--info last">{{ get_amount($previewData->charges->payable,$previewData->charges->sender_currency,2) }}</span>
                            </div>
                        </div>
                    </div>
                    <div class="preview-btn pt-5">
                        <button type="submit" class="btn--base w-100">{{ __("Confirm") }}</button>
                    </div>
                </div>
            </div>
       </form>
    </div>
</div>
@endsection