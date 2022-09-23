<?php

use Illuminate\Support\Facades\Route;

Route::prefix('/items')
->group(__DIR__ . '/Items/index.php');