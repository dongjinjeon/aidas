
@extends('user.layouts.master')
@section('breadcrumb')
    @include('user.components.breadcrumb',['breadcrumbs' => [
        [
            'name'  => __("Dashboard"),
            'url'   => setRoute("user.dashboard"),
        ]
    ], 'active' => __($page_title)])
@endsection

@section('content')
    <div class="dashboard-list-area mt-20">
        <div class="dashboard-header-wrapper">
            <h4 class="title">{{ $page_title ?? "" }}</h4>
            <div class="header-search-wrapper">
                <div class="position-relative">
                    <input class="form-control" type="text" name="search" placeholder="Ex: Transaction ID" aria-label="Search">
                    <span class="las la-search"></span>
                </div>
            </div>
        </div>
        <div class="dashboard-list-wrapper">
            <div class="item-wrapper">
                <div class="log-list">
                    @include('user.components.wallets.request-money-transation-log', compact('transactions'))
                </div>
            </div>
        </div>
    </div>
@endsection

@push('script')
    <script>  
        itemSearch($("input[name=search]"),$(".log-list"),"{{ setRoute('user.transactions.search.request.money') }}",1);
    </script>
@endpush