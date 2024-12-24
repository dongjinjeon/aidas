@extends('frontend.layouts.master') 
@php
    $defualt = get_default_language_code()??'en';
    $en = 'en';
@endphp
@section('content') 
<section class="about-section pt-80">
    <div class="container">
        <div class="row">
            <div class="col-lg-6 mb-30">
                <div class="about-content">
                    <h2 class="title"> {{ $about->value->language->$defualt->heading ?? $about->value->language->$en->heading }}</h2>
                    <p>{{ $about->value->language->$defualt->desc ?? $about->value->language->$en->desc }}</p>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="about-img">
                    <img src="{{ get_image(@$about->value->image, "site-section") }}" alt="img">
                </div>
            </div>
        </div>
    </div>
</section>
@include('frontend.partials.subscribe-section')
@endsection