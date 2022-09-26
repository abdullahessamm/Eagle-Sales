<?php

use Illuminate\Support\Facades\Route;

Route::controller(\App\Http\Controllers\Sales\InvoicesController::class)
->group(function () {
    Route::get('/', 'get');
    Route::put('/create', 'create');
});

Route::prefix('/orders')
->controller(\App\Http\Controllers\Sales\OrdersController::class)
->group(function () {
    Route::get('/', 'get');
    Route::patch('/update-state', 'updateState');
});