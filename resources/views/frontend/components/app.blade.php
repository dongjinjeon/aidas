@php
    $app    = $__website_sections->where('key',Str::slug(site_section_const()::APP_SECTION))->first();
    $defualt     = get_default_language_code();
    $en     = "en"; 
@endphp
<section class="app-section pt-80">
    <div class="container">
       <div class="app-section-title pb-40">
          <div class="row align-items-center mb-40-none">
             <div class="col-lg-7 mb-40">
                <div class="app-title">
                    <h2 class="titl">{{ @$app->value->language->$defualt->heading ?? @$app->value->language->$en->heading }}</h2>
                </div>
                <p>{{ @$app->value->language->$defualt->details ?? @$app->value->language->$en->details }}</p>
                <div class="app-btn-wrapper">
                    @foreach ($app->value->items ?? [] as $key => $item)
                    <a href="{{ @$item->link ?? "" }}" class="app-btn">
                        <div class="app-icon">
                            <img src="{{ get_image($item->image ?? "","site-section") }}" alt="icon">
                        </div>
                        <div class="content">
                            <span>{{ @$item->language->$defualt->title ?? @$item->language->$en->title }}</span>
                            <h5 class="title">{{ @$item->language->$defualt->sub_title ?? @$item->language->$en->sub_title }}</h5>
                        </div>
                        <div class="icon">
                            <img src="{{ asset("public/frontend/images/element/qr-icon.webp") }}" alt="element">
                        </div>
                    </a>
                    @endforeach
                </div>
             </div>
             <div class="col-lg-5 mb-40">
                <div class="app-img">
                    <img src="{{ get_image($app->value->image ?? "","site-section") }}" alt="img">
                </div>
            </div>
          </div>
       </div>
    </div>
</section>