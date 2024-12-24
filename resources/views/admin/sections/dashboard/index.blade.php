@extends('admin.layouts.master')

@push('css')
<style>
    .country-flag {
        width: 65px;
        height: 65px;
        display: -webkit-box;
        display: -ms-flexbox;
        display: flex;
        -webkit-box-align: center;
        -ms-flex-align: center;
        align-items: center;
    }
    .country-flag img {
    border-radius: 10px;
}
</style>
@endpush

@section('page-title')
    @include('admin.components.page-title',['title' => __($page_title)])
@endsection

@section('breadcrumb')
    @include('admin.components.breadcrumb',['breadcrumbs' => [
        [
            'name'  => __("Dashboard"),
            'url'   => setRoute("admin.dashboard"),
        ]
    ], 'active' => __("Dashboard")])
@endsection

@section('content')
    <div class="dashboard-area">
        <div class="dashboard-item-area">
            <div class="row">
                <div class="col-xxxl-4 col-xxl-3 col-xl-3 col-lg-6 col-md-6 col-sm-12 mb-15">
                    @php
                        $add_money_total    = $trx_add_money->count();

                        $add_money_pending  = $trx_add_money->where('status',payment_gateway_const()::STATUSPENDING)->count();
                        $add_money_success  = $trx_add_money->where('status',payment_gateway_const()::STATUSSUCCESS)->count();

                        $add_money_success_with_pending_count = ($add_money_pending + $add_money_success);

                        $one_percent_of_pending_success_add_money = (($add_money_success_with_pending_count / 100) == 0) ? 1 : ($add_money_success_with_pending_count / 100);

                        $one_percent_of_total_add_money = (($add_money_total / 100) == 0) ? 1 : ($add_money_total / 100);

                    @endphp
                    <div class="dashbord-item">
                        <div class="dashboard-content">
                            <div class="left">
                                <h6 class="title">{{ __("Add Money Request") }}</h6>
                                <div class="user-info">
                                    <h2 class="user-count">{{ $add_money_total?? "" }}</h2>
                                </div>
                                <div class="user-badge">
                                    <span class="badge badge--success">{{ __("Success") }} {{ $add_money_success?? "" }}</span>
                                    <span class="badge badge--warning">{{ __("Pending") }} {{ $add_money_pending?? "" }}</span>
                                </div>
                            </div>
                            <div class="right">
                                <div class="chart" id="chart6" data-percent="{{ floor(($add_money_pending / $one_percent_of_pending_success_add_money)) }}"><span>{{ floor(($add_money_pending / $one_percent_of_pending_success_add_money)) }}%</span></div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xxxl-4 col-xxl-3 col-xl-3 col-lg-6 col-md-6 col-sm-12 mb-15">
                    @php
                        $money_out_total    = $trx_money_out->count();
                        $money_out_pending  = $trx_money_out->where('status',payment_gateway_const()::STATUSPENDING)->count();
                        $money_out_success  = $trx_money_out->where('status',payment_gateway_const()::STATUSSUCCESS)->count(); 

                        $one_percent_of_total_money_out = (($money_out_total / 100) == 0) ? 1 : ($money_out_total / 100);
                    @endphp

                    <div class="dashbord-item">
                        <div class="dashboard-content">
                            <div class="left">
                                <h6 class="title">{{ __("Withdraw Money Request") }}</h6>
                                <div class="user-info">
                                    <h2 class="user-count">{{ $money_out_total?? "" }}</h2>
                                </div>
                                <div class="user-badge">
                                    <span class="badge badge--info">{{ __("Completed") }} {{ $money_out_success?? "" }}</span>
                                    <span class="badge badge--warning">{{ __("Pending") }} {{ $money_out_pending?? "" }}</span>
                                </div>
                            </div>
                            <div class="right">
                                <div class="chart" id="chart8" data-percent="{{ floor($money_out_pending / $one_percent_of_total_money_out) }}"><span>{{ floor($money_out_pending / $one_percent_of_total_money_out) }}%</span></div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xxxl-4 col-xxl-3 col-xl-3 col-lg-6 col-md-6 col-sm-12 mb-15">
                    @php
                        $support_ticket_pending = $support_ticket->where('status',support_ticket_const()::PENDING)->count();
                        $support_ticket_solved  = $support_ticket->where('status',support_ticket_const()::SOLVED)->count();
                        $support_ticket_active  = $support_ticket->where('status',support_ticket_const()::ACTIVE)->count();

                        $support_pending_solved_count = ($support_ticket_pending + $support_ticket_solved);
                        $one_percent_of_support_pending_solved_count = (($support_pending_solved_count / 100) == 0) ? 1 : ($support_pending_solved_count / 100);

                    @endphp
                    <div class="dashbord-item">
                        <div class="dashboard-content">
                            <div class="left">
                                <h6 class="title">{{ __("Active Tickets") }}</h6>
                                <div class="user-info">
                                    <h2 class="user-count">{{ $support_ticket_active?? "" }}</h2>
                                </div>
                                <div class="user-badge">
                                    <span class="badge badge--warning">{{ __("Pending") }} {{ $support_ticket_pending?? "" }}</span>
                                    <span class="badge badge--success">{{ __("Solved") }} {{ $support_ticket_solved?? "" }}</span>
                                </div>
                            </div>
                            <div class="right">
                                <div class="chart" id="chart10" data-percent="{{ floor(($support_ticket_pending / $one_percent_of_support_pending_solved_count))  }}"><span>{{ floor(($support_ticket_pending / $one_percent_of_support_pending_solved_count)) }}%</span></div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xxxl-4 col-xxl-3 col-xl-3 col-lg-6 col-md-6 col-sm-12 mb-15">
                    @php
                        $total_user = $users->count();

                        $active_users = $users->where('status',global_const()::ACTIVE)->count();
                        $banned_users = $users->where('status',global_const()::BANNED)->count();

                        $one_percent_of_total_users = (($total_user / 100) == 0) ? 1 : ($total_user / 100);

                    @endphp
                    <div class="dashbord-item">
                        <div class="dashboard-content">
                            <div class="left">
                                <h6 class="title">{{ __("Total Users") }}</h6>
                                <div class="user-info">
                                    <h2 class="user-count">{{ $total_user?? "" }}</h2>
                                </div>
                                <div class="user-badge">
                                    <span class="badge badge--info">{{ __("Active") }} {{ $active_users?? "" }}</span>
                                    <span class="badge badge--warning">{{ __("Unverified") }} {{ $banned_users?? "" }}</span>
                                </div>
                            </div>
                            <div class="right">
                                <div class="chart" id="chart11" data-percent="{{ floor($total_user / $one_percent_of_total_users) }}"><span>{{ floor($total_user / $one_percent_of_total_users) }}%</span></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                @if ($add_money_success>0)
                <h6 class="title">{{ __("Add Money Balance") }}</h6>
                @endif 
                @foreach ($add_money_by_currency as $item)
                    @php
                        $currency = App\Models\Admin\Currency::where('code',$item->request_currency)->first();
                    @endphp
                    @if ($currency != null) 
                    <div class="col-xxxl-4 col-xxl-3 col-xl-3 col-lg-6 col-md-6 col-sm-12 mb-15"> 
                        <div class="dashbord-item">
                            <div class="dashboard-content">
                                <div class="left">
                                    <h6 class="title">{{ $currency->name }}</h6>
                                    <div class="user-info">
                                        <h2 class="user-count">{{ $currency->symbol }} {{ ceil($item->total_request_amount)?? "" }}({{ $item->request_currency }})</h2>
                                    </div>
                                </div>
                                <div class="right">
                                    <div class="country-flag">
                                        <img src="{{ get_image($currency->flag,'currency-flag') }}" alt="flag">
                                     </div>
                                </div>
                            </div>
                        </div>
                    </div> 
                    @endif
                @endforeach
            </div>
            <div class="row">
                @if ($money_out_success>0)
                <h6 class="title">{{ __("Withdraw Money Balance") }}</h6>
                @endif 
                @foreach ($money_out_by_currency as $item)
                    @php
                        $currency = App\Models\Admin\Currency::where('code',$item->request_currency)->first();
                    @endphp
                    @if ($currency != null) 
                    <div class="col-xxxl-4 col-xxl-3 col-xl-3 col-lg-6 col-md-6 col-sm-12 mb-15">
                        <div class="dashbord-item">
                            <div class="dashboard-content">
                                <div class="left">
                                    <h6 class="title">{{ $currency->name }}</h6>
                                    <div class="user-info">
                                        <h2 class="user-count">{{ $currency->symbol }} {{ ceil($item->total_request_amount)?? "" }}({{ $item->request_currency }})</h2>
                                    </div>
                                </div>
                                <div class="right">
                                    <div class="country-flag">
                                        <img src="{{ get_image($currency->flag,'currency-flag') }}" alt="flag">
                                     </div>
                                </div>
                            </div>
                        </div>
                    </div> 
                    @endif
                @endforeach
            </div>
        </div>
    </div>
    <div class="chart-area mt-15">
        <div class="row mb-15-none">
            <div class="col-xxl-6 col-xl-6 col-lg-6 mb-15">
                <div class="chart-wrapper">
                    <div class="chart-area-header">
                        <h5 class="title">{{ __("Monthly Add Money Chart") }}</h5>
                    </div>
                    <div class="chart-container">
                        <div id="chart1" class="sales-chart"></div>
                    </div>
                </div>
            </div> 
            <div class="col-xxl-6 col-xl-6 col-lg-6 mb-15">
                <div class="chart-wrapper">
                    <div class="chart-area-header">
                        <h5 class="title">{{ __("Monthly Money Out Chart") }}</h5>
                    </div>
                    <div class="chart-container">
                        <div id="chart2" class="revenue-chart"></div>
                    </div>
                </div>
            </div> 
            <div class="col-xxxl-6 col-xxl-3 col-xl-6 col-lg-6 mb-15">
                <div class="chart-wrapper h-100">
                    <div class="chart-area-header">
                        <h5 class="title">{{ __("User Analytics") }}</h5>
                    </div>
                    <div class="chart-container">
                        <div id="chart4" class="balance-chart"></div>
                    </div> 
                </div>
            </div>
        </div>
    </div>
    <div class="table-area mt-15">
        <div class="table-wrapper">
            <div class="table-header">
                <h5 class="title">{{ __("Latest Add Moeny") }}</h5>
            </div>
            <div class="table-responsive">
                <table class="custom-table">
                    <thead>
                        <tr>
                            <th>{{ __("full Name")}}</th>
                            <th>{{ __("Email")}}</th>
                            <th>{{ __("Username")}}</th>
                            <th>{{ __("Phone")}}</th>
                            <th>{{ __("Amount")}}</th>
                            <th>{{ __("Gateway")}}</th>
                            <th>{{ __("Status")}}</th>
                            <th>{{ __("Time")}}</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($latest_transactions  as $key => $item)
                            <tr>
                                <td>{{ @$item->creator->full_name }}</td>
                                <td>{{ @$item->creator->email }}</td>
                                <td>{{ @$item->creator->username }}</td>
                                <td>{{ $item->creator->full_mobile ?? '' }}</td>
                                <td>{{ get_amount($item->request_amount,$item->request_currency) }}</td>
                                <td>
                                    <span class="text--info">
                                        @if ($item?->gateway_currency?->gateway?->name ?? false)
                                            {{ $item?->gateway_currency?->gateway?->name ?? "" }}
                                        @else
                                            {{ $item->remark }}
                                        @endif
                                    </span>
                                </td>
                                <td>
                                    <span class="{{ $item->string_status->class }}">{{ $item->string_status->value }}</span>
                                </td>
                                <td>{{ $item->created_at->format('d-m-y h:i:s A') }}</td>
                                <td> 
                                    <a href="{{ setRoute('admin.add.money.details',$item->id) }}" class="btn btn--base"><i class="las la-expand"></i></a>
                                </td>
                            </tr>
                        @empty
                            @include('admin.components.alerts.empty',['colspan' => 9])
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>  
@endsection

