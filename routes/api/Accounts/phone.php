<?php

use Illuminate\Support\Facades\Route;

Route::post('confirm', 'confirm');
Route::post('/resend-code', 'resendCode');
Route::post('check-unique', 'checkUnique');