@extends('frontend.layouts.master') 
@php
    $defualt = get_default_language_code()??'en'; 
@endphp
@section('content')  
    <!--~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
        Start page
    ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~-->
    <section class="page-section py-4">
        <div class="container">
            <div class="row justify-content-center pt-4">
                <div class="col-xl-8">
                    <div class="section-header text-center mb-4">
                       <h3>{{ @$page_data->title->language->$defualt->title }}</h3>
                    </div>
                    <div class="section-body">
                        {!! @$page_data->content->language->$defualt->content !!}
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!--~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
        End Page
    ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~-->  
@endsection  