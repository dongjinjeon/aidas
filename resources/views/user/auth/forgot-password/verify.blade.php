@extends('frontend.layouts.master') 
@section('content') 
    <section class="account-section ptb-80">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-6 col-md-8">
                    <div class="account-area">
                        <h3 class="title">{{ __("OTP Verification") }}</h3>
                        <p>{{ __("Please check your email address to get the OTP (One time password).") }}</p>
                        <form action="{{ setRoute('user.password.forgot.verify.code',$token) }}" class="account-form" method="POST">
                            @csrf
                            <div class="row ml-b-20">
                                <div class="col-lg-12 form-group">
                                    @include('admin.components.form.input',[
                                        'name'          => "code",
                                        'placeholder'   => "Enter Verification Code",
                                        'required'      => true,
                                        'value'         => old("code"),
                                    ])
                                </div>
                                <div class="col-lg-12 form-group">
                                    <div class="forgot-item">
                                        <label>{{ __("Don't get code? ") }}<a href="{{ setRoute('user.password.forgot.resend.code',$token) }}" class="text--base">{{ __("Resend") }}</a></label>
                                    </div>
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
@endsection 