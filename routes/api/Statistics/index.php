<?php

use App\Http\Controllers\Application\StatisticsController;
use Illuminate\Support\Facades\Route;

Route::controller(StatisticsController::class)
->middleware(['auth', 'approved'])
->group(function () {
    Route::get('/sales-by-sellers', 'salesBySellers');
    Route::get('/sales-by-suppliers', 'salesBySuppliers');
    Route::get('/sales-by-categories', 'salesByCategories');
    Route::get('/sales-by-order-state', 'salesByOrderState');
    Route::get('/sales-by-month', 'salesByMonth');
    Route::get('/sales-by-product', 'salesByProduct');
    Route::get('/seller-commission-by-year', 'sellerCommissionByYear');
});