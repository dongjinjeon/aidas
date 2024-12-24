@extends('user.layouts.master') 
@section('breadcrumb')
    @include('user.components.breadcrumb',['breadcrumbs' => [
        [
            'name'  => __("Dashboard"),
            'url'   => setRoute("user.dashboard"),
        ]
    ], 'active' => __($page_title)])
@endsection 
@section('content')
<div class="add-recipient-title">
    <h3 class="title">{{ __($page_title) }}</h3>
</div>
<div class="add-recipient-item">
 <form action="{{ setRoute('user.receipient.update') }}" method="post">
    @csrf
    @method("PUT")
    <input type="hidden" name="id" value="{{ $receipient->id }}">
    <div class="trx-inputs">
        <div class="row"> 
            <div class="col-xl-12 col-lg-12 col-md-12 form-group">
                <label>{{ __("UserName/Email") }}<span class="text--base">*</span></label>
                <input type="text" name="email" class="form--control email" value="{{ $receipient->email }}" placeholder="Enter Username Or Email..." readonly>
            </div> 
        </div>
            <div class="row">  
                <div class="col-xl-6 form-group mt-3">
                    <label>{{ __("First Name") }}<span class="text--base">*</span></label>
                    <input type="text" name="firstname" value="{{ $receipient->firstname }}" class="form--control" placeholder=" First Name">
                </div>
                <div class="col-xl-6 form-group mt-3">
                    <label>{{ __("Last Name") }}<span class="text--base">*</span></label>
                    <input type="text" name="lastname" value="{{ $receipient->lastname }}" class="form--control" placeholder=" Last Name">
                </div>
            </div>
            <div class="col-xl-12 col-lg-12 col-md-12 form-group">
                <div class="city-select-wrp">
                    <label>{{ __("Country") }}<span class="text--base">*</span></label> 
                    <select name="country" class="form--control select2-auto-tokenize country-select" data-placeholder="Select Country" data-old="{{ old('country',$receipient->country ?? "") }}"></select>
                </div>
            </div>
            <div class="row">
                <div class="col-xl-6 form-group">
                    <label>{{ __("City") }}<span class="text--base">*</span></label>
                    <input type="text" name="city" value="{{ $receipient->city }}" class="form--control" placeholder="City Name">
                </div>
                <div class="col-xl-6 form-group">
                    <label>{{ __("State") }}<span class="text--base">*</span></label>
                    <input type="text" name="state" value="{{ $receipient->state }}" class="form--control" placeholder="Enter State...">
                </div>
            </div>
            <div class="row">
                <div class="col-xl-6 form-group">
                    <label>{{ __("Zip Code") }}<span class="text--base">*</span></label>
                    <input type="number" name="zip" value="{{ $receipient->zip_code }}" class="form--control" placeholder="Enter Zip...">
                </div>
            </div>
            <div class="col-xl-12 col-lg-12 form-group">
                <label>{{ __("Address") }} <span class="text--base">*</span></label>
                <textarea class="form--control" name="address"  placeholder="Write Here…">{{ $receipient->address }}</textarea>
            </div>
            <div class="back-btn">
                <a href="{{ setRoute('user.receipient.index') }}"><i class="las la-arrow-left"></i> <span>{{ __("Back to recipient page") }}</span></a>
            </div>
            <div class="recipent-button mt-5">
                <button type="submit" class="btn btn--base w-100">{{ __("Submit") }}</button>
            </div>
        </div>
 </form>
</div>
@endsection
@push('script')
    <script>
        $(document).on("keyup",".email",function(){ 
            getUser($(this).val(),"{{ setRoute('user.info') }}",$(this));
        });
        function getUser(string,URL,errorPlace = null) {
            if(string.length < 3) {
                return false;
            }
            var CSRF = laravelCsrf();
            var data = {
                _token      : CSRF,
                text        : string,
            };
            $.post(URL,data,function() {
                // success
            }).done(function(response){ 
                if(response.data == null) {
                    if(errorPlace != null) { 
                        $(errorPlace).parents("form").find("textarea[name=address]").val("");
                        $(errorPlace).parents("form").find("input[name=lastname]").val("");
                        $(errorPlace).parents("form").find("input[name=firstname]").val("");
                        $(errorPlace).parents("form").find("input[name=zip]").val(""); 
                        $(errorPlace).parents("form").find("input[name=state]").val("");
                        $(errorPlace).parents("form").find("input[name=city]").val(""); 

                        throwMessage('error',["User doesn't  exists."]);
                    }
                }else { 
                    var user = response.data;
                    if(user.address == null || user.address == "") {
                        user.address = {};
                    }
                    var user_infos = {
                        firstname: user.firstname,
                        lastname: user.lastname,
                        middlename: user.middlename,
                        mobile_code: user.mobile_code, 
                        mobile: user.mobile,
                        address: user.address.address ?? "",
                        city: user.address.city ?? "",
                        state: user.address.state ?? "",
                        zip: user.address.zip ?? "",
                    };
                    $.each(user_infos,function(index,item) {
                        if(item == "" || item == null || item == undefined) {
                            $(errorPlace).parents("form").find("input[name="+index+"],textarea[name="+index+"]").removeAttr("readonly");
                        }
                        $(errorPlace).parents("form").find("input[name="+index+"],textarea[name="+index+"]").val(item);
                    })
                    $(errorPlace).parents("form").find(".phone-code").text("+"+user.mobile_code);

                    if(user.address.country == undefined || user.address.country == "") {
                        // make select box for country
                        var country_select = `
                            <label>Country <span>*</span></label>
                            <select name="country" class="form--control country-select" data-placeholder="Select Country" data-old="">
                                <option selected disabled>Select Country</option>
                            </select>
                        `;
                        $(".country-select-wrp").html(country_select);
                        $("select[name=country]").select2();

                        getAllCountries("{{ setRoute('global.countries') }}",$(".country-select"),$(".country-select"));
                        countrySelect(".country-select",$(".country-select"));
                        $(errorPlace).parents("form").find("input[name=zip]").val("").removeAttr("readonly");
                        $(errorPlace).parents("form").find("input[name=mobile_code]").val("").removeAttr("readonly");
                        $(errorPlace).parents("form").find("input[name=mobile]").val("").removeAttr("readonly");
                        $(errorPlace).parents("form").find("input[name=state]").val("").removeAttr("readonly");
                        $(errorPlace).parents("form").find("input[name=city]").val("").removeAttr("readonly");
                        $(errorPlace).parents("form").find(".phone-code").text("");
                        $("select[name=country]").change(function(){
                            var phoneCode = $("select[name=country] :selected").attr("data-mobile-code");
                            placePhoneCode(phoneCode);
                        });
                    }else {
                        $(errorPlace).parents("form").find("input[name=country]").val(user.address.country ?? "");
                        $(errorPlace).parents("form").find("input[name=state]").val(user.address.state ?? "");
                        $(errorPlace).parents("form").find("input[name=city]").val(user.address.city ?? "");
                        $(errorPlace).parents("form").find("input[name=zip]").val(user.address.zip ?? "");
                    }
                }
            }).fail(function(response) {
                var response = JSON.parse(response.responseText);
                throwMessage(response.type,response.message.error);
            });
        }
    </script>
     <script>
        getAllCountries("{{ setRoute('global.countries') }}");
        $(document).ready(function(){ 
            countrySelect(".country-select",$(".country-select").siblings(".select2")); 
        });
    </script>
@endpush