@push('script')
    <script>

        let thisMonthsDays = '@json($this_months_days)';
        let pendingAddMoneyChart = '@json($pending_add_money_chart_data)';
        let completeAddMoneyChart = '@json($success_add_money_chart_data)';
        let rejectedAddMoneyChart = '@json($rejected_add_money_chart_data)';
        let allAddMoneyChart = '@json($all_add_money_chart_data)';

        let pendingMoneyOutChart = '@json($pending_money_out_chart_data)';
        let completeMoneyOutChart = '@json($success_money_out_chart_data)';
        let rejectedMoneyOutChart = '@json($rejected_money_out_chart_data)';
        let allMoneyOutChart = '@json($all_money_out_chart_data)';

        let thisYearMonths              = '@json($this_year_months)';
        let monthWisePendingRevenue     = '@json($month_wise_pending_revenue)';
        let monthWiseCompleteRevenue    = '@json($month_wise_complete_revenue)';
        let monthWiseRejectedRevenue    = '@json($month_wise_reject_revenue)';
        let monthWiseAllRevenue         = '@json($month_wise_all_revenue)';


        let activeUsers                  = '{{ $active_users }}';
        let bannedUsers                  = '{{ $banned_users }}';
        let emailUnverifiedUsers           = '{{ $email_unverified_users }}';

        let today_profit_amount             = '{{ $today_profit_amount }}';
        let this_profit_week_amount         = '{{ $this_profit_week_amount }}';
        let this_profit_month_amount        = '{{ $this_profit_month_amount }}';
        let this_profit_year_amount         = '{{ $this_profit_year_amount }}';

        var options = {
            series: [{
                name: 'Pending',
                color: "#5A5278",
                data: JSON.parse(pendingAddMoneyChart),
                }, {
                name: 'Completed',
                color: "#6F6593",
                data: JSON.parse(completeAddMoneyChart),
                }, {
                name: 'Canceled',
                color: "#8075AA",
                data: JSON.parse(rejectedAddMoneyChart),
                }, {
                name: 'All',
                color: "#A192D9",
                data: JSON.parse(allAddMoneyChart),
                }
            ],
            chart: {
            type: 'bar',
            height: 350,
            stacked: true,
            toolbar: {
                show: true
            },
            zoom: {
                enabled: true
            }
            },
            responsive: [{
            breakpoint: 480,
            options: {
                legend: {
                position: 'bottom',
                offsetX: -10,
                offsetY: 0
                }
            }
            }],
            plotOptions: {
            bar: {
                horizontal: false,
                borderRadius: 10
            },
            },
            xaxis: {
            type: 'datetime',
            categories: JSON.parse(thisMonthsDays),
            },
            legend: {
            position: 'bottom',
            offsetX: 40
            },
            fill: {
            opacity: 1
            }
        };

        var monthlyAddMoneyChart = new ApexCharts(document.querySelector("#chart1"), options);
        monthlyAddMoneyChart.render();

 

        var options = {
            series: [{
                name: 'Pending',
                color: "#5A5278",
                data: JSON.parse(pendingMoneyOutChart),
                }, {
                name: 'Completed',
                color: "#6F6593",
                data: JSON.parse(completeMoneyOutChart),
                }, {
                name: 'Canceled',
                color: "#8075AA",
                data: JSON.parse(rejectedMoneyOutChart),
                }, {
                name: 'All',
                color: "#A192D9",
                data: JSON.parse(allMoneyOutChart),
                }
            ],
            chart: {
            type: 'bar',
            height: 350,
            stacked: true,
            toolbar: {
                show: true
            },
            zoom: {
                enabled: true
            }
            },
            responsive: [{
            breakpoint: 480,
            options: {
                legend: {
                position: 'bottom',
                offsetX: -10,
                offsetY: 0
                }
            }
            }],
            plotOptions: {
            bar: {
                horizontal: false,
                borderRadius: 10
            },
            },
            xaxis: {
            type: 'datetime',
            categories: JSON.parse(thisMonthsDays),
            },
            legend: {
            position: 'bottom',
            offsetX: 40
            },
            fill: {
            opacity: 1
            }
        };

        var revenueChart = new ApexCharts(document.querySelector("#chart2"), options);
        revenueChart.render();


        // Investment Chart START
        var options = {
            series: [{
            name: 'Add Money',
            color: "#5A5278",
            data: [44, 55, 57, 56, 61, 58, 63, 60, 66]
            }, {
            name: 'Money Out',
            color: "#6F6593",
            data: [76, 85, 101, 98, 87, 105, 91, 114, 94]
            }, {
            name: 'Total Balance',
            color: "#8075AA",
            data: [35, 41, 36, 26, 45, 48, 52, 53, 41]
            }],
            chart: {
            type: 'bar',
            toolbar: {
                show: false
            },
            height: 325
            },
            plotOptions: {
            bar: {
                horizontal: false,
                columnWidth: '55%',
                borderRadius: 5,
                endingShape: 'rounded'
            },
            },
            dataLabels: {
            enabled: false
            },
            stroke: {
            show: true,
            width: 2,
            colors: ['transparent']
            },
            xaxis: {
                type: 'datetime',
                categories: JSON.parse(thisYearMonths),
            },
            yaxis: {
            title: {
                text: '{{ $default_currency->symbol }}'
            }
            },
            fill: {
            opacity: 1
            }
        };

        var investmentAnalytics = new ApexCharts(document.querySelector("#chart3"), options);
        investmentAnalytics.render();
        // Investment Chart END


        // User Analytics START
        var options = {
          series: [parseInt(activeUsers), parseInt(emailUnverifiedUsers), parseInt(bannedUsers)],
          chart: {
          width: 350,
          type: 'pie'
        },
        colors: ['#5A5278', '#6F6593', '#8075AA', '#A192D9'],
        labels: ['Active', 'Unverified', 'Banned'],
        responsive: [{
          breakpoint: 1480,
          options: {
            chart: {
              width: 280
            },
            legend: {
              position: 'bottom'
            }
          },
          breakpoint: 1199,
          options: {
            chart: {
              width: 380
            },
            legend: {
              position: 'bottom'
            }
          },
          breakpoint: 575,
          options: {
            chart: {
              width: 280
            },
            legend: {
              position: 'bottom'
            }
          }
        }],
        legend: {
          position: 'bottom'
        },
        };

        var chart = new ApexCharts(document.querySelector("#chart4"), options);
        chart.render();

        // Growth END

    </script>
@endpush