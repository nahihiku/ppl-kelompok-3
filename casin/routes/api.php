<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\GoogleAuthController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes - CasIn.id
|--------------------------------------------------------------------------
|
| [BE-02] Endpoint API Registrasi & Login (Hashing password & Sanctum Token)
| [BE-03] Integrasi Google OAuth (Laravel Socialite)
|
*/

// [BE-02] Public Authentication Routes
Route::post('/register', [AuthController::class, 'register'])
    ->middleware('throttle:10,1')
    ->name('api.register');

Route::post('/login', [AuthController::class, 'login'])
    ->middleware('throttle:10,1')
    ->name('api.login');

// [BE-03] Google OAuth Routes (Laravel Socialite)
Route::prefix('auth/google')->group(function () {
    Route::get('/redirect', [GoogleAuthController::class, 'redirect'])
        ->middleware('throttle:15,1')
        ->name('api.google.redirect');

    Route::get('/callback', [GoogleAuthController::class, 'callback'])
        ->name('api.google.callback');

    Route::post('/', [GoogleAuthController::class, 'loginWithToken'])
        ->middleware('throttle:15,1')
        ->name('api.google.token');
});

// Protected routes (Sanctum Token)
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', [AuthController::class, 'me'])->name('api.user');
    Route::get('/me', [AuthController::class, 'me'])->name('api.me');
    Route::post('/logout', [AuthController::class, 'logout'])->name('api.logout');
});
