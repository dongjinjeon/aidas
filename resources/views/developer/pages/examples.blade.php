@extends('developer.layouts.master') 
@section('content') 
<div class="developer-main-wrapper">
    <h1 class="heading-title mb-20">Examples </h1>
    <p class="pb-10">For code examples and implementation guides, please refer to the “Examples” section on our developer portal. <a href="#0" target="_blank" class="highlight text--base">Go to GitHub Repository</a></p>
</div>
<div class="page-change-area">
    <div class="navigation-wrapper">
        <a href="{{ setRoute("developer.bestPractices") }}" class="left"><i class="las la-arrow-left me-1"></i> Best Practices</a>
        <a href="{{ setRoute("developer.faq") }}" class="right">FAQ <i class="las la-arrow-right ms-1"></i></a>
    </div>
</div>
@endsection