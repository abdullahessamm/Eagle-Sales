<?php

use Illuminate\Support\Facades\Route;

Route::post('confirm', 'confirm')->middleware('auth');
Route::post('/resend-code', 'resendCode')->middleware('auth');
Route::post('check-unique', 'checkUnique');