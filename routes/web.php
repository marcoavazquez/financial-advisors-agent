<?php

use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;
use Laravel\Socialite\Facades\Socialite;

// Auth
Route::get('/login', fn () => view('auth.login'))->name('login');
Route::get('/auth/redirect', [AuthController::class, 'redirect'])->name('auth.redirect');
Route::get('/auth/callback', [AuthController::class, 'callback'])->name('auth.callback');

Route::group(['middleware' => 'auth'], function () {

    // Hubspot
    Route::get('/hubspot/redirect', [\App\Http\Controllers\HubspotController::class, 'redirect'])
        ->name('hubspot.redirect');
    Route::get('/hubspot/callback', [\App\Http\Controllers\HubspotController::class, 'callback'])
        ->name('hubspot.callback');
    Route::post('/hubspot/sync', [\App\Http\Controllers\HubspotController::class, 'sync'])
        ->name('hubspot.sync');

    // Google
    Route::post('/google/sync', [\App\Http\Controllers\GoogleController::class, 'sync'])
        ->name('google.sync');

    // Messages
    Route::get('/chat-messages', [\App\Http\Controllers\ChatMessagesController::class, 'index'])
        ->name('chat-messages.index');
    Route::post('/chat-messages', [\App\Http\Controllers\ChatMessagesController::class, 'store'])
        ->name('chat-messages.store');

    Route::get('/{path?}', fn () => view('index'))->where('path', '.*')->name('home');
});

