<!DOCTYPE html>
<html lang="{{ get_default_language_code() }}">
    @php
        $cookie = App\Models\Admin\SiteSections::where('key','site_cookie')->first();
        //cookies results
        $approval_status      = request()->cookie('approval_status');
        $c_user_agent         = request()->cookie('user_agent');
        $c_ip_address         = request()->cookie('ip_address');
        $c_browser            = request()->cookie('browser');
        $c_platform           = request()->cookie('platform');
        //system informations
        $s_ipAddress    = request()->ip();
        $s_location     = geoip()->getLocation($s_ipAddress);
        $s_browser      = Agent::browser();
        $s_platform     = Agent::platform();
        $s_agent        = request()->header('User-Agent');
    @endphp
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ (isset($page_title) ? __($page_title) : __("Public")) }}</title>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:ital,opsz,wght@0,9..40,300;0,9..40,400;0,9..40,500;0,9..40,700;0,9..40,800;0,9..40,900;0,9..40,1000;1,9..40,400;1,9..40,500;1,9..40,600;1,9..40,700;1,9..40,800;1,9..40,900&family=Josefin+Sans:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;1,100;1,200;1,300;1,400;1,500;1,600;1,700&display=swap" rel="stylesheet">
    @include('partials.header-asset')
    @stack('css')
</head>
<body class="{{ selectedLangDir() ?? "ltr"}}">

@include('frontend.partials.preloader')
@include('frontend.partials.scroll-to-top')
<div class="space-wrapper">
    <div class="space-body-wrapper">
            @include('frontend.partials.header')

        @yield("content")
    </div>
    @include('frontend.partials.footer')
</div>

@include('partials.footer-asset')
@include('frontend.partials.extensions.tawk-to')

@stack('script')
<script>
    $(document).ready(function () {
        $(".language-select").change(function(){ 
            var submitForm = `<form action="{{ setRoute('frontend.languages.switch') }}" id="local_submit" method="POST"> @csrf <input type="hidden" name="target" value="${$(this).val()}" ></form>`;
            $("body").append(submitForm);
            $("#local_submit").submit();
        });
    });
</script> 
<script>
    var status = "{{  @$cookie->status }}";
     //cookies results
     var approval_status      = "{{ $approval_status}}";
     var c_user_agent         = "{{ $c_user_agent}}";
     var c_ip_address         = "{{ $c_ip_address}}";
     var c_browser            = "{{ $c_browser}}";
     var c_platform           = "{{ $c_platform}}";
     //system informations
    var s_ipAddress    = "{{ $s_ipAddress}}";
    var s_browser      = "{{ $s_browser}}";
    var s_platform     = "{{ $s_platform}}";
    var s_agent        = "{{ $s_agent}}";
    const pop = document.querySelector('.cookie-main-wrapper')
    if( status == 1){
        if(approval_status == 'allow' || approval_status == 'decline' || c_user_agent === s_agent || c_ip_address === s_ipAddress || c_browser === s_browser || c_platform === s_platform){
            pop.style.bottom = "-300px";
        }else{
            window.onload = function(){
            setTimeout(function(){
                pop.style.bottom = "20px";
            }, 2000)
        }
        }
    }else{
        pop.style.bottom = "-300px";
    }
    // })
</script>
<script>
    (function ($) {
        "use strict";
        //Allow
        $('.cookie-btn').on('click', function() {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            var postData = {
                type: "allow",
            };
            $.post('{{ route('global.set.cookie') }}', postData, function(response) {
                throwMessage('success', [response]);
                setTimeout(function() {
                    location.reload();
                }, 1000);
            });
        });
        //Decline
        $('.cookie-btn-cross').on('click', function() {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            var postData = {
                type: "decline",
            };
            $.post('{{ route('global.set.cookie') }}', postData, function(response) {
                throwMessage('error',[response]);
                setTimeout(function(){
                    location.reload();
                },1000);
            });
        });
    })(jQuery)
</script>
</body>
</html>