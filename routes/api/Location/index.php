<?php

use App\Http\Controllers\Location\UserLocationController;
use Illuminate\Support\Facades\Route;

Route::controller(UserLocationController::class)->group(function () {
    Route::get('/ip-location', 'getIpLocation');
    Route::get('/check-coords', 'checkCoordsLocation');
});