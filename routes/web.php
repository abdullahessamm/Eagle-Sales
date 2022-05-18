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

if (env('APP_MAINTENANCE')) {
    Route::get('/{params?}', function () {
        $view = view('errors.503');
        return response($view, 503);
    })->where('params', '.*');
} else {
    Route::view('/', 'landing')->name('landing');

    Route::view('/auth/{params?}', 'auth')->where('params', '.*')->middleware('availableCountry');

    Route::get('/lang/{lang}', function ($lang) {
        $cookie = cookie('lang', $lang, 60 * 24 * 30 * 12 * 80, null, '.' . env('APP_URL'), false, false);
        return redirect()->back()->withCookie($cookie);
    })->where('lang', 'en|ar|in');

    Route::get('/verify-mail/{userID}', [App\Http\Controllers\MailController::class, 'verifyMail'])
        ->where('userID', '^\d+$')->name('verify mail');
}
