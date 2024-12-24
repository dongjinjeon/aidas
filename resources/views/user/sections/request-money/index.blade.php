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
<div class="row mb-20-none">
    <div class="col-xl-7 col-lg-7 mb-20">
        <div class="custom-card mt-10">
            <div class="dashboard-header-wrapper">
                <h4 class="title">{{ __("Request Money") }}</h4>
            </div>
            <div class="card-body">
                <form action="{{ setRoute('user.request.money.submit') }}" method="POST" class="card-form">
                    @csrf
                    <div class="row">
                        <div class="col-xl-12 col-lg-12 form-group add-money">
                            <label>{{ __("Request Amount") }}<span>*</span></label>
                            <input type="number" name="amount" class="form--control" placeholder="Enter Amount">
                            <div class="currency">
                                <select class="nice-select" name="request_currency">
                                    @foreach ($user_wallets ?? [] as $item)
                                    <option
                                    value="{{ $item->currency->code }}"
                                    data-id="{{ $item->currency->id }}"
                                    data-rate="{{ $item->currency->rate }}"
                                    data-symbol="{{ $item->currency->symbol }}"
                                    data-type="{{ $item->currency->type }}"
                                    data-balance="{{ $item->balance }}"
                                    {{ get_default_currency_code() == $item->currency->code ? "selected": "" }}
                                    >{{ $item->currency->code }}</option> 
                                    @endforeach 
                                </select>
                            </div>
                            <code class="d-block mt-10 text-end walletBalanceShow"></code>
                        </div>
                        <div class="col-xl-12 col-lg-12 form-group">
                            <label>{{ __("Remarks") }} <span class="text--base">({{ __("Optional") }})</span></label>
                            <textarea class="form--control" name="remark" placeholder="{{ __("Explain Request Purposes Here") }}…"></textarea>
                        </div>
                        <div class="col-xl-12 col-lg-12 form-group">
                            <div class="note-area">
                                <code class="d-block limit-show">--</code>
                                <code class="d-block fees-show">--</code>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-12 col-lg-12">
                        <button type="submit" class="btn--base w-100">{{ __("Request Money") }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div class="col-xl-5 col-lg-5 mb-20">
        <div class="custom-card mt-10">
            <div class="dashboard-header-wrapper">
                <h4 class="title">{{ __("Summary") }}</h4>
            </div>
            <div class="card-body">
                <div class="preview-list-wrapper">
                    <div class="preview-list-item">
                        <div class="preview-list-left">
                            <div class="preview-list-user-wrapper">
                                <div class="preview-list-user-icon">
                                    <i class="las la-receipt"></i>
                                </div>
                                <div class="preview-list-user-content">
                                    <span>{{ __("Entered Amount") }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="preview-list-right">
                            <span class="text--success request-amount">--</span>
                        </div>
                    </div>
                    <div class="preview-list-item">
                        <div class="preview-list-left">
                            <div class="preview-list-user-wrapper">
                                <div class="preview-list-user-icon">
                                    <i class="las la-battery-half"></i>
                                </div>
                                <div class="preview-list-user-content">
                                    <span>{{ __("Total Fees & Charges") }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="preview-list-right">
                            <span class="text--warning fees">--</span>
                        </div>
                    </div>
                    <div class="preview-list-item">
                        <div class="preview-list-left">
                            <div class="preview-list-user-wrapper">
                                <div class="preview-list-user-icon">
                                    <i class="lab la-get-pocket"></i>
                                </div>
                                <div class="preview-list-user-content">
                                    <span>{{ __("Will Get") }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="preview-list-right">
                            <span class="text--danger will-get"></span>
                        </div>
                    </div>
                    <div class="preview-list-item">
                        <div class="preview-list-left">
                            <div class="preview-list-user-wrapper">
                                <div class="preview-list-user-icon">
                                    <i class="las la-money-check-alt"></i>
                                </div>
                                <div class="preview-list-user-content">
                                    <span class="last">{{ __("Total Payable Amount") }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="preview-list-right">
                            <span class="text--info last pay-in-total">--</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
  <div class="dashboard-list-area mt-60">
    <div class="dashboard-header-wrapper">
        <h4 class="title">{{ __("Latest Transactions") }}</h4>
        <div class="dashboard-btn-wrapper">
            <div class="dashboard-btn">
                <a href="{{ setRoute('user.transactions.request.money') }}" class="btn--base">{{ __("View More") }}</a>
            </div>
        </div>
    </div>
    @include('user.components.wallets.request-money-transation-log', compact('transactions'))
  </div>
</div>
@endsection
@push('script')
    <script>
        var fixedCharge     = "{{ $charges->fixed_charge ?? 0 }}";
        var percentCharge   = "{{ $charges->percent_charge ?? 0 }}";
        var minLimit        = "{{ $charges->min_limit ?? 0 }}";
        var maxLimit        = "{{ $charges->max_limit ?? 0 }}";
        $(document).ready(function(){ 
            walletBalance();
            getLimit();
            getFees();
            getPreview();
        }); 
        $('select[name=request_currency]').on('change',function(){    
            walletBalance();
            getLimit();
            getFees();
            getPreview();
        });
        
        $("input[name=amount]").keyup(function(){
                getFees();
                getPreview();
        }); 
        function walletBalance(){ 
            $('.walletBalanceShow').html("Available balance: " + $("select[name=request_currency] :selected").attr("data-symbol") + parseFloat($("select[name=request_currency] :selected").attr("data-balance")).toFixed(2));
        }
         //minimum and maxmimum money limite
         function getLimit(){
            var senderCurrencyCode =  acceptVar().senderCurrencyCode; 
            var exchangeRate = parseFloat(acceptVar().senderRate);
            var min_limit = minLimit;
            var max_limit = maxLimit;
            
            var min_limit_calc = parseFloat(min_limit*exchangeRate);
            var max_limit_clac = parseFloat(max_limit*exchangeRate);
            $('.limit-show').html("Limit: " + min_limit_calc.toFixed(2) + " " + senderCurrencyCode + " - " + max_limit_clac.toFixed(2) + " " + senderCurrencyCode);

        }

        // get variables 
        function acceptVar() {
            var senderCurrencyCode = $("select[name=request_currency] :selected").val();
            var senderRate = $("select[name=request_currency] :selected").attr("data-rate"); 

            return {
                    senderCurrencyCode:senderCurrencyCode,
                    senderRate:senderRate,
            };
        }

         //calculate fees 
         function feesCalculation(){
            var senderAmount = $("input[name=amount]").val();
            var senderCurrencyCode =  acceptVar().senderCurrencyCode;
            var senderRate =  acceptVar().senderRate;

            var fixedChargeCalculation = parseFloat(senderRate)*fixedCharge;
            var percentChargeCalculation = parseFloat(percentCharge/100)*parseFloat(senderAmount*1);
            var totalCharge = fixedChargeCalculation+percentChargeCalculation;

            return {
                fixed_charge: fixedChargeCalculation,
                percent_charge: percentChargeCalculation,
                total_charge: totalCharge,
            };

        }
        function getFees() {
            var senderCurrencyCode =  acceptVar().senderCurrencyCode;
            var charges = feesCalculation();
            $('.fees-show').html("Charge: " + parseFloat(charges.fixed_charge).toFixed(2) + " " + senderCurrencyCode +" + " + parseFloat(percentCharge) + "%" + " = "+ parseFloat(charges.total_charge).toFixed(2) + " " + senderCurrencyCode);
        }

        function getPreview() {
                var senderAmount = $("input[name=amount]").val();
                
                var charges = feesCalculation();
                
                var senderRate = acceptVar().senderRate; 
                // var receiver_currency = acceptVar().rCurrency;
                senderAmount == "" ? senderAmount = 0 : senderAmount = senderAmount;

                // Sending Amount
                $('.request-amount').text(parseFloat(senderAmount).toFixed(2) + " " + acceptVar().senderCurrencyCode);

                // Fees
                $('.fees').text(parseFloat(charges.total_charge).toFixed(2) + " " + acceptVar().senderCurrencyCode);

                // will get amount
                $('.will-get').text(parseFloat(senderAmount).toFixed(2) + " " + acceptVar().senderCurrencyCode);

                // Pay In Total 
                var receiverAmount = parseFloat(senderAmount)+parseFloat(charges.total_charge);
                $('.pay-in-total').text(parseFloat(receiverAmount).toFixed(2) + " " + acceptVar().senderCurrencyCode);

            }
    </script>
@endpush