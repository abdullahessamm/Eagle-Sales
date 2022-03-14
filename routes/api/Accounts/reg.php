<?php

use Illuminate\Support\Facades\Route;

Route::put('supplier', 'registerSupplier');

Route::prefix('seller')->group(function () {
    Route::put('hired', 'registerHiredSeller')
    ->middleware(['auth', 'admin', 'createSeller']);
    Route::put('freelancer', 'registerFreelancerSeller');
});
