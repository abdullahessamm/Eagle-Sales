<?php

use Illuminate\Support\Facades\Route;

Route::controller(App\Http\Controllers\Accounts\AuthController::class)
->group(__DIR__ . '/auth.php');

Route::controller(App\Http\Controllers\Media\Users::class)
->prefix('/media')
->middleware('auth')
->group(__DIR__ . '/media.php');

Route::controller(App\Http\Controllers\PhoneController::class)
->prefix('/phone')
->group(__DIR__ . '/phone.php');

Route::controller(App\Http\Controllers\Accounts\RegisterController::class)
->prefix('/register')
->group(__DIR__ . '/reg.php');

Route::prefix('/backoffice')
->middleware(['auth', 'approved', 'admin'])
->group(__DIR__ . '/backoffice/index.php');

Route::prefix('/customers')
->group(__DIR__ . '/customers/index.php');

Route::prefix('/sellers')
->middleware(['auth'])
->group(__DIR__ . '/sellers/index.php');

Route::prefix('/suppliers')
->middleware(['auth'])
->group(__DIR__ . '/suppliers/index.php');

// Other routes for account update
Route::controller(App\Http\Controllers\Accounts\UserUpdater::class)
->group(function () {
    Route::patch('ban/{id}', 'ban')->where('id', '^[0-9]+$')->middleware('auth');
    Route::patch('reactivate/{id}', 'reactivate')->where('id', '^[0-9]+$')->middleware('auth');
    Route::patch('approve/{id}', 'approve')->where('id', '^[0-9]+$')->middleware('auth');
    Route::patch('decline/{id}', 'decline')->where('id', '^[0-9]+$')->middleware('auth');
    Route::patch('change-password', 'changePassword')->middleware('auth');
});