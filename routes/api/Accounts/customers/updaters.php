<?php

use Illuminate\Support\Facades\Route;

Route::patch('account-info', 'updateAccountInfo')->middleware('auth');
Route::patch('user-info', 'updateUserInfo')->middleware('auth');