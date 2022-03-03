<?php

use App\Http\Controllers\TestController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

if (env("APP_MAINTENANCE")) {
    Route::any('/{params?}', function () {
        return response()->json(['msg' => 'server under maintenance mode'], 503);
    })->where('params', '.*');
} else {
    //Application routes
    Route::middleware(['guest'])->group(function () {
        Route::post('/login', [App\Http\Controllers\AuthController::class, 'login']);
        Route::get('/auth/{serialAccessToken}', [App\Http\Controllers\AuthController::class, 'getSerial']);
    });
    
    
    
    //testing
    Route::get('/my-token', function() {
        if (cache()->has(request()->bearerToken())) {
            $cachedValue = cache()->get(request()->bearerToken());
            return $cachedValue;
        }
        return auth()->user();
    })->middleware('auth');

    //Not found routes
    Route::any('/{params?}', function () {
        return response()->json(['msg' => 'Not Found'], 404);
    })->where('params', '.*');
}