@php
    $banner    = $__website_sections->where('key',Str::slug(site_section_const()::BANNER_SECTION))->first();
    $defualt     = get_default_language_code(); 
    $en     = "en"; 
    $currencies  = App\Models\Admin\Currency::active()->get();
    $charges = App\Models\Admin\TransactionSetting::where("slug",global_const()::TRANSFER)->first();
@endphp
<section class="banner-section">
    <div class="container">
        <div class="row align-items-center mb-40-none">
            <div class="col-lg-6 mb-40">
                <div class="banner-content">
                    <div class="banner-title">
                        <h1 class="title"><span class="text--base">{{ @$basic_settings->site_name }}</span> - {{ @$banner->value->language->$defualt->heading ?? @$banner->value->language->$en->heading}}</h1>
                    </div>
                    <div class="banner-paragraph">
                        <p> {{ @$banner->value->language->$defualt->desc ?? @$banner->value->language->$en->desc}}</p>
                    </div>
                    <div class="banner-btn pt-4">
                        <a href="{{ setRoute("user.register") }}" class="btn--base">{{ @$banner->value->language->$defualt->button_name ?? @$banner->value->language->$en->button_name}} <i class="las la-arrow-right"></i></a>
                        <a href="{{ setRoute("developer") }}" class="btn--base">{{ @$banner->value->language->$defualt->button_name_two ?? @$banner->value->language->$en->button_name_two}} <i class="las la-arrow-right"></i></a>
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="banner-form-wrapper">
                    <div class="exchange-area text-center mb-20">
                        <code class="d-block text-center"><span>{{ __("Exchange Rate") }}</span> <span class="rate-show"></span></code> 
                    </div>
                    <form class="banner-form">
                         <div class="form-area">
                            <div class="form-group">
                                <label>{{ __("Sender Amount") }}<span>*</span></label>
                                <div class="input-group">
                                    <input type="text" name="sender_amount" class="form--control" value="{{ old('sender_amount') }}" placeholder="{{ __('Enter Amount') }}">
                                    <div class="ad-select">
                                        <div class="custom-select">
                                            <div class="custom-select-inner">
                                                <input type="hidden" name="sender_currency" value="{{ $currencies[0]->code }}">
                                                <img src="{{ get_image($currencies[0]->flag, 'currency-flag') }}" alt="flag" class="custom-flag">
                                                <span class="custom-currency">{{ $currencies[0]->code }}</span>
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
                                                    @foreach ($currencies as $key => $item)
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
                         <div class="form-area">
                            <div class="form-group">
                                <label>{{ __("Recipients Amount") }}<span>*</span></label>
                                <div class="input-group">
                                    <input type="text" name="receiver_amount" class="form--control" value="{{ old('receiver_amount') }}" placeholder="{{ __('Enter Amount') }}" readonly>
                               
                                    <div class="ad-select">
                                        <div class="custom-select">
                                            <div class="custom-select-inner">
                                                <input type="hidden" name="receiver_currency" value="{{ $currencies[0]->code }}"> 
                                                <img src="{{ get_image($currencies[0]->flag, 'currency-flag') }}" alt="flag" class="custom-flag">
                                                <span class="custom-currency">{{ $currencies[0]->code }}</span>
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
                                                    @foreach ($currencies as $key => $item)
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
                         <div class="sending-btn pt-3">
                            <a href="{{ setRoute('user.send.money.index') }}" class="btn--base w-100">{{ __("Send Money") }}</a>
                         </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>
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
@endpush