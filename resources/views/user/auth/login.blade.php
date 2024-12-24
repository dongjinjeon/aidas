
@extends('frontend.layouts.master')
@php
    $lang = selectedLang();
    $auth_slug = Illuminate\Support\Str::slug(App\Constants\SiteSectionConst::AUTH_SECTION);
    $auth_text = App\Models\Admin\SiteSections::getData( $auth_slug)->first();
@endphp
@section('content') 
    <section class="account-section ptb-80">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-10 col-md-12">
                    <div class="account-area">
                        <div class="row align-items-center justify-content-center">
                            <div class="col-lg-6 col-md-6 mb-30 account-img">
                               <div class="account-item-img">
                                  <img src="{{  get_image($auth_text->value->image ?? "","site-section") ?? asset('public/frontend/images/element/account-img.webp') }}" alt="img">                                          
                               </div>
                            </div>
                            <div class="col-lg-6 col-md-8">
                                <form action="{{ setRoute('user.login.submit') }}" class="account-form" method="POST">
                                    @csrf
                                    <h3 class="title">{{ @$auth_text->value->language->$lang->login_title }}</h3>
                                     <p>{{ @$auth_text->value->language->$lang->login_text }}</p>
                                    <div class="row">
                                        @php
                                            env("APP_MODE") == 'demo' ? $email = "user@appdevs.net": $email= "";
                                            env("APP_MODE") == 'demo' ? $password = "appdevs": $password= "";
                                        @endphp
                                        <div class="col-lg-12 form-group"> 
                                            @include('admin.components.form.input',[
                                                'name'          => "credentials",
                                                'placeholder'   => __("Username OR Email Address"),
                                                'required'      => true,
                                                'value'         => old('email') ?? $email ?? "",
                                            ])
                                        </div>
                                        <div class="col-lg-12 form-group show_hide_password">
                                            <input type="password" class="form-control form--control" name="password" value="{{ $password ?? "" }}" placeholder="{{ __("Enter Password") }}" required>
                                            <a href="#0" class="show-pass"><i class="fa fa-eye-slash" aria-hidden="true"></i></a>
                                        </div>
                                        <div class="col-lg-12 form-group">
                                            <div class="forgot-item text-end">
                                                <label><a href="{{ setRoute('user.password.forgot') }}" class="text--base">{{ __("Forgot Password") }}?</a></label>
                                            </div>
                                        </div>
                                        <div class="col-lg-12 form-group text-center">
                                            <button type="submit" class="btn--base w-100">{{ __("Login Now") }}</button>
                                        </div>
                                        
                                        <div class="col-lg-12 text-center">
                                            <div class="account-item">
                                                <label>{{ __("Don't Have An Account") }}? <a href="{{ setRoute('user.register') }}" class="account-control-btn text--base">{{ __("Register Now") }}</a></label>
                                            </div>
                                        </div>
                                        <div class="col-lg-12 text-center">
                                            <div class="terms-item">
                                                <label>{{ __("By clicking Login you are agreeing with our") }} <a href="{{ route('page.view','terms-condition') }}" class="text--base">{{ __("Terms Condition") }}.</a></label>
                                            </div>
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
@endsection

@push('script')
    
@endpush