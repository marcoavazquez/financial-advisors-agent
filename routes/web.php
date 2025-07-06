<?php

use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;
use Laravel\Socialite\Facades\Socialite;

// Auth
Route::get('/login', fn () => view('auth.login'))->name('login');
Route::get('/auth/redirect', [AuthController::class, 'redirect'])->name('auth.redirect');
Route::get('/auth/callback', [AuthController::class, 'callback'])->name('auth.callback');

Route::group(['middleware' => 'auth'], function () {
    Route::get('/{path?}', fn () => view('index'))->where('path', '.*');
});

