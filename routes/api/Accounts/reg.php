<?php

use Illuminate\Support\Facades\Route;

Route::put('supplier', 'registerSupplier');

Route::prefix('seller')->group(function () {
    Route::put('hired', 'registerHiredSeller')
    ->middleware(['auth', 'approved', 'admin']);
    
    Route::put('freelancer', 'registerFreelancerSeller');
    
    Route::post('rewards', 'registerSellerRewards')->middleware(['auth', 'approved']);
});

Route::put('customer', 'registerCustomer');

Route::put('client', 'registerOnlineClient');

Route::put('admin', 'registerAdmin')->middleware(['auth', 'approved', 'admin']);

Route::prefix('check-unique')->group(function () {
    Route::post('users', 'checkUniqueFieldInUsers');
    Route::post('suppliers', 'checkUniqueFieldInSuppliers');
    Route::post('sellers', 'checkUniqueFieldInSellers');
    Route::post('customers', 'checkUniqueFieldInCustomers');
});