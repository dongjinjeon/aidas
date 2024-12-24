@extends('admin.layouts.master')

@push('css')

@endpush

@section('page-title')
    @include('admin.components.page-title', ['title' => __($page_title)])
@endsection

@section('breadcrumb')
    @include('admin.components.breadcrumb', [
        'breadcrumbs' => [
            [
                'name' => __('Dashboard'),
                'url' => setRoute('admin.dashboard'),
            ],
        ],
        'active' => __('Make Payment Logs'),
    ])
@endsection

@section('content')

    @php
        $sender_transaction = $transactions;
        $receiver_transaction = $transactions;

        $sender_currency = $sender_transaction->details->charges->sender_currency;
        $receiver_currency = $sender_transaction->details->charges->receiver_currency;
    @endphp

    <div class="custom-card">
        <div class="card-header">
            <h6 class="title">{{ __("Sender Information") }}</h6>
        </div>
        <div class="card-body">
            <div class="card-form">
                <div class="row align-items-center mb-10-none">
                    <div class="col-xl-4 col-lg-4 form-group">
                        <ul class="user-profile-list-two">
                            <li class="one">{{ __("Date:") }} <span>{{ $sender_transaction->created_at->format("Y-m-d h:i A") }}</span></li>
                            <li class="two">{{ __("Transaction Number:") }} <span>{{ $sender_transaction->trx_id }}</span></li>
                            <li class="three">{{ __("Username:") }} <span>{{ $sender_transaction->tran_creator->username }}</span></li>
                            <li class="four">{{ __("Type:") }} <span>Money Transfer</span></li>
                            <li class="five">{{ __("Request Amount:") }} <span>{{ get_amount($sender_transaction->request_amount,$sender_currency) }}</span></li>
                        </ul>
                    </div>
                    <div class="col-xl-4 col-lg-4 form-group">
                        <div class="user-profile-thumb">
                            <img src="{{ get_image(@$sender_transaction->creator->image,'user-profile') }}" alt="payment">
                        </div>
                    </div>
                    <div class="col-xl-4 col-lg-4 form-group">
                        <ul class="user-profile-list two">
                            <li class="one">{{ __("Charge:") }} <span>{{ get_amount($sender_transaction->details->charges->total_charge,$sender_currency) }}</span></li>
                            <li class="two">{{ __("After Charge:") }} <span>{{ get_amount($sender_transaction->total_payable,$sender_currency) }}</span></li>
                            <li class="three">{{ __("Rate:") }} <span>1 {{ $sender_currency }} = {{ get_amount($sender_transaction->details->charges->exchange_rate,$sender_transaction->details->charges->receiver_currency) }}</span></li>
                            <li class="four">{{ __("Payable:") }} <span>{{ get_amount($sender_transaction->details->charges->receiver_amount,$sender_transaction->details->charges->receiver_currency) }}</span></li>
                            <li class="five">{{ __("Status:") }} <span class="{{ $sender_transaction->StringStatus->class }}">{{ $sender_transaction->StringStatus->value }}</span></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @if ($receiver_transaction)
        <div class="custom-card mt-3">
            <div class="card-header">
                <h6 class="title">{{ __("Receiver Information") }}</h6>
            </div>
            <div class="card-body">
                <div class="card-form">
                    <div class="row align-items-center mb-10-none">
                        <div class="col-xl-4 col-lg-4 form-group">
                            <ul class="user-profile-list-two">
                                <li class="two">{{ __("Username:") }} <span>{{ $receiver_transaction->tran_creator->username }}</span></li>
                                <li class="three">{{ __("Email:") }} <span>{{ $receiver_transaction->tran_creator->email }}</span></li>
                                <li class="four">{{ __("Wallet:") }} <span>{{ @$receiver_transaction->creator_wallet->currency->code }}</span></li>
                            </ul>
                        </div>
                        <div class="col-xl-4 col-lg-4 form-group">
                            <div class="user-profile-thumb">
                                <img src="{{ get_image(@$receiver_transaction->tran_creator->image,'agent-profile') }}" alt="payment">
                            </div>
                        </div>
                        <div class="col-xl-4 col-lg-4 form-group">
                            <ul class="user-profile-list two">
                                <li class="two">{{ __("Received Amount:") }} <span>{{ get_amount($sender_transaction->details->charges->receiver_amount,$receiver_currency) }}</span></li>
                                <li class="three">{{ __("Rate:") }} <span>1 {{ $sender_currency }} = {{ get_amount($sender_transaction->details->charges->exchange_rate,$sender_transaction->details->charges->receiver_currency) }}</span></li>
                                <li class="four">{{ __("Current Balance:") }} <span>{{ get_amount(@$receiver_transaction->creator_wallet->balance,@$receiver_transaction->creator_wallet->currency->code) }}</span></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

@endsection

@push('script')
        
@endpush
