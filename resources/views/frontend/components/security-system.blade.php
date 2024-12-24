@php
    $security_system    = $__website_sections->where('key',Str::slug(site_section_const()::SECURITY_SYSTEM_SECTION))->first();
    $defualt     = get_default_language_code();
    $en     = "en";
@endphp
<section class="security-section ptb-80">
    <div class="container">
        <div class="row">
            <div class="col-xl-8 col-lg-12">
                <div class="section-header">
                    <h4 class="title text--base">{{ @$security_system->value->language->$defualt->heading ?? @$security_system->value->language->$en->heading }}</h4>
                </div>
                <div class="header-title">
                    <h2 class="title">{{ @$security_system->value->language->$defualt->sub_heading ?? @$security_system->value->language->$en->sub_heading }}</h2>
                </div>
            </div>
        </div>
        <div class="security-area pt-30">
            <div class="row mb-20-none">
                @foreach ($security_system->value->items ?? [] as $key => $item)
                <div class="col-lg-4 col-md-6 mb-20">
                    <div class="security-item">
                       <div class="icon">
                           <i class="{{ $item->icon ?? "" }}"></i>
                       </div>
                       <div class="security-details">
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