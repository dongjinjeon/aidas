@extends('frontend.layouts.master') 
@php
    $defualt = get_default_language_code()??'en';
    $en = 'en';
@endphp
@section('content') 
<!--~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
    service Section
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~-->
<section class="service-section pt-80">
    <div class="container">
        <div class="row">
            <div class="col-lg-8">
                <div class="service-title pb-20">
                    <h2 class="title">{{ $service->value->language->$defualt->heading ?? $service->value->language->$en->heading }}</h2>
                </div>
                 <div class="service-content-pragraph">
                    <p>{{ $service->value->language->$defualt->desc ?? $service->value->language->$en->desc }}</p>
                 </div>
            </div>
        </div>
        <div class="service-area pt-60">
            <div class="row mb-20-none">
                @php
                    $i = 1;
                @endphp
                @foreach ($service->value->items ?? [] as $key => $item)
                <div class="col-xl-8 col-lg-12 mb-20">
                    <div class="service-area" data-aos="fade-left" data-aos-duration="1200">
                        <div class="number">
                            <h3 class="title">{{ $i }}.</h3>
                        </div>
                        <div class="work-content tri-right left-top">
                            <p>{{ $item->language->$defualt->description ?? $item->language->$en->description }}</p>
                        </div>
                    </div>
                </div>
                @php
                    $i++
                @endphp
                @endforeach 
            </div>
        </div>
    </div>
</section>
@include('frontend.partials.subscribe-section')
@endsection