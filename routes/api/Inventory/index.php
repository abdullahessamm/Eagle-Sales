<?php

use Illuminate\Support\Facades\Route;

Route::prefix('/items')
->group(__DIR__ . '/Items/index.php');

Route::prefix('/categories')
->controller(\App\Http\Controllers\Inventory\CategoryController::class)
->group(function () {
    Route::put('/', 'createCategory');
    Route::patch('/', 'updateCategory');
    Route::get('/', 'getCategories');
    Route::get('/{id}', 'getCategory')
    ->where('id', '^[0-9]+$');
});