<?php

use Illuminate\Support\Facades\Route;

Route::put('create', 'addComment');
Route::delete('delete/{id}', 'deleteComment')
->where('id', '^[0-9]+$');