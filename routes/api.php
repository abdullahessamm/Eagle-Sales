<?php

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
    
    Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
        return $request->user();
    });
    
    
    
    //Not found routes
    Route::any('/{params?}', function () {
        return response()->json(['msg' => 'Not Found'], 404);
    })->where('params', '.*');
}