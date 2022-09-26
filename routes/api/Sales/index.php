<?php

use App\Http\Controllers\Sales\CommissionsController;
use App\Http\Controllers\Sales\JourneyPlansController;
use Illuminate\Support\Facades\Route;


// Invoices routes
Route::prefix('/invoices')
->middleware(['auth', 'approved'])
->group(__DIR__ . '/invoices.php');

// Commissions routes
Route::controller(CommissionsController::class)
->prefix('/commissions')
->middleware(['auth', 'approved'])
->group(__DIR__ . '/commissions.php');

// Journey plans routes
Route::controller(JourneyPlansController::class)
->prefix('/journey-plans')
->middleware(['auth', 'approved'])
->group(__DIR__ . '/journeyPlans.php');
