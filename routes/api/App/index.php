<?php

use Illuminate\Support\Facades\Route;

Route::controller(\App\Http\Controllers\Application\ConfigController::class)
->prefix('/config')
->middleware(['auth', 'approved', 'admin'])
->group(__DIR__ . '/config.php');