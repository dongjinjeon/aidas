@extends('user.layouts.master')

@section('breadcrumb')
    @include('user.components.breadcrumb',['breadcrumbs' => [
        [
            'name'  => __("Dashboard"),
            'url'   => setRoute("user.dashboard"),
        ]
    ], 'active' => __("")])
@endsection

@section('content')
<div class="dashboard-wallet"> 
    <div class="my-wallet ptb-20">
        <h3 class="title">{{ __("My Wallet") }}</h3>
    </div>
    <div class="row">
        @foreach ($userWallet as $item) 
        <div class="col-xxl-3 col-xl-4 col-lg-4 col-md-6 col-sm-6 mb-20">
            <div class="wallet-item">
                <div class="wallet-details">
                    <p>{{ $item->currency->name}}</p>
                    <h4 class="title">
                        @if ($item->currency->type == "CRYPTO")
                        {{ get_amount($item->balance,null,8) }}
                        @else
                        {{ get_amount($item->balance,null,2) }}
                        @endif 
                        {{ $item->currency->code}}
                    </h4>
                </div>
                <div class="country-flag">
                    <img src="{{ get_image($item->currency->flag,'currency-flag') }}" alt="flag">
                 </div>
            </div>
        </div> 
        @endforeach
    </div>
</div>    
<div class="dashboard-chart pt-40">
    <h3 class="title">{{ __("Transaction Overview") }}</h3>
    <div class="row">
        <div class="col-12">
            <div class="chart">
                <div class="chart-bg">
                    <div id="chart1" data-chart_one_data="{{ json_encode($chartData['chart_one_data']) }}" data-month_day="{{ json_encode($chartData['month_day']) }}"></div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="dashboard-list-area mt-60 mb-30">
    <div class="dashboard-header-wrapper">
        <h4 class="title">L{{ __("atest Transactions") }}</h4>
        <div class="dashboard-btn-wrapper">
            <div class="dashboard-btn">
                @if (auth()->user()->type == "personal")
                <a href="{{ setRoute('user.transactions.index','add-money-log') }}" class="btn--base">{{ __("View More") }}</a>
                @else
                <a href="{{ setRoute('user.payment.log.index') }}" class="btn--base">{{ __("View More") }}</a>
                @endif 
            </div>
        </div>
    </div>
</div>
@include('user.components.wallets.transation-log', compact('transactions'))
@endsection 