@extends('walletium-gateway.layouts.master')

@push('css')
    
@endpush

@section('content')
    <!--~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
        Start Account
    ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~--> 
   <section class="account-section ptb-80">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-6 col-md-4">
                <div class="account-area">
                    <div class="row align-items-center justify-content-center">
                        <div class="col-lg-12 col-md-12 mb-30 account-img mx-auto text-center">
                            @if ($payment_gateway_image)
                                <a class="site-logo" href="javascript:void(0)"><img src="{{ $payment_gateway_image }}" alt="logo"></a>
                            @endif
                            <h4 class="title">{{ __("Pay With") }} {{ $merchant_configuration->name }}</h4>
                            <p>{{ __("With a Walletium account, you're eligible for Purchase, Protection and Rewards") }}</p>
                        </div>
                        <div class="col-lg-12 col-md-12">
                            <form action="{{ $auth_form_submit }}" class="account-form bounce-safe" method="POST">
                                @csrf
                                <div class="row ml-b-20">
                                    <div class="col-lg-12 form-group">
                                        @include('admin.components.form.input',[
                                            'name'              => "email",
                                            'placeholder'       => "Enter Email", 
                                            'value'             => old("email"),
                                            'attribute'         => 'required',
                                        ])
                                    </div>
                                    <div class="col-lg-12 form-group show_hide_password">
                                        <input type="password" class="form-control form--control" name="password" placeholder="{{ __("Enter Password") }}" required>
                                        <a href="#0" class="show-pass"><i class="fa fa-eye-slash" aria-hidden="true"></i></a>
                                    </div>
                                    <div class="col-lg-12 form-group text-center">
                                        <button type="submit" class="btn--base w-100">{{ __("Login") }}</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
    <!--~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
        End Account
    ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~-->
@endsection


@push('script')
    
@endpush