<section id="how-app-works">
    <div class="container">
        <div class="row">
            <div class="col-12">
                <div class="title mb-5">
                    <h3 class="section-title text-center">{{__('landing.how_app_works.title')}}</h3>
                </div>
            </div>
        </div>
        <div class="row mt-5 mb-5">
            <div class="col-12 col-md-6 order-2 order-md-0">
                <h4
                    class="section-subtitle mb-4 mt-4 mt-md-0 text-center {{app()->currentLocale() == 'en' ? 'text-md-left' : 'text-md-right'}}"
                >
                    {{__('landing.how_app_works.cond')}} <span> {{__('landing.how_app_works.supplier.title')}} </span>
                </h4>
                <p 
                    dir="{{app()->currentLocale() == 'en' ? 'ltr' : 'rtl'}}"
                    class="{{app()->currentLocale() == 'en' ? 'text-left' : 'text-right'}}"
                >
                    {{__('landing.how_app_works.supplier.text')}}
                </p>
            </div>
            <div class="col-12 col-md-6 text-center text-md-right">
                <img src="{{ url('assets/images/landing/avatar/supplier.svg') }}" alt="Avatar">
            </div>
        </div>
        <div class="row mt-5 mb-5">
            <div class="col-12 col-md-6 text-center text-md-left">
                <img src="{{ url('assets/images/landing/avatar/seller.svg') }}" alt="Avatar">
            </div>
            <div class="col-12 col-md-6">
                <h4
                    class="section-subtitle mb-4 mt-4 mt-md-0 text-center {{app()->currentLocale() == 'en' ? 'text-md-left' : 'text-md-right'}}"
                >
                    {{__('landing.how_app_works.cond')}} <span> {{__('landing.how_app_works.seller.title')}} </span>
                </h4>
                <p 
                    dir="{{app()->currentLocale() == 'en' ? 'ltr' : 'rtl'}}"
                    class="{{app()->currentLocale() == 'en' ? 'text-left' : 'text-right'}}"
                >
                    {{__('landing.how_app_works.seller.text')}}
                </p>
            </div>
        </div>
        <div class="row mt-5 mb-5">
            <div class="col-12 col-md-6 order-2 order-md-0">
                <h4
                    class="section-subtitle mb-4 mt-4 mt-md-0 text-center {{app()->currentLocale() == 'en' ? 'text-md-left' : 'text-md-right'}}"
                >
                    {{__('landing.how_app_works.cond')}} <span> {{__('landing.how_app_works.customer.title')}} </span>
                </h4>
                <p 
                    dir="{{app()->currentLocale() == 'en' ? 'ltr' : 'rtl'}}"
                    class="{{app()->currentLocale() == 'en' ? 'text-left' : 'text-right'}}"
                >
                    {{__('landing.how_app_works.customer.text')}}
                </p>
            </div>
            <div class="col-12 col-md-6 text-center text-md-right">
                <img src="{{ url('assets/images/landing/avatar/customer.svg') }}" alt="Avatar">
            </div>
        </div>
    </div>
</section>