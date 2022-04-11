<section id="about" class="pt-5 mt-5 mt-md-0">
    <div class="container">
        <div class="row">
            <div class="col-12 col-md-4">
                <div class="left-side h-100 d-flex align-items-end justify-content-center justify-content-md-start">
                    <img src="{{url('assets/images/landing/avatar/about.svg')}}" alt="Avatar">
                </div>
            </div>

            <div class="col-12 col-md-8">
                <div class="text-center right-side" dir="{{ app()->currentLocale() == 'en' ? 'ltr' : 'rtl'}}">
                    <h3 class="section-title mb-3 mb-md-5 mt-3"> {{__('landing.about.about_us')}} </h3>
                    <p class="section-paragraph {{app()->currentLocale() == 'en' ? 'text-left' : 'text-right'}}">
                        {{__('landing.about.about_us_text')}}
                    </p>
                </div>
            </div>
        </div>
    </div>
</section>