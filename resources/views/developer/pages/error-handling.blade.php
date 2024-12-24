@extends('developer.layouts.master') 
@section('content') 
<div class="developer-main-wrapper">
    <h1 class="heading-title mb-20">Error Handling</h1>
    <p>In case of an error, the API will return an error response containing a specific error code <strong>400, 403</strong> Failed and a user-friendly message. Refer to our API documentation for a comprehensive list of error codes and their descriptions.</p>
</div>
<div class="page-change-area">
    <div class="navigation-wrapper">
        <a href="{{ setRoute("developer.responseCodes") }}" class="left"><i class="las la-arrow-left me-1"></i>  Response Codes</a>
        <a href="{{ setRoute("developer.bestPractices") }}" class="right">Best Practices <i class="las la-arrow-right ms-1"></i></a>
    </div>
</div>
@endsection