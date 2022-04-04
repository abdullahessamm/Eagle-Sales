<?php

use Illuminate\Support\Facades\Route;

Route::post('/login', 'login')->middleware('guest');
Route::get('/get-serial/{serialAccessToken}', 'getSerial')->middleware('guest');
Route::delete('/logout', 'logout')->middleware('auth');
Route::get('/me', 'getAuthedUserInfo')->middleware('auth');