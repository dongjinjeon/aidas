@php
    $why_choose_us    = $__website_sections->where('key',Str::slug(site_section_const()::WHY_CHOOSE_US))->first();
    $defualt     = get_default_language_code();
    $en     = "en";
@endphp
<section class="chooice-section pt-80">
    <div class="container">
       <div class="row">
           <div class="col-xl-8 col-lg-12">
               <div class="section-header">
                  <h4 class="title text--base">{{ @$why_choose_us->value->language->$defualt->heading ?? @$why_choose_us->value->language->$en->heading }}</h4>
               </div>
                <div class="section-title">
                    <h2 class="title">{{ @$why_choose_us->value->language->$defualt->sub_heading ?? @$why_choose_us->value->language->$en->sub_heading }}</h2>
                </div>
           </div>
       </div>
       <div class="choose-us-area pt-30">
           <div class="row mb-20-none">
                @foreach ($why_choose_us->value->items ?? [] as $key => $item)
                <div class="col-lg-4 mb-20" data-aos="fade-right" data-aos-duration="1200">
                  <div class="choice-item">
                    <div class="icon">
                        <i class="{{ $item->icon ?? "" }}"></i>
                    </div>
                      <div class="choice-content">
                        <h3 class="title">{{ @$item->language->$defualt->title ?? @$item->language->$en->title }}</h3>
                        <p>{{ @$item->language->$defualt->description ?? @$item->language->$en->description }}</p>
                      </div>
                  </div>
                </div>
                @endforeach 
           </div>
       </div>
    </div>
</section>