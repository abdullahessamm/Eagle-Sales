<?php

use Illuminate\Support\Facades\Route;

Route::prefix('/suppliers')
->group(function () {
    Route::get('/', 'getOurCommissions');
    
    Route::patch('/obtain/{id}', 'obtainSupplierCommission')
    ->where('id', '^[0-9]+$');
});

Route::prefix('/sellers')
->group(function () {
    Route::get('/', 'getSellersCommissions');
    
    Route::patch('/withdraw/{id}', 'withdrawSellerCommissions')
    ->where('id', '^[0-9]+$');
});