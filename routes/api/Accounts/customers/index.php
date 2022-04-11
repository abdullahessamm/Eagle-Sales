<?php

use App\Http\Controllers\Accounts\Customers\Getters;
use App\Http\Controllers\Accounts\Customers\Updaters;
use Illuminate\Support\Facades\Route;

Route::controller(Getters::class)
->prefix('get')
->group(__DIR__ . '/getters.php');

Route::controller(Updaters::class)
->prefix('update')
->group(__DIR__ . '/updaters.php');

Route::controller(App\Http\Controllers\Accounts\CustomerCategoryController::class)
->prefix('/categories')
->group(__DIR__ . '/categories.php');