<?php

use Illuminate\Support\Facades\Route;

Route::post('/login', 'login');
Route::get('/auth/{serialAccessToken}', 'getSerial');
Route::delete('/logout', 'logout')->middleware('auth');