<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthenticatedSessionController;

Route::get('/', function () {
    return view('welcome');
})->name('home');

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthenticatedSessionController::class, 'createLogin'])->name('login');
    Route::post('/login', [AuthenticatedSessionController::class, 'storeLogin'])
        ->middleware('throttle:10,1')->name('login.store');
    Route::get('/register', [AuthenticatedSessionController::class, 'createRegistration'])->name('register');
    Route::post('/register', [AuthenticatedSessionController::class, 'storeRegistration'])
        ->middleware('throttle:5,1')->name('register.store');
    Route::get('/auth/google/redirect', [AuthenticatedSessionController::class, 'redirectToGoogle'])
        ->middleware('throttle:10,1')->name('google.redirect');
    Route::get('/auth/google/callback', [AuthenticatedSessionController::class, 'handleGoogleCallback'])
        ->name('google.callback');
});

Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])
    ->middleware('auth')->name('logout');
