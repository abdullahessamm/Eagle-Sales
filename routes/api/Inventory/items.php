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
});

// media routes
Route::controller(\App\Http\Controllers\Media\Items::class)
->prefix('/media')
->middleware('auth')
->group(function () {
    Route::post('/upload-image', 'uploadImage');
    Route::post('/upload-cover-image', 'uploadCoverImage');
    Route::delete('/delete-image', 'deleteImage');
    Route::post('/upload-video', 'uploadVideo');
    Route::delete('/delete-video', 'deleteVideo');
});