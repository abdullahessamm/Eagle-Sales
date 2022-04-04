<?php

use Illuminate\Support\Facades\Route;

Route::patch('account-info', 'accountInfo');
Route::patch('user-info', 'update');
Route::patch('permissions', 'updatePermissions');