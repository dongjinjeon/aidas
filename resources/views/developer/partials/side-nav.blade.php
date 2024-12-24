@php
    $current_url = URL::current();
@endphp
<div class="developer-bar">
    <div class="developer-bar-wrapper">
        <ul class="developer-bar-main-menu"> 
            <li class="sidebar-single-menu {{ $current_url == setRoute('developer') ? "active" : "" }}">
                <a href="{{ setRoute("developer") }}">
                    <span class="title">{{ __("Introduction") }}</span>
                </a>
            </li>
            @php 
                $setup_section_get_started  = [ 
                    setRoute('developer.prerequisites'), 
                    setRoute('developer.authentication'), 
                    setRoute('developer.baseUrl'), 
                ];
            @endphp
            <li class="sidebar-single-menu has-sub @if (in_array($current_url,$setup_section_get_started)) active @endif">
                <a href="javascript:void(0)">
                    <span class="title">{{ __("Getting Started") }}</span>
                </a>
                <ul class="sidebar-submenu @if (in_array($current_url,$setup_section_get_started)) open @endif">
                    <li class="nav-item {{ $current_url == setRoute('developer.prerequisites') ? "active" : "" }}">
                        <a href="{{ setRoute('developer.prerequisites') }}">
                            <span class="title">{{ __("Prerequisites") }}</span>
                        </a>
                    </li>
                    <li class="nav-item {{ $current_url == setRoute('developer.authentication') ? "active" : "" }}">
                        <a href="{{ setRoute('developer.authentication') }}">
                            <span class="title">{{ __("Authentication") }}</span>
                        </a>
                    </li>
                    <li class="nav-item {{ $current_url == setRoute('developer.baseUrl') ? "active" : "" }}">
                        <a href="{{ setRoute('developer.baseUrl') }}">
                            <span class="title"> {{ __("Base URL") }}</span>
                        </a>
                    </li>
                </ul>
            </li>
            @php 
                $setup_section_api_reference  = [ 
                    setRoute('developer.accessToken'),  
                    setRoute('developer.initiatePayment'),  
                    setRoute('developer.checkPaymentStatus'),  
                ];
            @endphp
            <li class="sidebar-single-menu has-sub @if (in_array($current_url,$setup_section_api_reference)) active @endif">
                <a href="javascript:void(0)">
                    <span class="title">{{ __("API Reference") }}</span>
                </a>
                <ul class="sidebar-submenu @if (in_array($current_url,$setup_section_api_reference)) open @endif">
                    <li class="nav-item {{ $current_url == setRoute('developer.accessToken') ? "active" : "" }}">
                        <a href="{{ setRoute('developer.accessToken') }}">
                            <span class="title">{{ __("Access Token") }}</span>
                        </a>
                    </li>
                    <li class="nav-item {{ $current_url == setRoute('developer.initiatePayment') ? "active" : "" }}">
                        <a href="{{ setRoute('developer.initiatePayment') }}">
                            <span class="title">{{ __("Initiate Payment") }}</span>
                        </a>
                    </li>
                    <li class="nav-item {{ $current_url == setRoute('developer.checkPaymentStatus') ? "active" : "" }}">
                        <a href="{{ setRoute('developer.checkPaymentStatus') }}">
                            <span class="title">{{ __("Check Payment Status") }}</span>
                        </a>
                    </li>
                </ul>
            </li>
            <li class="sidebar-single-menu {{ $current_url == setRoute('developer.responseCodes') ? "active" : "" }}">
                <a href="{{ setRoute('developer.responseCodes') }}">
                    <span class="title">{{ __("Response Codes") }}</span>
                </a>
            </li>
            <li class="sidebar-single-menu {{ $current_url == setRoute('developer.errorHandling') ? "active" : "" }}">
                <a href="{{ setRoute('developer.errorHandling') }}">
                    <span class="title">{{ __("Error Handling") }}</span>
                </a>
            </li>
            <li class="sidebar-single-menu {{ $current_url == setRoute('developer.bestPractices') ? "active" : "" }}">
                <a href="{{ setRoute('developer.bestPractices') }}">
                    <span class="title">{{ __("Best Practices") }}</span>
                </a>
            </li>
            <li class="sidebar-single-menu {{ $current_url == setRoute('developer.examples') ? "active" : "" }}">
                <a href="{{ setRoute('developer.examples') }}">
                    <span class="title">{{ __("Examples") }}</span>
                </a>
            </li>
            <li class="sidebar-single-menu {{ $current_url == setRoute('developer.faq') ? "active" : "" }}">
                <a href="{{ setRoute('developer.faq') }}">
                    <span class="title">{{ __("FAQ") }}</span>
                </a>
            </li>
            <li class="sidebar-single-menu {{ $current_url == setRoute('developer.support') ? "active" : "" }}">
                <a href="{{ setRoute('developer.support') }}">
                    <span class="title">{{ __("Support") }}</span>
                </a>
            </li>
        </ul>
    </div>
</div>