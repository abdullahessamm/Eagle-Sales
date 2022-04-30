<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>{{ env('APP_NAME') }}</title>
    <link rel="icon" href="{{ asset('assets/images/logo/icon.png') }}">
    <!-- link to bootstrap -->
    <link rel="stylesheet" href="{{ asset('assets/css/bootstrap.css') }}">
    <!-- link to fontawesome -->
    <link rel="stylesheet" href="{{ asset('assets/css/fontawesome.css') }}">
    <!-- link to landing page style -->
    <link rel="stylesheet" href="{{ asset('assets/css/landing.css') }}">
</head>
<body data-simplebar>
    <!-- under development message -->
    @include('landing.dev_msg')

    <!--- Header -->
    @include('landing.header')

    <!--- Mobile navigator -->
    @include('landing.mobile_nav')

    <!-- About -->
    @include('landing.about')

    <!-- Services -->
    @include('landing.services')

    <!-- how app works -->
    @include('landing.how_app_works')

    <!-- feedback -->
    @include('landing.feedback')

    <!-- App features -->
    @include('landing.app_features')

    <!-- Services -->
    @include('landing.our_success_partners')

    <!-- footer -->
    @include('landing.footer')

    <script src="{{ asset('assets/js/jQuery.js') }}"></script>
    <script src="{{ asset('assets/js/popper.js') }}"></script>
    <script src="{{ asset('assets/js/bootstrap.js') }}"></script>
    <script src="{{asset('assets/js/landing.js')}}"></script>
</body>
</html>