@php
    $testimonial    = $__website_sections->where('key',Str::slug(site_section_const()::CLIENT_FEEDBACK_SECTION))->first();
    $defualt     = get_default_language_code();
    $en     = "en";
@endphp
<section class="testimonial-section pt-80">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-xl-7 col-lg-7 text-center">
                <div class="section-header">
                    <h4 class="tite text--base">{{ $testimonial->value->language->$defualt->heading ?? $testimonial->value->language->$en->heading }}</h4>
                </div>
                <div class="section-title">
                    <h2 class="section-title">{{ $testimonial->value->language->$defualt->sub_heading ?? $testimonial->value->language->$en->sub_heading }}</h2>
                </div>
            </div>
        </div>
        <div class="testimonial-area pt-30">
            <div class="testimonial-slider">
                <div class="swiper-wrapper">
                    @foreach ($testimonial->value->items ?? [] as $key => $item)
                    <div class="swiper-slide">
                        <div class="testimonial-wrapper">
                            <div class="testimonial-thumb">
                                <img src="{{ get_image($item->image ?? "","site-section") }}" alt="client">
                                <div class="testimonial-quote">
                                    <svg width="22" height="19" fill="none" xmlns="http://www.w3.org/2000/svg"
                                        class="fill-white">
                                        <path
                                            d="M6.027 18.096c-.997 0-1.813-.204-2.448-.612a5.147 5.147 0 01-1.564-1.564 5.729 5.729 0 01-.952-2.38C.927 12.679.86 11.976.86 11.432c0-2.221.567-4.239 1.7-6.052C3.693 3.567 5.461 2.093 7.863.96l.612 1.224c-1.405.59-2.606 1.519-3.604 2.788-1.042 1.27-1.564 2.561-1.564 3.876 0 .544.068 1.02.204 1.428a3.874 3.874 0 012.516-.884c1.179 0 2.199.385 3.06 1.156.862.77 1.292 1.836 1.292 3.196 0 1.27-.43 2.312-1.292 3.128-.861.816-1.881 1.224-3.06 1.224zm11.56 0c-.997 0-1.813-.204-2.448-.612a5.148 5.148 0 01-1.564-1.564 5.73 5.73 0 01-.952-2.38c-.136-.861-.204-1.564-.204-2.108 0-2.221.567-4.239 1.7-6.052 1.134-1.813 2.902-3.287 5.304-4.42l.612 1.224c-1.405.59-2.606 1.519-3.604 2.788-1.042 1.27-1.564 2.561-1.564 3.876 0 .544.068 1.02.204 1.428a3.874 3.874 0 012.516-.884c1.179 0 2.199.385 3.06 1.156.862.77 1.292 1.836 1.292 3.196 0 1.27-.43 2.312-1.292 3.128-.861.816-1.881 1.224-3.06 1.224z">
                                        </path>
                                    </svg>
                                </div>
                            </div>
                            <div class="testimonial-content">
                                <div class="testimonial-ratings">
                                    @for ($i = 0; $i < $item->star; $i++)
                                    <i class="fas fa-star"></i>
                                    @endfor 
                                </div>
                                <p>{{ @$item->language->$defualt->comment ?? @$item->language->$en->comment }}</p>
                                <div class="testimonial-user-wrapper">
                                    <div class="testimonial-user-content">
                                        <h5 class="title">{{ $item->name ?? "" }}<span>/ {{ $item->designation ?? "" }}</span></h5>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
                <div class="swiper-pagination"></div>
            </div>
        </div>
    </div>
</section>