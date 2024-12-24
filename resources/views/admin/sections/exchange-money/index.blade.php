@extends('admin.layouts.master') 
@section('page-title')
    @include('admin.components.page-title', ['title' => __($page_title)])
@endsection

@section('breadcrumb')
    @include('admin.components.breadcrumb', [
        'breadcrumbs' => [
            [
                'name' => __('Dashboard'),
                'url' => setRoute('admin.dashboard'),
            ],
        ],
        'active' => __('Withdraw Money Logs'),
    ])
@endsection

@section('content')
    <div class="table-area">
        <div class="table-wrapper">
            <div class="table-header">
                <h5 class="title">{{ $page_title }}</h5>
                <div class="table-btn-area"> 
                    @include('admin.components.search-input',[
                        'name'  => 'transaction_search',
                    ]) 
                </div>
            </div>
            <div class="table-responsive">
                @include('admin.components.data-table.exchange-money-transaction-log',[
                    'data'  => $transactions
                ])
            </div>
            {{ get_paginate($transactions) }}
        </div>
    </div>
@endsection

@push('script')
<script>
    itemSearch($("input[name=transaction_search]"),$(".transaction-search-table"),"{{ setRoute('admin.exchange.money.search') }}",1);
</script>
@endpush
