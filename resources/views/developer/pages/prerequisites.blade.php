@extends('developer.layouts.master') 
@section('content')  
<div class="developer-main-wrapper">
    <h1 class="heading-title mb-20">Prerequisites</h1>
    <p>Before you begin integrating the {{ @$basic_settings->site_name }}  Developer API, make sure you have:</p>
    <ol class="pt-1">
        <li>An active {{ @$basic_settings->site_name }}  Business account.</li>
        <li>Basic knowledge of API integration and web development with PHP & Laravel.</li>
        <li> A secure and accessible web server to handle API requests.</li>
    </ol>
</div>
<div class="page-change-area">
     
    <div class="navigation-wrapper">
        <a href="{{ setRoute("developer") }}" class="left"><i class="las la-arrow-left me-1"></i> Introduction</a>
        <a href="{{ setRoute("developer.authentication") }}" class="right">Authentication <i class="las la-arrow-right ms-1"></i></a>
    </div> 
</div>
@endsection