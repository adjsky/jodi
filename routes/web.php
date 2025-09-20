<?php

declare(strict_types=1);

use App\Http\Controllers\LoginController;
use App\Http\Controllers\LoginTwoFactorController;
use App\Http\Controllers\LogoutController;
use Illuminate\Support\Facades\Route;

Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'index'])
        ->name('login');
    Route::post('/login', [LoginController::class, 'store'])
        ->name('login.store');

    Route::get('/login/2fa', [LoginTwoFactorController::class, 'index'])
        ->name('login.two-factor');
});

Route::middleware('auth')->group(function () {
    Route::get('/', fn () => inertia('Home'))->name('home');

    Route::post('/logout', LogoutController::class);
});
