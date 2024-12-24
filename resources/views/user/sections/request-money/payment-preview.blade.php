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
    <div class="col-xl-7 col-lg-8 col-md-10 mb-20">
       <form action="{{ setRoute('user.request.money.payment.confirm') }}" method="post">
            @csrf
            <input type="hidden" name="identifier" value="{{ $requestMoneyData->identifier }}">
            <div class="custom-card mt-10">
                <div class="card-body">
                    <div class="dashboard-header-wrapper">
                        <h4 class="title">{{ __("Request Money Payment") }}</h4>
                    </div>
                    <div class="preview-list-wrapper"> 
                        <div class="preview-list-item">
                            <div class="preview-list-left">
                                <div class="preview-list-user-wrapper">
                                    <div class="preview-list-user-icon">
                                        <i class="las la-receipt"></i>
                                    </div>
                                    <div class="preview-list-user-content">
                                        <span>{{ __("Request Amount") }}</span>
                                    </div>
                                </div>
                            </div>
                            <div class="preview-list-right">
                                <span class="text--warning">{{ get_amount($requestMoneyData->request_amount,$requestMoneyData->request_currency,2)  }}</span>
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
                                <span class="text--warning">{{ $requestMoneyData->user->email }}</span>
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
                                <span class="text--warning">{{ get_amount($requestMoneyData->total_charge,$requestMoneyData->request_currency,2) }}</span>
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
                                <span class="text--danger">{{ get_amount($requestMoneyData->request_amount,$requestMoneyData->request_currency,2) }}</span>
                            </div>
                        </div>
                        @if ($requestMoneyData->remark != null)  
                        <div class="preview-list-item">
                            <div class="preview-list-left">
                                <div class="preview-list-user-wrapper">
                                    <div class="preview-list-user-icon">
                                        <i class="las la-smoking"></i>
                                    </div>
                                    <div class="preview-list-user-content">
                                        <span>{{ __("Remarks") }}</span>
                                    </div>
                                </div>
                            </div>
                            <div class="preview-list-right">
                                <span class="text--info">{{ $requestMoneyData->remark }}</span>
                            </div>
                        </div>
                        @endif
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
                                <span class="text--info last">{{ get_amount($requestMoneyData->total_payable,$requestMoneyData->request_currency,2) }}</span>
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