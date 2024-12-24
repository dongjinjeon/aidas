@extends('frontend.layouts.master') 
@php
    $defualt = get_default_language_code()??'en';
    $en = 'en';
@endphp
@section('content') 
<section class="contact-location pt-80">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-xl-12 text-center">
                <div class="section-title pb-40">
                    <h2 class="title">{{ $contact->value->language->$defualt->heading ?? $contact->value->language->$defualt->heading }}</h2>
                </div>
            </div>
        </div>
        <div class="row justify-content-center mb-30-none">
            @foreach ($contact->value->items ?? [] as $key => $item)
            <div class="col-xl-4 col-lg-4 col-md-6 mb-30">
                <div class="contact-widget">
                    <div class="contact-item-icon">
                        <i class="{{ $item->icon ?? "" }}"></i>
                    </div>
                    <div class="contact-item-content">
                        <h3 class="title">{{ $item->language->$defualt->title ?? $item->language->$en->title }}</h3>
                        <span class="sub-title">{{ $item->language->$defualt->details ?? $item->language->$en->details }}</span>
                    </div>
                </div>
            </div> 
            @endforeach 
        </div>
    </div>
</section>
<section class="contact-section ptb-80">
    <div class="container">
       <div class="contact-form">
            <div class="massage-area">
                <div class="row mb-30-none justify-content-center">
                    <div class="col-xl-12 col-lg-12 mb-30">
                        <div class="contact-form-area">
                            <div class="contact-header text-center pb-30">
                                <h2 class="title">{{ $contact->value->language->$defualt->sub_heading ?? $contact->value->language->$en->sub_heading }}</h2>
                            </div>
                            <form action="{{ setRoute('frontend.contact.message.send') }}" method="POST" class="contact-form">
                                @csrf
                                <div class="row justify-content-center mb-10-none">
                                    <div class="col-xl-6 col-lg-6 col-md-6 form-group">
                                        <label>{{ __("Name") }}<span>*</span></label>
                                        <input type="text" name="name" class="form--control" placeholder="Enter Name...">
                                    </div>
                                    <div class="col-xl-6 col-lg-6 col-md-6 form-group">
                                        <label>{{ __("Email") }}<span>*</span></label>
                                        <input type="email" name="email" class="form--control" placeholder="Enter Email...">
                                    </div>
                                    <div class="col-xl-12 col-lg-12 form-group">
                                        <label>{{ __("Message") }}<span>*</span></label>
                                        <textarea name="message" class="form--control" placeholder="{{ __("Write Here") }}..."></textarea>
                                    </div>
                                    <div class="col-lg-12 form-group pt-3">
                                        <button type="submit" class="btn--base w-100">{{ __("Send Message") }}</button>
                                    </div>
                                 </div>
                             </form>
                        </div>
                     </div>
                </div>
            </div>
         </div>
    </div>
</section>
@endsection