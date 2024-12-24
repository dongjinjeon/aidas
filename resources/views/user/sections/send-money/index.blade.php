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
<div class="row mb-20-none justify-content-center">
    <div class="col-xl-6 col-lg-6 col-md-8 mb-20">
        <div class="banner-form-wrapper sending-form">
            <div class="exchange-area text-center mb-20">
                <code class="d-block text-center"><span>{{ __("Exchange Rate") }}</span><span class="rate-show"></span></code>
            </div>
            <form class="banner-form" method="POST" action="{{ setRoute('user.send.money.submit') }}">
                @csrf
                 <div class="form-area">
                    <div class="form-group">
                        <label>{{ __("Sender Amount") }}<span>*</span></label>
                        <div class="input-group">
                            <input type="text" name="sender_amount" class="form--control" value="{{ old('sender_amount') }}" id="amountInput" placeholder="{{ __('Enter Amount') }}">
                            <div class="ad-select">
                                <div class="custom-select">
                                    <div class="custom-select-inner">
                                        <input type="hidden" name="sender_currency" value="{{ $user_wallets[0]->currency->code }}">
                                        <img src="{{ get_image($user_wallets[0]->currency->flag, 'currency-flag') }}" alt="flag" class="custom-flag">
                                        <span class="custom-currency">{{ $user_wallets[0]->currency->code }}</span>
                                    </div>
                                </div>
                                <div class="custom-select-wrapper">
                                    <div class="custom-select-search-box">
                                        <div class="custom-select-search-wrapper">
                                            <button type="submit" class="search-btn"><i class="las la-search"></i></button>
                                            <input type="text" class="form--control custom-select-search" placeholder="{{ __("Enter a country or currency") }}...">
                                        </div>
                                    </div>
                                    <div class="custom-select-list-wrapper">
                                        <ul class="custom-select-list">
                                            @foreach ($user_wallets as $key => $item)
                                            <li class="custom-option {{ $key == 0 ? 'active' : ''}}" data-item='{{ json_encode($item->currency) }}'>
                                                <img src="{{ get_image($item->currency->flag,'currency-flag') }}" alt="flag" class="custom-flag">
                                                <span class="custom-country">{{ $item->currency->country }}</span>
                                                <span class="custom-currency">{{ $item->currency->code }}</span>
                                            </li> 
                                            @endforeach 
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="text-end"> 
                            <code class="d-block mt-10 balance-show">--</code>
                        </div>
                    </div>
                 </div>
                 <div class="form-area">
                    <div class="form-group">
                        <label>{{ __("Recipients Amount") }}<span>*</span></label>
                        <div class="input-group">
                            <input type="text" name="receiver_amount" class="form--control" value="{{ old('receiver_amount') }}" placeholder="{{ __('Enter Amount') }}" readonly>
                       
                            <div class="ad-select">
                                <div class="custom-select">
                                    <div class="custom-select-inner">
                                        <input type="hidden" name="receiver_currency" value="{{ $receiver_wallets[0]->code }}"> 
                                        <img src="{{ get_image($receiver_wallets[0]->flag, 'currency-flag') }}" alt="flag" class="custom-flag">
                                        <span class="custom-currency">{{ $receiver_wallets[0]->code }}</span>
                                    </div>
                                </div>
                                <div class="custom-select-wrapper">
                                    <div class="custom-select-search-box">
                                        <div class="custom-select-search-wrapper">
                                            <button type="submit" class="search-btn"><i class="las la-search"></i></button>
                                            <input type="text" class="form--control custom-select-search" placeholder="{{ __("Enter a country or currency") }}...">
                                        </div>
                                    </div>
                                    <div class="custom-select-list-wrapper">
                                        <ul class="custom-select-list">
                                            @foreach ($receiver_wallets as $key => $item)
                                            <li class="custom-option {{ $key == 0 ? 'active' : ''}}" data-item='{{ json_encode($item) }}'>
                                                <img src="{{ get_image($item->flag,'currency-flag') }}" alt="flag" class="custom-flag">
                                                <span class="custom-country">{{ $item->country }}</span>
                                                <span class="custom-currency">{{ $item->code }}</span>
                                            </li> 
                                            @endforeach 
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                 </div>
                 <div class="d-flex justify-content-between">
                    <code class="d-block mt-10 limit-show">--</code>
                    <code class="d-block mt-10 fees-show">--</code> 
                </div>
                 <div class="sending-btn pt-3">
                    <button class="btn--base w-100">{{ __("Send Money") }}</button>
                 </div>
            </form>
        </div>
    </div> 
    <div class="col-xl-6 col-lg-6 mb-20">
        <div class="custom-card mt-10">
            <div class="dashboard-header-wrapper">
                <h5 class="title">{{ __("Summary") }}</h5>
            </div>
            <div class="card-body">
                <div class="preview-list-wrapper">
                    <div class="preview-list-item">
                        <div class="preview-list-left">
                            <div class="preview-list-user-wrapper">
                                <div class="preview-list-user-icon">
                                    <i class="las la-wallet"></i>
                                </div>
                                <div class="preview-list-user-content">
                                    <span>{{ __("Sender Wallet") }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="preview-list-right">
                            <span><span class="text--base sender-currency">--</span></span>
                        </div>
                    </div>
                    <div class="preview-list-item">
                        <div class="preview-list-left">
                            <div class="preview-list-user-wrapper">
                                <div class="preview-list-user-icon">
                                    <i class="las la-wallet"></i>
                                </div>
                                <div class="preview-list-user-content">
                                    <span>{{ __("Receiver Wallet") }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="preview-list-right">
                            <span><span class="text--base receiver-currency">--</span></span>
                        </div>
                    </div>
                    <div class="preview-list-item">
                        <div class="preview-list-left">
                            <div class="preview-list-user-wrapper">
                                <div class="preview-list-user-icon">
                                    <i class="las la-receipt"></i>
                                </div>
                                <div class="preview-list-user-content">
                                    <span>{{ __("Sending Amount") }}</span>
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
                                    <i class="las la-exchange-alt"></i>
                                </div>
                                <div class="preview-list-user-content">
                                    <span>{{ __("Exchange Rate") }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="preview-list-right">
                            <span class="rate-show">--</span>
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
                                    <span>{{ __("Receiver Will Get") }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="preview-list-right">
                            <span class="text--danger receive-amount">--</span>
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
<div class="dashboard-list-area mt-60 mb-30">
    <div class="dashboard-header-wrapper">
        <h4 class="title">{{ __("Latest Transactions") }}</h4>
        <div class="dashboard-btn-wrapper">
            <div class="dashboard-btn">
                <a href="{{ setRoute('user.transactions.index','send-money-log') }}" class="btn--base">{{ __("View More") }}</a>
            </div>
        </div>
    </div> 
</div>
@include('user.components.wallets.transation-log', compact('transactions'))
@endsection
@push('script')
    <script>
        var userBalanceRoute = "{{ setRoute('user.wallets.balance') }}"; 
        var fixedCharge     = "{{ $charges->fixed_charge ?? 0 }}";
        var percentCharge   = "{{ $charges->percent_charge ?? 0 }}";
        var minLimit        = "{{ $charges->min_limit ?? 0 }}";
        var maxLimit        = "{{ $charges->max_limit ?? 0 }}";

        function setAdSelectInputValue(data) {
            var data = JSON.parse(data);
            return data.code;
        }

        function adSelectActiveItem(input) {
            var adSelect        = $(input).parents(".ad-select");
            var selectedItem    = adSelect.find(".custom-option.active");
            if(selectedItem.length > 0) {
                return selectedItem.attr("data-item");
            }
            return false;
        }

        function run(selectedItem, receiver = false, userBalance = true) {
            if(selectedItem == false) { 
                return false;
            }
            if(selectedItem.length == 0) { 
                return false;
            }

            function acceptVar() {
                var senderCurrency                  = selectedItem.code ?? "";
                var senderCountry                   = selectedItem.name ?? "";
                var senderSymbol                    = selectedItem.symbol ?? "";
                var senderCurrency_rate             = selectedItem.rate ?? 0;
                var senderCurrency_minLimit         = minLimit ?? 0;
                var senderCurrency_maxLimit         = maxLimit ?? 0;
                var senderCurrency_percentCharge    = percentCharge ?? 0;
                var senderCurrency_fixedCharge      = fixedCharge ?? 0;

                var receiverCurrency                = receiver.code ? receiver.code : "";
                var receiverCountry                 = receiver.name ? receiver.name : "";
                var receiverCurrencyRate            = receiver.rate ? receiver.rate : 0;

                return {
                    sCurrency: senderCurrency,
                    sCountry: senderCountry,
                    sSymbol: senderSymbol,
                    sCurrency_rate: senderCurrency_rate,
                    sCurrency_minLimit: senderCurrency_minLimit,
                    sCurrency_maxLimit: senderCurrency_maxLimit,
                    sCurrency_percentCharge: senderCurrency_percentCharge,
                    sCurrency_fixedCharge: senderCurrency_fixedCharge,
                    rCurrency           : receiverCurrency,
                    rCountry            : receiverCountry,
                    rCurrency_rate      : receiverCurrencyRate,
                };
            }

            function receiveAmount() {
                var senderAmount = $("input[name=sender_amount]").val();
                var exchangeRate = getExchangeRate();

                if(senderAmount == "" || !$.isNumeric(senderAmount)) {
                    senderAmount = 0;
                }

                var receiverCurrency = acceptVar().rCurrency;
                var receiveAmount = parseFloat(senderAmount) * parseFloat(exchangeRate);
                $("input[name=receiver_amount]").val(parseFloat(receiveAmount).toFixed(2));
                return receiveAmount;
            }

            function getLimit() {
                var sender_currency = acceptVar().sCurrency;
                var sender_currency_rate = acceptVar().sCurrency_rate;
                var min_limit = acceptVar().sCurrency_minLimit;
                var max_limit = acceptVar().sCurrency_maxLimit

                if($.isNumeric(min_limit) && $.isNumeric(max_limit)) {
                    var min_limit_calc = parseFloat(min_limit*sender_currency_rate).toFixed(2);
                    var max_limit_clac = parseFloat(max_limit*sender_currency_rate).toFixed(2);
                    $('.limit-show').html("Limit " + min_limit_calc + " " + sender_currency + " - " + max_limit_clac + " " + sender_currency);
                    return {
                        minLimit:min_limit_calc,
                        maxLimit:max_limit_clac,
                    };
                }else {
                    $('.limit-show').html("--");
                    return {
                        minLimit:0,
                        maxLimit:0,
                    };
                }
            }
            getLimit();

            function feesCalculation(){
                var sender_currency = acceptVar().sCurrency;
                var sender_currency_rate = acceptVar().sCurrency_rate;
                var sender_amount = $("input[name=sender_amount]").val();
                sender_amount == "" ? sender_amount = 0 : sender_amount = sender_amount;

                var fixed_charge = acceptVar().sCurrency_fixedCharge;
                var percent_charge = acceptVar().sCurrency_percentCharge;

                if($.isNumeric(percent_charge) && $.isNumeric(fixed_charge) && $.isNumeric(sender_amount)) {
                    // Process Calculation
                    var fixed_charge_calc = parseFloat(sender_currency_rate*fixed_charge);
                    var percent_charge_calc  = (parseFloat(sender_amount) / 100) * parseFloat(percent_charge);
                    var total_charge = parseFloat(fixed_charge_calc) + parseFloat(percent_charge_calc);
                    total_charge = parseFloat(total_charge).toFixed(2);
                    // return total_charge;
                    return {
                        total: total_charge,
                        fixed: fixed_charge_calc,
                        percent: percent_charge_calc,
                    };
                }else {
                    // return "--";
                    return false;
                }
            }

            function getFees() {
                var sender_currency = acceptVar().sCurrency;
                var percent = acceptVar().sCurrency_percentCharge;
                var charges = feesCalculation();
                if(charges == false) {
                    return false;
                }
                $('.fees-show').html("Charge: " + parseFloat(charges.fixed).toFixed(2) + " " + sender_currency +" + " + parseFloat(percent).toFixed(2) + "%" + " = "+ parseFloat(charges.total).toFixed(2) + " " + sender_currency);
            }
            getFees();

            function getExchangeRate() { 
                var sender_currency = acceptVar().sCurrency;
                var sender_currency_rate = acceptVar().sCurrency_rate;
                var receiver_currency = acceptVar().rCurrency;
                var receiver_currency_rate = acceptVar().rCurrency_rate;
                var rate = parseFloat(receiver_currency_rate) / parseFloat(sender_currency_rate);
                $('.rate-show').html("1 " + sender_currency + " = " + parseFloat(rate).toFixed(4) + " " + receiver_currency);

                return rate;
            }
            getExchangeRate();

            function getPreview() {

                var senderAmount = $("input[name=sender_amount]").val();
                var sender_currency = acceptVar().sCurrency;
                senderAmount == "" ? senderAmount = 0 : senderAmount = senderAmount;

                // Sending Amount
                $('.request-amount').text(senderAmount + " " + sender_currency);

                var receiver_currency = acceptVar().rCurrency;
                var receiverAmount = receiveAmount();
                receiveAmount = parseFloat(receiverAmount).toFixed(2);
                $('.receive-amount').text(receiveAmount + " " + receiver_currency);

                $(".sender-currency").text(sender_currency);
                $(".receiver-currency").text(receiver_currency);

                $("input[name=sender_currency]").val(sender_currency);
                $("input[name=receiver_currency]").val(receiver_currency);

                // Fees
                var charges = feesCalculation();
                // console.log(total_charge + "--");
                $('.fees').text(charges.total + " " + sender_currency);

                // Pay In Total
                var pay_in_total = parseFloat(charges.total) + parseFloat(senderAmount);
                $('.pay-in-total').text(parseFloat(pay_in_total).toFixed(2) + " " + sender_currency);

            }
            getPreview(); 
            function getUserBalance() {
                var selectedCurrency = acceptVar().sCurrency;
                var selectedSymbol = acceptVar().sSymbol;
                var CSRF = $("meta[name=csrf-token]").attr("content");
                var data = {
                    _token      : CSRF,
                    target      : selectedCurrency,
                };
                // Make AJAX request for getting user balance
                $.post(userBalanceRoute,data,function() {
                    // success
                }).done(function(response){
                    var balance = response.data;
                    balance = parseFloat(balance).toFixed(2);
                    $(".balance-show").html("Available Balance "+selectedSymbol+balance);

                }).fail(function(response) {
                    var response = JSON.parse(response.responseText);
                    throwMessage(response.type,response.message.error);
                });
            }

            if(userBalance) {
                getUserBalance();
            }
        }
        $(document).ready(function(){
            run(JSON.parse(adSelectActiveItem("input[name=sender_currency]")),JSON.parse(adSelectActiveItem("input[name=receiver_currency]")));
        });
        $(document).on("click",".custom-option",function() { 
            run(JSON.parse(adSelectActiveItem("input[name=sender_currency]")),JSON.parse(adSelectActiveItem("input[name=receiver_currency]")));
        });

        $("input[name=sender_amount]").keyup(function(){
            run(JSON.parse(adSelectActiveItem("input[name=sender_currency]")),JSON.parse(adSelectActiveItem("input[name=receiver_currency]")));
        });

        $(".ad-select .custom-select-search").keyup(function(){
            var searchText = $(this).val().toLowerCase();
            var itemList =  $(this).parents(".ad-select").find(".custom-option");
            $.each(itemList,function(index,item){
                var text = $(item).find(".custom-currency").text().toLowerCase();
                var country = $(item).find(".custom-country").text().toLowerCase();

                var match = text.match(searchText);
                var countryMatch = country.match(searchText);

                if(match == null && countryMatch == null) {
                    $(item).addClass("d-none");
                }else {
                    $(item).removeClass("d-none");
                }
            });
        });

        var timeOut;
    </script>
    <script>
        // Get the input element
        var amountInput = document.getElementById('amountInput');
    
        // Add event listener for input event
        amountInput.addEventListener('input', function() {
            // Get the input value
            var inputValue = amountInput.value;
    
            // Remove any non-numeric characters except decimal point from the input value
            var numericValue = inputValue.replace(/[^0-9.]/g, '');
    
            // Update the input field value with the cleaned numeric value
            amountInput.value = numericValue;
        });
    </script>
@endpush