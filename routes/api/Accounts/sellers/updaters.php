<?php

use Illuminate\Support\Facades\Route;

Route::patch('account-info', 'updateAccountInfo');
Route::patch('user-info', 'updateUserInfo');
Route::patch('hired-info', 'updateHiredSeller')->middleware(['approved', 'admin']);