<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="{{url('images/Logo.png')}}">
    <title>{{env('APP_NAME')}} | 503</title>
</head>
<style>
    body {
        display: flex;
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        justify-content: center;
        align-items: center;
        flex-direction: row;
    }

    img {
        width: 50%;
        min-width: 50px;
        max-width: 86px;
    }

    .company {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
    }

    .logo {
        display: flex;
        justify-content: center;
        transform: translateX(-4px);
    }

    h4 {
        margin: 0;
        padding: 0;
        text-align: center;
        margin-top: 5px;
        color: #115478;
    }
</style>
<body>
    <div class="company">
        <div class="logo">
            <img src="{{url('images/Logo1.png')}}" alt="logo" draggable="false">
        </div>
        <h4> Eagle sales </h4>
    </div>
    <div class="separator"></div>
    <h2> Comming soon </h2>
</body>
</html>