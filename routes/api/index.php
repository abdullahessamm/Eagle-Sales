<?php

use Illuminate\Support\Facades\Route;

if (env("API_MAINTENANCE")) {
    Route::any('/{params?}', function () {
        return response()->json(['msg' => 'server under maintenance mode'], 503);
    })->where('params', '.*');
} else {
    //Accounts routes
    Route::prefix('/accounts')->group(__DIR__ . '/Accounts/index.php');
    
    //Inventory routes
    Route::prefix('/inventory')->group(__DIR__ . '/Inventory/index.php');

    // Invoices routes
    Route::prefix('/invoices')->group(__DIR__ . '/Invoices/index.php');

    //Location routes
    Route::prefix('/location')->group(__DIR__ . '/Location/index.php');

    //Places routes
    Route::prefix('/places')->group(__DIR__ . '/Places/index.php');

    //Statistics routes
    Route::prefix('/statistics')->group(__DIR__ . '/Statistics/index.php');

    //App routes
    Route::prefix('/app')->group(__DIR__ . '/App/index.php');

    //Not found routes
    Route::any('/{params?}', function () {
        return response()->json(['msg' => 'Route not found'], 404);
    })->where('params', '.*');
}