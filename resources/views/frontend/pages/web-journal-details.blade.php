@extends('frontend.layouts.master') 
@php
    $defualt = get_default_language_code()??'en'; 
    $en = 'en';
@endphp
@section('content') 
<section class="blog-section blog-details-section  ptb-80">
    <div class="container">
        <div class="row justify-content-center mb-30-none">
            <div class="col-xl-8 col-lg-8 mb-30">
                <div class="blog-item">
                    <div class="blog-thumb">
                        <img src="{{ get_image(@$blog->data?->image ?? null,'site-section') }}" alt="blog">
                    </div>
                    <div class="blog-content pt-3=4">
                        <h3 class="title">{{ @$blog->data->language->$defualt->title ?? @$blog->data->language->$en->title }}</h3>
                        {!! @$blog->data->language->$defualt->description ?? @$blog->data->language->$en->description !!}
                        <div class="blog-tag-wrapper">
                            <span>{{ __("Tags") }}:</span>
                            <ul class="blog-footer-tag">
                                @foreach ($blog->data->language->$defualt->tags ?? [] as $tag)
                                <li><a href="javascript:void(0)">{{ $tag }}</a></li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-4 col-lg-4 mb-30">
                <div class="blog-sidebar">
                    <div class="widget-box mb-30">
                        <h4 class="widget-title">{{ __("Categories") }}</h4>
                        <div class="category-widget-box">
                            <ul class="category-list">
                                @foreach ($categories ?? [] as $cat)
                                @php
                                    $blogCount = App\Models\Frontend\Announcement::where('status',1)->where('announcement_category_id',$cat->id)->count();

                                @endphp
                                    @if( $blogCount > 0)
                                    <li><a href="{{ setRoute('webJournal.by.category',$cat->id) }}"> {{ $cat->name->language->$defualt->name ?? $cat->name->language->$en->name }}<span>{{ @$blogCount }}</span></a></li>
                                    @else
                                    <li><a href="javascript:void(0)"> {{ @$cat->name->language->$defualt->name ?? @$cat->name->language->$en->name }}<span>{{ @$blogCount }}</span></a></li>
                                    @endif

                                @endforeach
                            </ul>
                        </div>
                    </div>
                    <div class="widget-box mb-30">
                        <h4 class="widget-title">{{ __("Recent Posts") }}</h4>
                        <div class="popular-widget-box">
                            @foreach ($recentPost as $post)
                            <div class="single-popular-item d-flex flex-wrap align-items-center">
                                <div class="popular-item-thumb">
                                    <a href="{{route('webJournal.details',[$post->id,$post->slug])}}"><img src="{{ get_image(@$post->data?->image,'site-section') }}" alt="blog"></a>
                                </div>
                                <div class="popular-item-content">
                                    <span class="date">{{showDate($post->created_at)}}</span>
                                    <h6 class="title"><a href="{{route('webJournal.details',[$post->id,$post->slug])}}">{{ @$post->data->language->$defualt->title ?? @$post->data->language->$en->title }}</a></h6>
                                </div>
                            </div> 
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section> 
@endsection