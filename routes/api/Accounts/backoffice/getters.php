<?php

use Illuminate\Support\Facades\Route;

Route::get('all', 'getAll');
Route::get('{id}', 'getById')->where('id', '^[1-9][0-9]*$');