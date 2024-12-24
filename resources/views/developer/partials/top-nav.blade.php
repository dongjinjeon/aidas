<header class="developer-header">
    <div class="container-fluid">
        <div class="developer-wrapper">
            <div class="developer-logo-area">
                <div class="sidebar-mobile-btn">
                    <button><i class="fas fa-bars"></i></button>
                </div>
                <a class="site-logo site-title" href="{{ setRoute("index") }}"><img src="{{ get_logo($basic_settings) }}" alt="logo"></a>
                <span class="logo-text">{{ __("Developer") }}</span>
            </div>
            <div class="developer-header-content">
                <ul class="developer-header-list">
                    <li>
                        <a href="{{ setRoute("developer.support") }}">{{ __("Support") }}</a>
                    </li>
                </ul>
                <div class="developer-header-action">
                    <a href="{{ setRoute("index") }}" class="btn--base"><i class="las la-user-edit me-1"></i> {{ __("Back to Home page") }}</a>
                </div>
            </div>
        </div>
    </div>
</header>