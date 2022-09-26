<?php

use App\Http\Controllers\Places\AvailablePlacesController;
use Illuminate\Support\Facades\Route;

Route::controller(AvailablePlacesController::class)->group(function () {
    Route::get('/', 'getPlaces');
    Route::put('/add-country', 'addCountry')->middleware(['auth', 'approved', 'admin']);
    Route::put('/add-city', 'addCity')->middleware(['auth', 'approved', 'admin']);
    Route::patch('/update-country', 'updateCountry')->middleware(['auth', 'approved', 'admin']);
    Route::patch('/update-city', 'updateCity')->middleware(['auth', 'approved', 'admin']);
    Route::delete('/delete-country', 'deleteCountry')->middleware(['auth', 'approved', 'admin']);
    Route::delete('/delete-city', 'deleteCity')->middleware(['auth', 'approved', 'admin']);
});