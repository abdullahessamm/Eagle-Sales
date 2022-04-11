<section id="services" class="mb-4" dir="{{ app()->currentLocale() == 'en' ? 'ltr' : 'rtl'}}">
    <div class="container">
        <div class="row">
            <div class="col-12">
                <h3 class="section-title text-center mb-5"> {{__('landing.services.our_services')}} </h3>
            </div>
        </div>
        <div class="row">
            <div class="col-12 col-md-3">
                <div class="service text-center h-100">
                    <div class="img-container">
                        <img src="{{url('assets/images/landing/icons/services/online_sales.png')}}" alt="online sales">
                    </div>
                    <h5 class="title"> {{__('landing.services.online_sales.title')}} </h5>
                    <p class="mobile-description d-block d-md-none  {{app()->currentLocale() == 'en' ? 'text-left' : 'text-right'}}">
                        {{__('landing.services.online_sales.text')}}
                    </p>
                </div>
            </div>
            <div class="col-12 col-md-3">
                <div class="service text-center h-100  mt-4 mt-md-0">
                    <div class="img-container">
                        <img src="{{url('assets/images/landing/icons/services/direct_sales.png')}}" alt="Direct Sales">
                    </div>
                    <h5 class="title"> {{__('landing.services.direct_sales.title')}} </h5>
                    <p class="mobile-description d-block d-md-none  {{app()->currentLocale() == 'en' ? 'text-left' : 'text-right'}}">
                        {{__('landing.services.direct_sales.text')}}
                    </p>
                </div>
            </div>
            <div class="col-12 col-md-3">
                <div class="service text-center h-100  mt-4 mt-md-0">
                    <div class="img-container">
                        <img src="{{url('assets/images/landing/icons/services/digital_marketing.png')}}" alt="digital marketing">
                    </div>
                    <h5 class="title"> {{__('landing.services.digital_marketing.title')}} </h5>
                    <p class="mobile-description d-block d-md-none  {{app()->currentLocale() == 'en' ? 'text-left' : 'text-right'}}">
                        {{__('landing.services.digital_marketing.text')}}
                    </p>
                </div>
            </div>
            <div class="col-12 col-md-3">
                <div class="service text-center h-100  mt-4 mt-md-0">
                    <div class="img-container">
                        <img src="{{url('assets/images/landing/icons/services/analystics.png')}}" alt="Sales & Market Analytics">
                    </div>
                    <h5 class="title"> {{__('landing.services.analystics.title')}} </h5>
                    <p class="mobile-description d-block d-md-none  {{app()->currentLocale() == 'en' ? 'text-left' : 'text-right'}}">
                        {{__('landing.services.analystics.text')}}
                    </p>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-12">
                <div id="service-description">
                    <p class="noselect {{app()->currentLocale() == 'ar' ? 'text-right' : 'text-left'}}"></p>
                </div>
            </div>
        </div>
    </div>
</section>