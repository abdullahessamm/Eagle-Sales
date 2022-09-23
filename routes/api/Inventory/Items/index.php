<?php

use Illuminate\Support\Facades\Route;

Route::controller(\App\Http\Controllers\Inventory\ItemController::class)
->group(function () {
    Route::get('/', 'getItems');

    Route::get('/{id}', 'getItemById')
    ->where('id', '^[0-9]+$');

    Route::put('/create', 'create')
    ->middleware('auth');

    Route::post('/excel-upload', 'uploadItemsUsingExcel')
    ->middleware('auth');

    Route::post('/approvals/{id}', 'approveOrReject')
    ->where('id', '^[0-9]+$')
    ->middleware('auth');

    Route::patch('/update/{id}', 'update')
    ->where('id', '^[0-9]+$')
    ->middleware('auth');

    Route::patch('/change-status/{id}', 'changeStatus')
    ->where('id', '^[0-9]+$')
    ->middleware('auth');

    Route::patch('/activate/{id}', 'activate')
    ->where('id', '^[0-9]+$')
    ->middleware('auth');

    Route::patch('/deactivate/{id}', 'deactivate')
    ->where('id', '^[0-9]+$')
    ->middleware('auth');
});

// UOMs routes
Route::controller(\App\Http\Controllers\Inventory\UomController::class)
->prefix('/uoms')
->middleware('auth')
->group(__DIR__ . '/uoms.php');

// Promotions routes
Route::controller(\App\Http\Controllers\Inventory\PromotionController::class)
->prefix('/promotions')
->middleware('auth')
->group(__DIR__ . '/promotions.php');

// Rates routes
Route::controller(\App\Http\Controllers\Inventory\RateController::class)
->prefix('/rates')
->middleware('auth')
->group(__DIR__ . '/rates.php');

// Comments routes
Route::controller(\App\Http\Controllers\Inventory\CommentController::class)
->prefix('/comments')
->middleware('auth')
->group(__DIR__ . '/comments.php');

// media routes
Route::controller(\App\Http\Controllers\Media\Items::class)
->prefix('/media')
->middleware('auth')
->group(__DIR__ . '/media.php');