<div id="mobile-menu" class="d-block {{ app()->currentLocale() == 'en' ? 'text-left' : 'text-right'}}" dir="{{ app()->currentLocale() == 'en' ? 'ltr' : 'rtl'}}">
    <div class="header mb-3">
        <div class="container-fluid">
            <div class="row">
                <div class="col-6">
                    <a href="{{ url('/') }}">
                        <img src="{{ asset('assets/images/logo/white_sm.png') }}" class="{{app()->currentLocale() == 'en' ? '' : 'h-flip'}}" alt="logo" width="50" height="40">
                    </a>
                </div>
                <div class="col-6 {{ app()->currentLocale() == 'en' ? 'text-right' : 'text-left'}}">
                    <div id="close-mobile-menu" class="d-inline-block">
                        <i class="fas fa-times"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="body mt-3">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="link pb-3 pt-3">
                        <a href="#"><i class="fa-solid fa-house"></i> {{__('landing.header.links.home')}}</a>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-12">
                    <div class="link pb-3 pt-3">
                        <a href="#"><i class="fa-solid fa-gears"></i> {{__('landing.header.links.services')}}</a>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-12">
                    <div class="link pb-3 pt-3">
                        <a href="#"><i class="fa-solid fa-mobile-screen-button"></i> {{__('landing.header.links.how_app_works')}}</a>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-12">
                    <div class="link pb-3 pt-3">
                        <a href="#"><i class="fa-solid fa-store"></i> {{__('landing.header.links.store')}}</a>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-12 mt-3 mb-4">
                    <div class="separator"></div>
                </div>
            </div>
            <div class="row">
                <div class="col-6">
                    <div class="login {{app()->currentLocale() == 'en' ? 'text-right' : 'text-left'}}">
                        <a class="d-inline-block" href="#"><i class="fa-solid fa-user"></i> {{__('landing.header.links.login')}}</a>
                    </div>
                </div>
                <div class="col-6">
                    <div class="signup {{app()->currentLocale() == 'en' ? 'text-left' : 'text-right'}}">
                        <a class="d-inline-block" href="#"><i class="fa-solid fa-user-plus"></i> {{__('landing.header.links.signup')}}</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="footer position-absolute text-center w-100" dir="ltr">
        <span class="lang">
            <a href="{{ url('/lang/en') }}"> English </a>
        </span>
        <span class="lang">
            <a href="{{ url('/lang/ar') }}"> العربية </a>
        </span>
    </div>
</div>