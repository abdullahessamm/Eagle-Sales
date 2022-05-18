<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title> {{ env('APP_NAME') }} </title>
    <link rel="icon" href="{{ asset('assets/images/logo/icon.png') }}">
</head>

<body>
    <div class="screen">
        <div class="screen-content">
            <div class="screen-content-header">
                <div class="screen-content-header-logo">
                    <img src="{{ asset('assets/images/logo/full_logo_colored.png') }}" alt="Logo" title="Logo">
                </div>
                <div class="screen-content-header-title">
                    <h1>
                        {{ env('APP_NAME') }}
                    </h1>
                </div>
            </div>
            <div class="screen-content-body">
                <div class="screen-content-body-text">
                    <h1>
                        Sorry, this app is not supported in your country.
                    </h1>
                    <p>
                        Please contact us for more information.
                    </p>
                </div>
            </div>
        </div>
    </div>
</body>

<style>
    .screen {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-color: #f5f5f5;
        overflow-y: auto;
    }

    .screen-content {
        width: 100%;
        height: 100%;
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
    }

    .screen-content-header {
        width: 100%;
        height: 50%;
        display: flex;
        flex-direction: column;
        justify-content: end;
        align-items: center;
    }

    .screen-content-header-logo {
        width: 100%;
        display: flex;
        flex-direction: column;
        justify-content: end;
        align-items: center;
    }

    .screen-content-header-logo img {
        width: 50%;
        max-width: 200px;
    }

    .screen-content-header-title {
        width: 100%;
        display: flex;
        flex-direction: column;
        align-items: center;
    }

    .screen-content-header-title h1 {
        font-size: 2.5rem;
        font-weight: bold;
        color: #45ba91;
    }

    .screen-content-body {
        width: 100%;
        height: 50%;
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
    }

    .screen-content-body-text {
        width: 100%;
        height: 100%;
        display: flex;
        flex-direction: column;
        justify-content: start;
        align-items: center;
    }

    .screen-content-body-text h1 {
        font-size: 2.5rem;
        font-weight: bold;
        color: #444;
    }

    .screen-content-body-text p {
        font-size: 1.5rem;
        color: #555;
    }
</style>

</html>