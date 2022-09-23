<?php

use Illuminate\Support\Facades\Route;

Route::controller(\App\Http\Controllers\Sales\InvoicesController::class)
->middleware(['auth', 'approved'])
->group(function () {
    Route::get('/', 'get');
    Route::put('/create', 'create');
});

Route::prefix('/orders')
->controller(\App\Http\Controllers\Sales\OrdersController::class)
->middleware(['auth', 'approved'])
->group(function () {
    Route::get('/', 'get');
    Route::patch('/update-state', 'updateState');
});