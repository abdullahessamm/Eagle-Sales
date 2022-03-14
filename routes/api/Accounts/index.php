<?php

use Illuminate\Support\Facades\Route;

Route::controller(App\Http\Controllers\Accounts\AuthController::class)
->middleware(['guest'])
->group(__DIR__ . '/auth.php');

Route::controller(App\Http\Controllers\Accounts\RegisterController::class)
->prefix('/register')->group(__DIR__ . '/reg.php');