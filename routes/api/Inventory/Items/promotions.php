<?php

use Illuminate\Support\Facades\Route;

Route::put('create', 'addPromotion');
Route::patch('update', 'updatePromotion');
Route::patch('activate', 'activatePromotion');
Route::patch('deactivate', 'deactivatePromotion');