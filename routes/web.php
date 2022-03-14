<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

// Route::get('/{params?}', function () {
//     $view = view('errors.503');
//     return response($view, 503);
// })->where('params', '.*');

if (env('APP_MAINTENANCE')) {
    Route::get('/{params?}', function () {
        $view = view('errors.503');
        return response($view, 503);
    })->where('params', '.*');
} else {
    Route::get('/', function () {
        return view('welcome');
    });

    Route::get('/verify-mail/{userID}', [App\Http\Controllers\MailController::class, 'verifyMail'])
        ->where('userID', '^\d+$');
}
