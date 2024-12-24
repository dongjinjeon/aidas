<!DOCTYPE html>
<html lang="{{ get_default_language_code() }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ (isset($page_title) ? __($page_title) : __("Public")) }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@100;200;300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    
    @include('partials.header-asset')
    @stack('css')
</head>
<body>

<!--~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
    Start Header
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~-->
 @include('frontend.partials.preloader')
 @include('developer.partials.top-nav')
<!--~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
    End Header
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~-->
<div class="developer-page-container">
    @include('developer.partials.side-nav')
    <div class="developer-body-wrapper">
        @yield('content')
    </div>
</div> 
@include('partials.footer-asset')
@stack('script')
<script>
    $(".sidebar-mobile-btn button").click(function(){
        $(".developer-page-container .developer-bar").toggleClass("active");
        $('.body-overlay').addClass('active');
    });
    $(document).on("click","#body-overlay",function(){
        $('.body-overlay').removeClass('active');
        $('.developer-page-container .developer-bar').removeClass('active');
    });
</script>
</body>
</html>