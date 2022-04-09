<section id="about">
    <div class="container">
        <div class="row">
            <div class="col-4 d-none d-md-block">
                <div class="left-side">
                    <img src="{{url('assets/images/landing/avatar/about.svg')}}" alt="Avatar">
                </div>
            </div>

            <div class="col-12 col-md-8">
                <div class="text-center right-side" dir="{{ app()->currentLocale() == 'en' ? 'ltr' : 'rtl'}}">
                    <h3 class="section-title mb-5 mt-3"> {{__('landing.about.about_us')}} </h3>
                    <p class="section-paragraph">
                        {{__('landing.about.about_us_text')}}
                    </p>
                    <div class="button">
                        {{__('landing.about.see_more')}}
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>