@extends('admin.layouts.master')

@push('css')

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
    ], 'active' => __("Make Payment Logs")])
@endsection

@section('content')
    <div class="table-area">
        <div class="table-wrapper">
            <div class="table-header">
                <h5 class="title">{{ __("All Logs") }}</h5>
            </div>
            <div class="table-responsive">
                <table class="custom-table">
                    <thead>
                        <tr>
                            <th>Trx ID</th>
                            <th>Email</th>
                            <th>Username</th>
                            <th>User Type</th>
                            <th>Amount</th>
                            <th>User Type</th>
                            <th>Status</th>
                            <th>Time</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($transactions  as $key => $item)
                            <tr>
                                <td>{{ $item->trx_id }}</td>
                                <td>{{ @$item->tran_creator->email }}</td>
                                <td>{{ @$item->tran_creator->username }}</td>
                                <td>{{ @$item->tran_creator->type }}</td>
                                <td>{{ get_amount($item->request_amount,$item->request_currency) }}</td>
                                <td><span class="text--info">Sender</td>
                                <td>
                                    <span class="{{ $item->string_status->class }}">{{ $item->string_status->value }}</span>
                                </td>
                                <td>{{ $item->created_at->format('d-m-y h:i:s A') ?? "" }}</td>
                                <td> 
                                    @include('admin.components.link.custom',[
                                        'href'          => setRoute('admin.make.payment.details',$item->trx_id),
                                        'permission'    => "admin.make.payment.details",
                                        'class'         => "btn btn--base",
                                        'icon'          => "las la-expand",
                                    ])
                                </td>
                            </tr>
                        @empty
                            @include('admin.components.alerts.empty',['colspan' => 10])
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection

@push('script')
    
@endpush