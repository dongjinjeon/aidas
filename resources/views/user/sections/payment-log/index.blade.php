@extends('user.layouts.master')

@push('css')
    
@endpush

@section('breadcrumb')
    @include('user.components.breadcrumb',['breadcrumbs' => [
        [
            'name'  => __("Dashboard"),
        'url'   => setRoute("user.dashboard"),
        ]
    ], 'active' => __("Payment Log")])
@endsection

@section('content')
    <div class="dashboard-list-area mt-20">
        <div class="dashboard-header-wrapper">
            <h5 class="title">{{ __($page_title) }}</h5>
        </div>
        <div class="dashboard-list-wrapper">
            @include('user.components.wallets.transation-log',compact('transactions'))
        </div>
    </div>
@endsection

@push('script')
   
@endpush