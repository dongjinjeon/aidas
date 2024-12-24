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
            <div class="col-xl-10 col-lg-12 col-md-12">
                <div class="account-area">
                    <div class="row align-items-center justify-content-center">
                        <div class="col-xl-6 col-lg-6 mb-30 account-img">
                           <div class="account-item-img">
                              <img src="{{  get_image($auth_text->value->image ?? "","site-section") ?? asset('public/frontend/images/element/account-img.webp') }}" alt="img">                                          
                           </div>
                        </div>
                        <div class="col-xl-6 col-lg-6 col-md-10"> 
                            <h3 class="title">{{ @$auth_text->value->language->$lang->register_title }}</h3>
                                <p>{{ @$auth_text->value->language->$lang->register_text }}</p>
                                <div class="account-select-item">
                                <label>{{ __("Account Type") }} <span>*</span></label>
                                <select class="nice-select select-area py-0 w-100 account-type">
                                    <option selected disabled>{{ __("Select One") }}</option>
                                    <option value="1">{{ __("Personal Account") }}</option>
                                    <option value="2">{{ __("Business Account") }}</option>
                                </select>
                                </div>
                            <div class="personal-account ptb-30 select-account" data-select-target="1" style="display: none;">
                                <form action="{{ setRoute('user.register.submit') }}" class="account-form" method="POST">
                                    @csrf
                                    <input type="hidden" name="type" value="personal">
                                    <div class="row">
                                        <div class="col-lg-6 form-group">
                                            @include('admin.components.form.input',[
                                                'name'          => "firstname",
                                                'placeholder'   => __("First Name"),
                                                'value'         => old("firstname"),
                                            ])
                                        </div>
                                        <div class="col-lg-6 form-group">
                                            @include('admin.components.form.input',[
                                                'name'          => "lastname",
                                                'placeholder'   => __("Last Name"),
                                                'value'         => old("lastname"),
                                            ])
                                        </div>
                                        <div class="col-lg-12 form-group">
                                            @include('admin.components.form.input',[
                                                'type'          => "email",
                                                'name'          => "email",
                                                'placeholder'   => __("Email"),
                                                'value'         => old("email"),
                                            ])
                                        </div>
                                        <div class="col-lg-12 form-group">  
                                            <select name="country" class="form--control select2-auto-tokenize country-select" data-old="{{ old('country',$user_country) }}">
                                                <option selected disabled>{{ __("Select Country") }}</option>
                                            </select>
                                        </div>  
                                        <div class="col-lg-12 form-group show_hide_password">
                                            <input type="password" class="form--control" name="password" placeholder="{{ __("Password") }}" required>
                                            <a href="javascript:void(0)" class="show-pass"><i class="fa fa-eye-slash" aria-hidden="true"></i></a> 
                                        </div> 
                                        <div class="col-lg-12 form-group">
                                            <div class="custom-check-group mb-0">
                                                <input type="checkbox" id="level-1" name="agree">
                                                <label for="level-1" class="mb-0">{{ __("I have read agreed with the") }} 
                                                    <a href="{{ route('page.view','terms-condition') }}" class="text--base">{{ __("Terms Condition") }}</a>
                                                    <a class="text--base">&</a>
                                                    <a href="{{ route('page.view','privacy-policy') }}" class="text--base">{{ __("Privacy Policy") }}</a>
                                                </label>
                                            </div> 
                                        </div>
                                        <div class="col-lg-12 form-group text-center">
                                            <button type="submit" class="btn--base w-100">{{ __("Register Now") }}</button>
                                        </div>
                                        <div class="col-lg-12 text-center">
                                            <div class="account-item mt-10">
                                                <label>{{ __("Already Have An Account?") }} <a href="{{ setRoute('user.login') }}" class="text--base">{{ _("Login Now") }}</a></label>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                            <div class="business-account ptb-30 select-account" data-select-target="2" style="display: none;">
                                <form action="{{ setRoute('user.register.submit') }}" class="account-form" method="POST">
                                    @csrf
                                    <input type="hidden" name="type" value="business">
                                    <div class="row">
                                        <div class="col-lg-6 form-group">
                                            @include('admin.components.form.input',[
                                                'name'          => "firstname",
                                                'placeholder'   => __("First Name"),
                                                'value'         => old("firstname"),
                                            ])
                                        </div>
                                        <div class="col-lg-6 form-group">
                                            @include('admin.components.form.input',[
                                                'name'          => "lastname",
                                                'placeholder'   => __("Last Name"),
                                                'value'         => old("lastname"),
                                            ])
                                        </div>
                                        <div class="col-lg-12 form-group">
                                            <input type="text" class="form-control form--control" name="company_name" value="{{ old('company_name') }}" placeholder="Company Name">
                                        </div> 
                                        <div class="col-lg-12 form-group">
                                            @include('admin.components.form.input',[
                                                'type'          => "email",
                                                'name'          => "email",
                                                'placeholder'   => __("Email"),
                                                'value'         => old("email"),
                                            ])
                                        </div>
                                        <div class="col-lg-12 form-group">  
                                            <select name="country" class="form--control select2-auto-tokenize country-select" data-old="{{ old('country',$user_country) }}">
                                                <option selected disabled>{{ __("Select Country") }}</option>
                                            </select>
                                        </div> 
                                        <div class="col-lg-12 form-group show_hide_password">
                                            <input type="password" class="form--control" name="password" placeholder="{{ __("Password") }}" required>
                                            <a href="javascript:void(0)" class="show-pass"><i class="fa fa-eye-slash" aria-hidden="true"></i></a> 
                                        </div> 
                                        <div class="col-lg-12 form-group">
                                            <div class="custom-check-group mb-0">
                                                <input type="checkbox" id="level-2" name="agree">
                                                <label for="level-2" class="mb-0">{{ __("I have read agreed with the") }} 
                                                    <a href="{{ route('page.view','terms-condition') }}" class="text--base">{{ __("Terms Condition") }}</a>
                                                    <a class="text--base">&</a>
                                                    <a href="{{ route('page.view','privacy-policy') }}" class="text--base">{{ __("Privacy Policy") }}</a>
                                                </label>
                                            </div> 
                                        </div>
                                        <div class="col-lg-12 form-group text-center">
                                            <button type="submit" class="btn--base w-100">{{ __("Register Now") }}</button>
                                        </div>
                                        <div class="col-lg-12 text-center">
                                            <div class="account-item mt-10">
                                                <label>{{ __("Already Have An Account?") }} <a href="{{ setRoute('user.login') }}" class="text--base">{{ _("Login Now") }}</a></label>
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
    </div>
