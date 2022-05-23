<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="icon" href="{{ asset('assets/images/logo/icon.png') }}">
    <!-- link to fontawesome -->
    <link rel="stylesheet" href="{{ asset('assets/css/fontawesome.css') }}">
    <!-- link to auth app style -->
    <link rel="stylesheet" href="{{url('assets/css/auth.css')}}">
    <title>{{ env('APP_NAME') }}</title>
</head>
<body>
    <div id="auth-app"></div>
    <script src="{{ asset('assets/js/auth.js') }}"></script>
</body>
</html>