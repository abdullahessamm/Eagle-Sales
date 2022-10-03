<?php

use App\Http\Controllers\Accounts\Sellers\Getters;
use App\Http\Controllers\Accounts\Sellers\Updaters;
use Illuminate\Support\Facades\Route;

Route::controller(Getters::class)
->prefix('get')
->group(__DIR__ . '/getters.php');

Route::controller(Updaters::class)
->prefix('update')
->group(__DIR__ . '/updaters.php');