@extends('frontend.layouts.master') 
@php
    $defualt = get_default_language_code()??'en'; 
    $en = 'en';
@endphp
@section('content') 
<!--~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
        Start blog
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~-->
<section class="blog-section ptb-80">
    <div class="container mx-auto">
        <div class="blog-header text-center pb-40">
            <h2 class="title">{{ $blog_section->value->language->$defualt->heading ?? $blog_section->value->language->$en->heading }}</h2>
        </div>
        <div class="row justify-content-center mb-20-none">
            @foreach ($blogs ?? [] as $item)
            <div class="col-lg-4 col-md-6 mb-20">
                <div class="blog-item">
                    <div class="blog-thumb">
                        <img src="{{ get_image(@$item->data?->image ?? null,'site-section') }}" alt="img"> 
                    </div>
                    @php
                        $description = $item->data?->language?->$defualt->description ?? $item->data?->language?->$en->description;
                        $description = strip_tags($description);
                    @endphp
                    <div class="blog-content">
                        <a href="{{route('webJournal.details',[$item->id,$item->slug])}}"><h3 class="title">{{ $item->data?->language?->$defualt->title ?? $item->data?->language?->$en->title }}</h3></a>
                        <p>{{ Str::words($description, 30, '...') }}</p>
                    </div>
                    <div class="blog-btn">
                        <a href="{{route('webJournal.details',[$item->id,$item->slug])}}" class="btn--base w-100">{{ __("Read More") }} <i
                            class="las la-arrow-right"></i>
                        </a>
                    </div>
                </div>
            </div> 
            @endforeach
        </div>
    </div>
</section>
<!--~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
    End blog
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~-->
@endsection