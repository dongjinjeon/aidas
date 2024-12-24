@php
    $feature    = $__website_sections->where('key',Str::slug(site_section_const()::FEATURE_SECTION))->first();
    $defualt     = get_default_language_code();
    $en     = "en"; 
@endphp
<section class="features-section pt-80">
    <div class="container">
        <div class="row">
            <div class="col-xl-8 col-lg-12">
                <div class="section-header">
                    <h4 class="title text--base">{{ @$feature->value->language->$defualt->heading ?? @$feature->value->language->$en->heading }}</h4>
                </div>
                <div class="header-title">
                    <h2 class="title">{{ @$feature->value->language->$defualt->sub_heading ?? @$feature->value->language->$en->sub_heading }}</h2>
                </div>
            </div>
        </div>
        <div class="feature-area pt-30">
            <div class="row mb-20-none">
                @foreach ($feature->value->items ?? [] as $key => $item)
                <div class="col-lg-4 col-md-6 mb-20" data-aos="fade-right" data-aos-duration="1800">
                    <div class="feature-item">
                        <div class="icon">
                            <i class="{{ $item->icon ?? "" }}"></i>
                        </div>
                        <div class="feature-content">
                            <h3 class="title">{{ @$item->language->$defualt->title ?? @$item->language->$en->title }}</h3>
                            <p class="details">{{ @$item->language->$defualt->description ?? @$item->language->$en->description }}</p>
                        </div>
                    </div>
                </div>
                @endforeach  
            </div>
        </div>
    </div>
</section>