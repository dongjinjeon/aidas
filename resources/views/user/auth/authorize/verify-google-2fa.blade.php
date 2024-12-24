@extends('frontend.layouts.master')
@push('css')
    <style>
        .account-section .account-wrapper .account-form input {
            border: 1px solid #3e7c70 !important;
        }
        .otp-form{
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
        }
        .otp {
            display: inline-block;
            width: 58px;
            height: 50px;
            text-align: center;
            padding: 0;
            margin: 3px;
            border-radius: 5px;
        }
    </style>
@endpush
@section('content')
<section class="account-section ptb-80"> 
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-10 col-md-12">
                <div class="account-area">
                    <div class="row align-items-center justify-content-center"> 
                        <div class="col-lg-6 mx-auto">
                            <h3 class="title">{{ __("Two Factor Authorization") }}</h3>
                            <p>{{ __("Please enter your authorization code to access dashboard") }}</p>
                            <form action="{{ setRoute('user.authorize.google.2fa.submit') }}" class="account-form" method="POST">
                                @csrf
                                <div id="recaptcha-container"></div>
                                <div class="row ml-b-20">
                                    <div class="col-lg-12 form-group otp-form">
                                        <input class="otp form--control" name="code[]" type="text" oninput='digitValidate(this)' onkeyup='tabChange(1)'
                                        maxlength=1 required>
                                    <input class="otp form--control" name="code[]" type="text" oninput='digitValidate(this)' onkeyup='tabChange(2)'
                                        maxlength=1 required>
                                    <input class="otp form--control" name="code[]" type="text" oninput='digitValidate(this)' onkeyup='tabChange(3)'
                                        maxlength=1 required>
                                    <input class="otp form--control" name="code[]" type="text" oninput='digitValidate(this)' onkeyup='tabChange(4)'
                                        maxlength=1 required>
                                    <input class="otp form--control" name="code[]" type="text" oninput='digitValidate(this)' onkeyup='tabChange(5)'
                                        maxlength=1 required>
                                    <input class="otp form--control" name="code[]" type="text" oninput='digitValidate(this)' onkeyup='tabChange(6)'
                                        maxlength=1 required>
                                    </div>
                                    <div class="col-lg-12 form-group text-start">
                                        <div class="forgot-item">
                                            <label><a href="{{ setRoute('user.login') }}" class="text--base">{{ __("Back to Login") }}</a></label>
                                        </div>
                                    </div>
                                    <div class="col-lg-12 form-group text-center">
                                        <button type="submit" class="btn--base w-100">{{ __("Authorize") }}</button>
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
    <ul class="bg-bubbles">
        <li></li>
        <li></li>
        <li></li>
        <li></li>
        <li></li>
        <li></li>
        <li></li>
        <li></li>
        <li></li>
        <li></li>
    </ul>
@endsection
@push('script')
        <script>
            let digitValidate = function (ele) {
                console.log(ele.value);
                ele.value = ele.value.replace(/[^0-9]/g, '');
            }
            let tabChange = function (val) {
                let ele = document.querySelectorAll('.otp');
                if (ele[val - 1].value != '') {
                    ele[val].focus()
                } else if (ele[val - 1].value == '') {
                    ele[val - 2].focus()
                }
            }
            $(".otp").parents("form").find("input[type=submit],button[type=submit]").click(function(e){
                // e.preventDefault();
                var otps = $(this).parents("form").find(".otp");
                var result = true;
                $.each(otps,function(index,item){
                    if($(item).val() == "" || $(item).val() == null) {
                        result = false;
                    }
                });

                if(result == false) {
                    $(this).parents("form").find(".otp").addClass("required");
                }else {
                    $(this).parents("form").find(".otp").removeClass("required");
                    $(this).parents("form").submit();
                }
            });
        </script>
@endpush
