<?php

declare(strict_types=1);

use App\Http\Controllers\LoginController;
use App\Http\Controllers\LogoutController;
use App\Http\Controllers\TwoFactorChallengeController;
use Illuminate\Support\Facades\Route;

Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'index'])
        ->name('login');
    Route::post('/login', [LoginController::class, 'store'])
        ->middleware('throttle:5,1')
        ->name('login.store');

    Route::get('/two-factor-challenge', [TwoFactorChallengeController::class, 'index'])
        ->name('two-factor-challenge');
});

Route::middleware('auth')->group(function () {
    Route::get('/', fn() => inertia('Home'))->name('home');

    Route::post('/logout', LogoutController::class);
});
