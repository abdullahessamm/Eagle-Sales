<?php

use Illuminate\Support\Facades\Route;

Route::controller(App\Http\Controllers\Accounts\AuthController::class)
->group(__DIR__ . '/auth.php');

Route::controller(App\Http\Controllers\Accounts\RegisterController::class)
->prefix('/register')
->group(__DIR__ . '/reg.php');

Route::controller(App\Http\Controllers\PhoneController::class)
->prefix('/phone')
->group(__DIR__ . '/phone.php');

Route::controller(App\Http\Controllers\Accounts\CustomerCategoryController::class)
->prefix('/customerCategories')
->group(__DIR__ . '/customerCategory.php');