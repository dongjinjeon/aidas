@extends('developer.layouts.master') 
@section('content')  
<div class="developer-main-wrapper">
    <h1 class="heading-title mb-20">Response Codes</h1>
    <p>{{ @$basic_settings->site_name }}  API responses include standard HTTP status codes to indicate the success or failure of a request. Successful responses will have a status code of <strong>200 OK</strong>, while various error conditions will be represented by different status codes along with error messages in the response body.</p>
</div>
<div class="page-change-area">
    <div class="navigation-wrapper">
        <a href="{{ setRoute("developer.checkPaymentStatus") }}" class="left"><i class="las la-arrow-left me-1"></i>  Check Payment Status</a>
        <a href="{{ setRoute("developer.errorHandling") }}" class="right">Error Handling <i class="las la-arrow-right ms-1"></i></a>
    </div>
</div>
@endsection