@extends('walletium-gateway.layouts.master')

@push('css')
    
@endpush

@section('content')
    <!--~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
    Start Payment-preview
    ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~-->
    <section class="payment-preview-section">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-xl-6 col-lg-6 ptb-80">
                    <form class="custom-card" method="POST" action="{{ $submit_form_url }}">
                        @csrf
                        <div class="card-body">
                            <div class="preview-list-wrapper">
                                <div class="preview-list-item">
                                    <div class="preview-list-left">
                                        <div class="preview-list-user-wrapper">
                                            <div class="preview-list-user-icon">
                                                <img src="{{ get_image($user->image,'user-profile','profile') }}" alt="client">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="preview-list-center">
                                        <a class="site-logo" href="javascript:void(0)"><img src="{{ $payment_gateway_image }}" alt="logo"></a>
                                    </div>
                                    <div class="preview-list-right">
                                        <span class="text--success">{{ get_amount($request_record->amount,$request_record->currency,3)  }}</span>
                                    </div>
                                </div>
                                <div class="preview-list-item">
                                    <div class="preview-list-left">
                                        <div class="preview-list-user-wrapper">
                                            <div class="preview-list-user-content ps-0">
                                                <span>{{ __("Ship To") }} {{ $user->fullname }}</span>
                                                <h6 class="title mt-10 mb-0">{{ $user->address->address ?? "" }}</h6>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                @if ($user_wallet != null) 
                                <div class="preview-list-item">
                                    <div class="preview-list-left">
                                        <div class="preview-list-user-wrapper">
                                            <div class="preview-list-user-content ps-0">
                                                <span>{{ __("Available Balance ") }}</span> 
                                            </div>
                                        </div>
                                    </div> 
                                    <div class="preview-list-right">
                                        <span class="text--warning">{{ get_amount($user_wallet->balance,$request_record->currency,3)  }}</span>
                                    </div>
                                </div>
                                @endif
                            </div>
                            <div class="conformation-btn">
                                <a href="{{ url($request_record->data->cancel_url . "?token=".$token) }}" class="btn--base bg--danger text-white">{{ __("Cancel") }}</a>
                                <button type="submit" class="btn--base">{{ __("Pay") }}</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>
    <!--~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
        End Payment-preview
    ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~-->
@endsection


@push('script')
    
@endpush