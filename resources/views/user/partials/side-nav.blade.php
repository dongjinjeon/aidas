@php
    $user_type = auth()->user()->type; 
@endphp
<div class="sidebar">
    <div class="sidebar-inner">
        <div class="sidebar-inner-wrapper">
            <div class="sidebar-logo">
                <a href="{{ setRoute('index') }}" class="sidebar-main-logo">
                    <img src="{{ get_logo($basic_settings) }}" alt="logo">
                </a>
                <button class="sidebar-menu-bar">
                    <i class="fas fa-exchange-alt"></i>
                </button>
            </div>
            <div class="sidebar-menu-wrapper">
                <ul class="sidebar-menu">
                    <li class="sidebar-menu-item">
                        <a href="{{ setRoute('user.dashboard') }}">
                            <i class="menu-icon las la-palette"></i>
                            <span class="menu-title">{{ __("Dashboard") }}</span>
                        </a>
                    </li> 
                    <li class="sidebar-menu-item">
                        <a href="{{ setRoute('user.add.money.index') }}">
                            <i class="menu-icon las la-plus-square"></i>
                            <span class="menu-title">{{ __("Add Money") }}</span>
                        </a>
                    </li>
                    <li class="sidebar-menu-item">
                        <a href="{{ setRoute('user.send.money.index') }}">
                            <i class="menu-icon las la-paper-plane"></i>
                            <span class="menu-title">{{ __("Send Money") }}</span>
                        </a>
                    </li>
                    <li class="sidebar-menu-item">
                        <a href="{{ setRoute('user.receipient.index') }}">
                            <i class="menu-icon las la-user-friends"></i>
                            <span class="menu-title">{{ __("My Recipients") }}</span>
                        </a>
                    </li>    
                    <li class="sidebar-menu-item">
                        <a href="{{ setRoute('user.withdraw.money.index') }}">
                            <i class="menu-icon las la-cloud-upload-alt"></i>
                            <span class="menu-title">{{ __("Withdraw") }}</span>
                        </a>
                    </li>  
                    <li class="sidebar-menu-item">
                        <a href="{{ setRoute('user.exchange.money.index') }}">
                            <i class="menu-icon lab la-stack-exchange"></i>
                            <span class="menu-title">{{ __("Money Exchange") }}</span>
                        </a>
                    </li>
                    <li class="sidebar-menu-item">
                        <a href="{{ setRoute('user.request.money.index') }}">
                            <i class="menu-icon las la-fill-drip"></i>
                            <span class="menu-title">{{ __("Request Money") }}</span>
                        </a>
                    </li>
                    <li class="sidebar-menu-item sidebar-dropdown">
                        <a href="javascript:void(0)">
                            <i class="menu-icon las la-calendar-check"></i>
                            <span class="menu-title">{{ __("Transaction Log") }}</span>
                        </a>
                        <ul class="sidebar-submenu">
                            <li class="sidebar-menu-item">
                                <a href="{{ setRoute('user.transactions.index','add-money-log') }}" class="nav-link">
                                    <i class="menu-icon las la-ellipsis-h"></i>
                                    <span class="menu-title">{{ __("Add Money Log") }}</span>
                                </a>
                                <a href="{{ setRoute('user.transactions.index','send-money-log') }}" class="nav-link">
                                    <i class="menu-icon las la-ellipsis-h"></i>
                                    <span class="menu-title">{{ __("Send Money Log") }}</span>
                                </a>
                                <a href="{{ setRoute('user.transactions.index','withdraw-log') }}" class="nav-link">
                                    <i class="menu-icon las la-ellipsis-h"></i>
                                    <span class="menu-title">{{ __("Withdraw Log") }}</span>
                                </a>
                                <a href="{{ setRoute('user.transactions.index','money-exchange-log') }}" class="nav-link">
                                    <i class="menu-icon las la-ellipsis-h"></i>
                                    <span class="menu-title">{{ __("Money Exchange Log") }}</span>
                                </a>
                                <a href="{{ setRoute('user.transactions.request.money') }}" class="nav-link">
                                    <i class="menu-icon las la-ellipsis-h"></i>
                                    <span class="menu-title">{{ __("Request Money Log") }}</span>
                                </a>
                                <a href="{{ setRoute('user.transactions.voucher.log') }}" class="nav-link">
                                    <i class="menu-icon las la-ellipsis-h"></i>
                                    <span class="menu-title">{{ __("Voucher Log") }}</span>
                                </a>
                                @if ($user_type == "business")
                                <a href="{{ setRoute('user.payment.log.index') }}" class="nav-link">
                                    <i class="menu-icon las la-ellipsis-h"></i>
                                    <span class="menu-title">{{ __("Payment Log") }}</span>
                                </a>
                                @endif
                            </li>
                        </ul>
                    </li>
                    <li class="sidebar-menu-item">
                        <a href="{{ setRoute('user.my-voucher.index') }}">
                            <i class="menu-icon las la-scroll"></i>
                            <span class="menu-title">{{ __("My Voucher") }}</span>
                        </a>
                    </li>  
                    <li class="sidebar-menu-item sidebar-dropdown">
                        <a href="javascript:void(0)">
                            <i class="menu-icon las la-user-cog"></i>
                            <span class="menu-title">{{ __("Settings") }}</span>
                        </a>
                        <ul class="sidebar-submenu">
                            <li class="sidebar-menu-item">
                                <a href="{{ setRoute('user.authorize.kyc') }}" class="nav-link">
                                    <i class="menu-icon las la-ellipsis-h"></i>
                                    <span class="menu-title">{{ __("KYC Verification") }}</span>
                                </a> 
                                <a href="{{ setRoute('user.security.google.2fa') }}" class="nav-link">
                                    <i class="menu-icon las la-ellipsis-h"></i>
                                    <span class="menu-title">{{ __("2FA Security") }}</span>
                                </a>  
                                @if ($user_type == "business")
                                <a href="{{ setRoute('user.api.key') }}" class="nav-link">
                                    <i class="menu-icon las la-ellipsis-h"></i>
                                    <span class="menu-title">{{ __("API Key") }}</span>
                                </a>
                                @endif
                            </li>
                        </ul>
                    </li>
                    <li class="sidebar-menu-item">
                        <a href="javascript:void(0)" class="logout-btn">
                            <i class="menu-icon las la-sign-out-alt"></i>
                            <span class="menu-title">{{ __("Logout") }}</span>
                        </a>
                    </li>
                </ul>
            </div>
        </div>
        <div class="sidebar-doc-box bg-overlay-base bg_img" data-background="{{ asset("public/frontend/images/element/sidebar.webp") }}">
            <div class="sidebar-doc-icon">
                <i class="las la-headset"></i>
            </div>
            <div class="sidebar-doc-content">
                <h4 class="title">{{ __("Help Center") }}</h4>
                <p>{{ __("How can we help you") }}?</p>
                <div class="sidebar-doc-btn">
                    <a href="{{ setRoute('user.support.ticket.index') }}" class="btn--base w-100">{{ __("Get Support") }}</a>
                </div>
            </div>
        </div>
    </div>
</div>
@push('script')
    <script>
        $(".logout-btn").click(function(){
            var actionRoute =  "{{ setRoute('user.logout') }}";
            var target      = 1;
            var message     = `Are you sure to <strong>Logout</strong>?`;

            openAlertModal(actionRoute,target,message,"Logout","POST");
        });
    </script>
@endpush