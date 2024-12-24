<div class="dashboard-list-wrapper">
   @forelse ($transactions as $item)
   <div class="dashboard-list-item-wrapper">
    <div class="dashboard-list-item {{ $item->status == 1 ? "receive": "sent" }} ">
        <div class="dashboard-list-left">
            <div class="dashboard-list-user-wrapper">
                <div class="dashboard-list-user-icon">
                    <i class="las la-dollar-sign"></i>
                </div>
                <div class="dashboard-list-user-content">
                    @if ($item->type == payment_gateway_const()::TYPEADDMONEY)
                    <h4 class="title">{{ __("Add Balance via") }} <span class="text--warning">{{ $item->gateway_currency->name }}</span></h4>
                    @elseif ($item->type == payment_gateway_const()::TYPEWITHDRAW)
                        <h4 class="title">{{ __("Withdraw Money via") }} <span class="text--warning">{{ $item->gateway_currency->name }}</span></h4>
                    @elseif ($item->type == payment_gateway_const()::TYPEMONEYEXCHANGE)
                    <h4 class="title">{{ __("Exchange Money") }} <span class="text--warning">{{ $item->request_currency }} To {{ $item->details->charges->exchange_currency }}</span></h4>
                    @elseif ($item->type == payment_gateway_const()::TYPETRANSFERMONEY)
                        @if ($item->isAuthUser()) 
                            <h4 class="title">{{ __("Send Money to ") }} <span class="text--warning">{{ $item->receiver_info->username }}</span></h4>
                        @else
                            <h4 class="title">{{ __("Received Money from ") }} <span class="text--warning">{{ $item->user->username }}</span></h4>
                        @endif
                    @elseif ($item->type == payment_gateway_const()::REQUESTMONEY)
                        @if ($item->user_id != auth()->user()->id)
                        <h4 class="title">{{ __("Request Money Payment Received") }}</h4>
                        @else
                        <h4 class="title">{{ __("Request Money Payment") }}</h4> 
                        @endif
                    
                    @elseif ($item->type == payment_gateway_const()::TYPEADDSUBTRACTBALANCE)
                    <h4 class="title">{{ __("Balance Update From Admin (".$item->request_currency.")") }} </h4>
                    @elseif ($item->type == payment_gateway_const()::REDEEMVOUCHER)
                    <h4 class="title">{{ __("Redeem Voucher") }} </h4>
                    @elseif ($item->type == payment_gateway_const()::TYPEMAKEPAYMENT)
                    <h4 class="title">{{ __("Make Payment Vai") }} Walletium </h4>
                    @endif
                    <span class="{{ $item->stringStatus->class }}">{{ $item->stringStatus->value }} </span>
                </div>
            </div>
        </div>
        <div class="dashboard-list-right">
            @if ($item->type == payment_gateway_const()::TYPEADDMONEY)
            <h4 class="main-money text--base">{{ get_amount($item->request_amount,$item->request_currency) }}</h4>
            <h6 class="exchange-money">{{ get_amount($item->total_payable,$item->gateway_currency->currency_code) }}</h6>
            @elseif($item->type == payment_gateway_const()::TYPEWITHDRAW)
            <h4 class="main-money text--base"> {{ get_amount($item->total_payable,$item->gateway_currency->currency_code) }}</h4>
            <h6 class="exchange-money">{{ get_amount($item->request_amount,$item->request_currency) }}</h6>
            @elseif($item->type == payment_gateway_const()::TYPEMONEYEXCHANGE)
            <h4 class="main-money text--base"> {{ get_amount($item->receive_amount,$item->payment_currency) }}</h4>
            <h6 class="exchange-money">{{ get_amount($item->request_amount,$item->request_currency) }}</h6>   
            @elseif ($item->type == payment_gateway_const()::TYPETRANSFERMONEY)
            <h4 class="main-money text--base"> {{ get_amount($item->receive_amount,$item->payment_currency) }}</h4>
            <h6 class="exchange-money">{{ get_amount($item->request_amount,$item->user_wallet->currency->code) }}</h6>
            @elseif ($item->type == payment_gateway_const()::REQUESTMONEY)
            <h4 class="main-money text--base">{{ get_amount($item->request_amount,$item->request_currency) }}</h4>
            <h6 class="exchange-money">{{ get_amount($item->total_payable,$item->payment_currency) }}</h6>
            @elseif ($item->type == payment_gateway_const()::TYPEADDSUBTRACTBALANCE)
            <h4 class="main-money text--base">{{ get_amount($item->request_amount,$item->request_currency) }}</h4>
            <h6 class="exchange-money">{{ get_amount($item->available_balance,$item->request_currency) }}</h6>
            @elseif ($item->type == payment_gateway_const()::REDEEMVOUCHER)
            <h4 class="main-money text--base">{{ get_amount($item->request_amount,$item->request_currency) }}</h4>
            <h6 class="exchange-money">{{ get_amount($item->total_payable,$item->request_currency) }}</h6>
            @elseif ($item->type == payment_gateway_const()::TYPEMAKEPAYMENT)
            <h4 class="main-money text--base">{{ get_amount($item->request_amount,$item->request_currency) }}</h4>
            <h6 class="exchange-money">{{ get_amount($item->total_payable,$item->request_currency) }}</h6>
     
        @endif
        </div>
    </div>
    <div class="preview-list-wrapper">
        @if ($item->voucher != null)
        <div class="preview-list-item">
            <div class="preview-list-left">
                <div class="preview-list-user-wrapper">
                    <div class="preview-list-user-icon">
                        <i class="lab la-tumblr"></i>
                    </div>
                    <div class="preview-list-user-content">
                        <span>{{ __('Voucher Code') }}</span>
                    </div>
                </div>
            </div>
            <div class="preview-list-right">
                <span>{{ $item->voucher->code }}</span>
            </div>
        </div> 
        @endif 
        <div class="preview-list-item">
            <div class="preview-list-left">
                <div class="preview-list-user-wrapper">
                    <div class="preview-list-user-icon">
                        <i class="lab la-tumblr"></i>
                    </div>
                    <div class="preview-list-user-content">
                        <span>{{ __('Transaction ID') }}</span>
                    </div>
                </div>
            </div>
            <div class="preview-list-right">
                <span>{{ $item->trx_id }}</span>
            </div>
        </div>
        <div class="preview-list-item">
            <div class="preview-list-left">
                <div class="preview-list-user-wrapper">
                    <div class="preview-list-user-icon">
                        <i class="las la-exchange-alt"></i>
                    </div>
                    <div class="preview-list-user-content">
                        <span>{{ __("Exchange Rate") }}</span>
                    </div>
                </div>
            </div>
            <div class="preview-list-right">
                @if ($item->type == payment_gateway_const()::TYPEADDMONEY || $item->type == payment_gateway_const()::TYPEWITHDRAW)
                <span>1 {{ $item->request_currency }} = {{ get_amount($item->exchange_rate,$item->gateway_currency->currency_code,3) }}</span> 
                @elseif ($item->type == payment_gateway_const()::TYPEMONEYEXCHANGE)
                <span>1 {{ $item->request_currency }} = {{ get_amount($item->exchange_rate,$item->details->charges->exchange_currency,3) }}</span> 
                @elseif ($item->type == payment_gateway_const()::TYPETRANSFERMONEY)
                <span>1 {{ $item->request_currency }} = {{ get_amount($item->exchange_rate,$item->payment_currency) }}</span>
                @elseif ($item->type == payment_gateway_const()::REQUESTMONEY)
                <span>1 {{ $item->request_currency }} = 1 {{ $item->payment_currency }}</span>
                @elseif ($item->type == payment_gateway_const()::TYPEADDSUBTRACTBALANCE)
                <span>1 {{ $item->request_currency }} = 1 {{ $item->request_currency }}</span>
                @elseif ($item->type == payment_gateway_const()::REDEEMVOUCHER)
                <span>1 {{ $item->request_currency }} = 1 {{ $item->request_currency }}</span>
                @elseif ($item->type == payment_gateway_const()::TYPEMAKEPAYMENT)
                <span>1 {{ $item->request_currency }} = 1 {{ $item->request_currency }}</span>

                @endif
                
            </div>
        </div> 
        <div class="preview-list-item">
            <div class="preview-list-left">
                <div class="preview-list-user-wrapper">
                    <div class="preview-list-user-icon">
                        <i class="las la-battery-half"></i>
                    </div>
                    <div class="preview-list-user-content">
                        <span>{{ __("Fees & Charge") }}</span>
                    </div>
                </div>
            </div>
            <div class="preview-list-right">
                @if ($item->type == payment_gateway_const()::TYPEADDMONEY || $item->type == payment_gateway_const()::TYPEWITHDRAW)
                <span>{{ @get_amount($item->total_charge,$item->gateway_currency->currency_code) }}</span>
                @elseif ($item->type == payment_gateway_const()::TYPEMONEYEXCHANGE)
                <span>{{ get_amount($item->total_charge,$item->request_currency) }}</span> 
                @elseif ($item->type == payment_gateway_const()::TYPETRANSFERMONEY)
                <span>{{ get_amount($item->total_charge,$item->request_currency) }}</span>
                @elseif ($item->type == payment_gateway_const()::REQUESTMONEY)
                <span>{{ get_amount($item->total_charge,$item->request_currency) }}</span>
                @elseif ($item->type == payment_gateway_const()::REDEEMVOUCHER)
                <span>{{ get_amount($item->total_charge,$item->request_currency) }}</span>
                @elseif ($item->type == payment_gateway_const()::TYPEADDSUBTRACTBALANCE)
                <span>0 {{ $item->request_currency }}</span>
                @elseif ($item->type == payment_gateway_const()::TYPEMAKEPAYMENT)
                <span>{{ get_amount($item->total_charge,$item->request_currency) }}</span>
                @endif
            </div>
        </div> 
        @if ($item->type == payment_gateway_const()::REQUESTMONEY)
        <div class="preview-list-item">
            <div class="preview-list-left">
                <div class="preview-list-user-wrapper"> 
                    <div class="preview-list-user-icon">
                        <i class="las la-user"></i>
                    </div>
                    <div class="preview-list-user-content">
                        <span>{{ __('Paid By') }}</span>
                    </div>
                </div>
            </div> 
            <div class="preview-list-right">
                <span>{{ $item->user->email }}</span>
            </div>
        </div> 
        @endif
        @if ($item->remark != null)
        <div class="preview-list-item">
            <div class="preview-list-left">
                <div class="preview-list-user-wrapper"> 
                    <div class="preview-list-user-icon">
                        <i class="las la-smoking"></i>
                    </div>
                    <div class="preview-list-user-content">
                        <span>{{ __('Remarks') }}</span>
                    </div>
                </div>
            </div> 
            <div class="preview-list-right">
                <span>{{ $item->remark }}</span>
            </div>
        </div> 
        @endif
        @if ($item->reject_reason != null)
        <div class="preview-list-item">
            <div class="preview-list-left">
                <div class="preview-list-user-wrapper"> 
                    <div class="preview-list-user-icon">
                        <i class="las la-smoking"></i>
                    </div>
                    <div class="preview-list-user-content">
                        <span>{{ __('Reject Reason') }}</span>
                    </div>
                </div>
            </div> 
            <div class="preview-list-right">
                <span>{{ $item->reject_reason }}</span>
            </div>
        </div> 
        @endif
        <div class="preview-list-item">
            <div class="preview-list-left">
                <div class="preview-list-user-wrapper"> 
                    <div class="preview-list-user-icon">
                        <i class="las la-clock"></i>
                    </div>
                    <div class="preview-list-user-content">
                        <span>{{ __('Date') }}</span>
                    </div>
                </div>
            </div> 
            <div class="preview-list-right">
                <span>{{ $item->created_at }}</span>
            </div>
        </div> 
        @if ($item->type == payment_gateway_const()::TYPEADDMONEY)
        @if ($item->gateway_currency->gateway->isTatum($item->gateway_currency->gateway) && $item->status == payment_gateway_const()::STATUSWAITING)
            <div class="preview-list-item d-block">
                <div class="preview-list-left">
                    <div class="preview-list-user-wrapper">
                        <div class="preview-list-user-icon">
                            <i class="las la-times-circle"></i>
                        </div>
                        <div class="preview-list-user-content">
                            <span>{{ __("Txn Hash") }}</span>
                        </div>
                    </div>
                    <form action="{{ setRoute('user.add.money.payment.crypto.confirm', $item->trx_id) }}" method="POST">
                        @csrf
                        @php
                            $input_fields = $item->details->payment_info->requirements ?? [];
                        @endphp

                        @foreach ($input_fields as $input)
                            <div class="mt-2">
                                <input type="text" class="form-control" name="{{ $input->name }}" placeholder="{{ $input->placeholder ?? "" }}" required>
                            </div>
                        @endforeach

                        <div class="text-end">
                            <button type="submit" class="btn--base my-2">{{ __("Process") }}</button>
                        </div>

                    </form>
                </div>
            </div>
        @endif
        @endif
    </div>
</div>
   @empty
   <div class="alert alert-primary" style="margin-top: 37.5px; text-align:center">{{ __('No data found!') }}</div>
   @endforelse
   {{ get_paginate($transactions) }}
</div>