<?php

use Illuminate\Support\Facades\Route;

if (env("API_MAINTENANCE")) {
    Route::any('/{params?}', function () {
        return response()->json(['msg' => 'server under maintenance mode'], 503);
    })->where('params', '.*');
} else {
    //Accounts routes
    Route::prefix('/accounts')->group(__DIR__ . '/Accounts/index.php');
    
    //Items routes
    Route::prefix('/items')->group(__DIR__ . '/Items/index.php');

    //Locations route (for now)
    Route::get('/ip-location', function () {
        $ip = request()->ip();
        if (env('APP_DEVELOPER_MATCHINE'))
            $ip = $ip === '127.0.0.1' ? '41.40.244.201' : $ip;
        
        $location = geoip()->getLocation($ip)->toArray();
        return response()->json([
            'success' => true,
            'data' => $location
        ]);
    });

    //Not found routes
    Route::any('/{params?}', function () {
        return response()->json(['msg' => 'Route not found'], 404);
    })->where('params', '.*');
}