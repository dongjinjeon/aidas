@extends('frontend.layouts.master') 
@section('content') 
    <section class="account-section ptb-80">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-6 col-md-8">
                    <div class="account-area">
                        <h3 class="title">{{ __("Password Reset") }}</h3>
                        <p>{{ __("Reset your password") }}</p>
                        <form action="{{ setRoute('user.password.reset',$token) }}" class="account-form" method="POST">
                            @csrf
                            <div class="row ml-b-20">
                                <div class="col-lg-12 form-group">
                                    @include('admin.components.form.input',[
                                        'name'          => "password",
                                        'type'          => 'password',
                                        'placeholder'   => "Enter New Password",
                                        'required'      => true,
                                    ])
                                </div>
                                <div class="col-lg-12 form-group">
                                    @include('admin.components.form.input',[
                                        'name'          => "password_confirmation",
                                        'type'          => 'password',
                                        'placeholder'   => "Enter Confirm Password",
                                        'required'      => true,
                                    ])
                                </div>
                                <!-- <div class="col-lg-12 form-group">
                                    <div class="forgot-item">
                                        <label><a href="{{ setRoute('user.login') }}" class="text--base">{{ __("Login") }}</a></label>
                                    </div>
                                </div> -->
                                <div class="col-lg-12 form-group text-center pt-3">
                                    <button type="submit" class="btn--base w-100">{{ __("Reset") }}</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection 