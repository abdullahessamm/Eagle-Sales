<?php

use Illuminate\Support\Facades\Cookie;
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

// set default language
if (!session()->has('lang')) {
    session()->put('lang', 'en');
}

if (env('APP_MAINTENANCE')) {
    Route::get('/{params?}', function () {
        $view = view('errors.503');
        return response($view, 503);
    })->where('params', '.*');
} else {
    Route::get('/', function () {
        app()->setLocale(session()->get('lang'));
        return view('landing');
    })->name('landing');

    Route::view('/auth/{params?}', 'auth')->where('params', '.*');

    Route::get('/store/{params?}', function () {
        app()->setLocale(session()->get('lang'));
        return view('store');
    })->where('params', '.*')->name('store');

    Route::get('/lang/{lang}', function ($lang) {
        session()->put('lang', $lang);
        return redirect()->back();
    })->where('lang', 'en|ar|in');

    Route::get('/verify-mail/{userID}', [App\Http\Controllers\MailController::class, 'verifyMail'])
        ->where('userID', '^\d+$')->name('verify mail');
        
}
