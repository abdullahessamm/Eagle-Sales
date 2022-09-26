<?php

use Illuminate\Support\Facades\Route;

Route::get('/', 'get');
Route::put('/create', 'create');
Route::patch('visit', 'visit');
Route::delete('delete', 'delete');