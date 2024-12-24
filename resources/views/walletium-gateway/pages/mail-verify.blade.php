@extends('walletium-gateway.layouts.master')

@push('css')
    
@endpush

@section('content')
    <!--~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
        Start Account
    ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~-->
    <div class="row">
        <div class="col-4 ptb-80 mx-auto text-center">
            <section class="account-section payment custom-card">
                <div class="right">
                    <div class="account-area">
                        <div class="account-header text-center">
                            @if ($payment_gateway_image)
                                <a class="site-logo" href="javascript:void(0)"><img src="{{ $payment_gateway_image }}" alt="logo"></a>
                            @endif
                            <h4 class="title">{{ __("Account verification") }}</h4>
                            <p>{{ __("Please check your email inbox to get verification code") }}</p>
                        </div>
                        <div class="account-middle">
                            <div class="account-form-area">
                                <form action="{{ $form_submit_url }}" class="account-form bounce-safe" method="POST">
                                    @csrf
                                    <div class="row ml-b-20">
                                        <div class="col-lg-12 form-group">
                                            @include('admin.components.form.input',[
                                                'name'              => "code",
                                                'placeholder'       => "Enter Code", 
                                                'value'             => old("code"),
                                                'attribute'         => 'required',
                                            ])
                                        </div>
                                        <div class="col-lg-12 form-group text-center">
                                            <button type="submit" class="btn--base w-100">{{ __("Verify") }}</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </div>
    </div>
    <!--~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
        End Account
    ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~-->
@endsection


@push('script')
    
@endpush