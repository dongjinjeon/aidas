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
                <div class="col-lg-6 col-md-8">
                    <div class="account-area">
                        <h3 class="title">{{ @$auth_text->value->language->$lang->forget_title }}</h3>
                        <p>{{ @$auth_text->value->language->$lang->forget_text }}</p>
                        <form action="{{ setRoute('user.password.forgot.send.code') }}" class="account-form" method="POST">
                            @csrf
                            <div class="row ml-b-20">
                                <div class="col-lg-12 form-group">
                                    @include('admin.components.form.input',[
                                        'name'          => "credentials",
                                        'placeholder'   => "Username OR Email Address",
                                        'required'      => true,
                                    ])
                                </div>
                                <div class="col-lg-12 form-group">
                                    <div class="forgot-item">
                                        <label>Already Have An Account? <a href="{{ setRoute('user.login') }}" class="text--base">{{ __("Login") }}</a></label>
                                    </div>
                                </div>
                                <div class="col-lg-12 form-group text-center">
                                    <button type="submit" class="btn--base w-100">{{ __("Send Code") }}</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection 