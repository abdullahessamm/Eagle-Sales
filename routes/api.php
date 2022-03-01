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
    Route::get('/test', function () {
        return response()->json(['msg' => 'test msg'], 200);
    });
    
    //Application routes
    Route::any('/test', [App\Http\Controllers\TestController::class, 'index']);
    
    
    //Not found routes
    Route::any('/{params?}', function () {
        return response()->json(['msg' => 'Not Found'], 404);
    })->where('params', '.*');
}