</section>

@endsection

@push('script')
    <script> 
        getAllCountries("{{ setRoute('global.countries') }}",$(".country-select"));
        $(document).ready(function(){
            $("select[name=country]").change(function(){
                var phoneCode = $("select[name=country] :selected").attr("data-mobile-code");
                placePhoneCode(phoneCode);
            });

            setTimeout(() => {
                var phoneCodeOnload = $("select[name=country] :selected").attr("data-mobile-code");
                placePhoneCode(phoneCodeOnload);
            }, 400);
            countrySelect(".country-select",$(".country-select").siblings(".select2"));
        });
    </script>
    <script>
        $(".account-type").change(function(){
            var targetItem = $(this).val();
            selectContainItem(targetItem);
        });

        $(document).ready(function() {
            var professionSelectedItem = $(".account-type").val();
            selectContainItem(professionSelectedItem);
        });


        function selectContainItem(targetItem) {
            $(".select-account").slideUp(300);
            if(targetItem == null) return false;
            if(targetItem.length > 0) {
                var findTargetItem = $(".select-account");
                $.each(findTargetItem, function(index,item) {
                    if($(item).attr("data-select-target") == targetItem) {
                        $(item).slideDown(300);
                    }else {
                        $(item).slideUp(300);
                    }
                })
            }
        }
    </script>
@endpush