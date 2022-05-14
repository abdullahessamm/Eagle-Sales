<?php

use App\Http\Controllers\Places\AvailablePlacesController;
use Illuminate\Support\Facades\Route;

Route::controller(AvailablePlacesController::class)->group(function () {
    Route::get('/', 'getPlaces');
    Route::put('/add-country', 'addCountry');
    Route::put('/add-city', 'addCity');
    Route::patch('/update-country', 'updateCountry');
    Route::patch('/update-city', 'updateCity');
    Route::delete('/delete-country', 'deleteCountry');
    Route::delete('/delete-city', 'deleteCity');
});