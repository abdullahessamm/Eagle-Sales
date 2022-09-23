<?php

use Illuminate\Support\Facades\Route;

Route::post('/upload-image', 'uploadImage');
Route::post('/upload-cover-image', 'uploadCoverImage');
Route::delete('/delete-image', 'deleteImage');
Route::post('/upload-video', 'uploadVideo');
Route::delete('/delete-video', 'deleteVideo');