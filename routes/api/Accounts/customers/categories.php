<?php

use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'approved', 'admin'])->group(function () {
    Route::put('create', 'create');
    Route::patch('update', 'update');
});

Route::prefix('get')->group(function () {
    Route::get('/', 'getAll');
    Route::get('{id}', 'getById')->where('id', '^[0-9]+$');
    Route::get('{name}', 'getByName')->where('name', '^[a-zA-Z]+$');
    Route::get('{nameAr}', 'getByNameAR')->where('nameAr', '^[\x{0621}-\x{064A}]+$');
});