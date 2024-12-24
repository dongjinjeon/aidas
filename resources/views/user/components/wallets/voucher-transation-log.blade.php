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
                     <h4 class="title">{{ __("Redeem Voucher") }}</h4>
                     <span class="{{ $item->stringStatus->class }}">{{ $item->stringStatus->value }} </span>
                 </div>
             </div>
         </div>
         <div class="dashboard-list-right">
             <h4 class="main-money text--base">{{ get_amount($item->request_amount,$item->request_currency) }}</h4>
             <h6 class="exchange-money">{{ get_amount($item->total_payable,$item->request_currency) }}</h6>
         </div>
     </div>
     <div class="preview-list-wrapper">
         <div class="preview-list-item">
             <div class="preview-list-left">
                 <div class="preview-list-user-wrapper">
                     <div class="preview-list-user-icon">
                         <i class="lab la-tumblr"></i>
                     </div>
                     <div class="preview-list-user-content">
                         <span>{{ __('Code') }}</span>
                     </div>
                 </div>
             </div>
             <div class="preview-list-right">
                 <span>{{ $item->code }}</span>
             </div>
         </div>
         <div class="preview-list-item">
             <div class="preview-list-left">
                 <div class="preview-list-user-wrapper">
                     <div class="preview-list-user-icon">
                         <i class="las la-user"></i>
                     </div>
                     <div class="preview-list-user-content">
                         <span>{{ __('Created By') }}</span>
                     </div>
                 </div>
             </div>
             <div class="preview-list-right">
                 <span>{{ $item->user->email }}</span>
             </div>
         </div>
         <div class="preview-list-item">
             <div class="preview-list-left">
                 <div class="preview-list-user-wrapper">
                     <div class="preview-list-user-icon">
                         <i class="lab la-tumblr"></i>
                     </div>
                     <div class="preview-list-user-content">
                         <span>{{ __('Amount') }}</span>
                     </div>
                 </div>
             </div>
             <div class="preview-list-right">
                 <span>{{ get_amount($item->request_amount,$item->request_currency,2) }}</span>
             </div>
         </div>
         @if ($item->transaction != null)
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
                <span>{{ $item->transaction->trx_id }}</span>
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
                 <span>1 {{ $item->request_currency }} = {{ get_amount($item->transaction->exchange_rate,$item->request_currency,3) }}</span> 
             </div>
         </div>  
         @endif
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
                 <span>{{ @get_amount($item->total_charge,$item->request_currency) }}</span>
             </div>
         </div>  
         <div class="preview-list-item">
             <div class="preview-list-left">
                 <div class="preview-list-user-wrapper">
                     <div class="preview-list-user-icon">
                         <i class="las la-battery-half"></i>
                     </div>
                     <div class="preview-list-user-content">
                         <span>{{ __("Total Payable") }}</span>
                     </div>
                 </div>
             </div>
             <div class="preview-list-right">
                 <span>{{ @get_amount($item->total_payable,$item->request_currency) }}</span>
             </div>
         </div>  
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
         @if ($item->status == 2)
         <div class="preview-list-item">
            <div class="preview-list-left">
                <div class="preview-list-user-wrapper"> 
                    <div class="preview-list-user-icon">
                        <i class="las la-smoking"></i>
                    </div>
                    <div class="preview-list-user-content">
                        <span>{{ __('Action') }}</span>
                    </div>
                </div>
            </div> 
            <div class="preview-list-right">
                <a href="{{ setRoute('user.my-voucher.cancel',$item->code) }}" class="btn btn--warning text-white">{{ __("Cancel") }}</a>
            </div>
        </div> 
        @endif
     </div>
 </div>
    @empty
    <div class="alert alert-primary" style="margin-top: 37.5px; text-align:center">{{ __('No data found!') }}</div>
    @endforelse
    {{ get_paginate($transactions) }}
 </div>