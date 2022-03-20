<?php

use Illuminate\Support\Facades\Route;

Route::post('/login', 'login')->middleware('guest');
Route::get('/auth/{serialAccessToken}', 'getSerial')->middleware('guest');
Route::delete('/logout', 'logout')->middleware('auth');