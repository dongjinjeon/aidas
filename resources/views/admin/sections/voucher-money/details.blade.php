@extends('admin.layouts.master')

@push('css')
@endpush

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
        'active' => __('Request Money Details'),
    ])
@endsection
@section('content')
<div class="custom-card">
    <div class="card-header">
        <h6 class="title">{{ __("Request Money Details") }}</h6>
    </div>
    <div class="card-body">
        <form class="card-form">
            <div class="row align-items-center mb-10-none">
                <div class="col-xl-4 col-lg-4 form-group">
                    <ul class="user-profile-list-two">
                        <li class="one">Date: <span>{{ @$data->created_at->format('d-m-y h:i:s A') }}</span></li>
                        <li class="two">TRX ID: <span>{{ @$data->transaction->trx_id }}</span></li>
                        <li class="three">Sender: <span>
                            @if($data->user_id != null)
                                    <a href="{{ setRoute('admin.users.details',$data->user->username) }}">{{ $data->user->fullname }} ({{ __("USER") }})</a>  
                            @endif
                            </span>
                        </li>
                        <li class="three">Receiver: <span>
                            @if($data->transaction != null)
                                    <a href="{{ setRoute('admin.users.details',$data->transaction->receiver_info->username) }}">{{ $data->transaction->receiver_info->fullname }} ({{ __("USER") }})</a>  
                            @endif
                            </span>
                        </li> 
                        <li class="five">Amount: <span>{{ get_amount(@$data->request_amount,$data->request_currency) }}</span></li>
                    </ul>
                </div>

                <div class="col-xl-4 col-lg-4 form-group">
                    <div class="user-profile-thumb">
                        <img src="{{ @get_gateway_image($data->gateway_currency->payment_gateway_id) }}" alt="payment">
                    </div>
                </div>
                <div class="col-xl-4 col-lg-4 form-group">
                    <ul class="user-profile-list two">
                        <li class="one">Fees &Charge: <span>{{ number_format(@$data->total_charge,2) }} {{ @$data->request_currency }}</span></li> 
                        <li class="three">Rate: <span>1 {{ $data->request_currency }} = {{ number_format(@$data->exchange_rate,2) }} {{ @$data->request_currency }}</span></li>
                        <li class="four">Will Get: <span>{{ get_amount(@$data->request_amount,$data->request_currency) }}</span></li>
                        <li class="four">Payable: <span>{{ get_amount(@$data->total_payable,$data->request_currency) }}</span></li>
                        <li class="five">Status:  <span class="{{ @$data->stringStatus->class }}">{{ @$data->stringStatus->value }}</span></li>
                    </ul>
                </div>
            </div>
        </form>
    </div>
</div> 

@endsection
 