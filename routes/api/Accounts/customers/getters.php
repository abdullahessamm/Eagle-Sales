<?php

use Illuminate\Support\Facades\Route;

Route::get('all', 'getAll')->middleware(['auth', 'approved', 'admin']);
Route::get('{id}', 'getById')->where('id', '^[1-9][0-9]*$')->middleware('auth');