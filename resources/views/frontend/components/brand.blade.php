@php
    $brand    = $__website_sections->where('key',Str::slug(site_section_const()::BRAND_SECTION))->first(); 
@endphp
<section class="brand-section  ptb-40">
    <div class="container">
        <div class="row">
            <div class="brand-slider">
                <div class="swiper-wrapper">
                    @foreach ($brand->value->items ?? [] as $key => $item)
                    <div class="swiper-slide">
                        <div class="brand-item">
                            <img src="{{ get_image(@$item->image, "site-section") }}" alt="brand">
                        </div>
                    </div>
                    @endforeach 
                </div>
            </div>
        </div>
    </div>
</section>