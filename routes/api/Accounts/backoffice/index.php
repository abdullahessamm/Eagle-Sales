<?php

use Illuminate\Support\Facades\Route;

Route::controller(App\Http\Controllers\Accounts\Backoffice\Getters::class)
->prefix('/get')
->group(__DIR__ . '/getters.php');

Route::controller(App\Http\Controllers\Accounts\Backoffice\Updaters::class)
->prefix('/update')
->group(__DIR__ . '/updaters.php');