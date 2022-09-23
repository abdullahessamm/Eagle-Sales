<?php

use Illuminate\Support\Facades\Route;

Route::post('upload-profile-picture', 'uploadProfilePicture');
Route::post('upload-vat-certificate', 'uploadVatCertificate');
Route::delete('delete-profile-picture', 'deleteProfilePicture');