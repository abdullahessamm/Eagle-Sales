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
    
    //testing
    Route::get('/my-token', function() {
        return request()->user();
    })->middleware('auth');

    //Not found routes
    Route::any('/{params?}', function () {
        return response()->json(['msg' => 'Route not found'], 404);
    })->where('params', '.*');
}