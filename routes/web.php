<?php

declare(strict_types=1);

use App\Http\Controllers\LoginController;
use App\Http\Controllers\TwoFactorChallengeController;
use App\Notifications;
use Illuminate\Support\Facades\Route;

Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'show'])->name('login');
    Route::post('/login', [LoginController::class, 'login']);

    Route::get(
        '/two-factor-challenge',
        [TwoFactorChallengeController::class, 'show']
    )->name('two-factor-challenge');
    Route::post(
        '/two-factor-challenge/consume',
        [TwoFactorChallengeController::class, 'consume']
    );
    Route::post(
        '/two-factor-challenge/resend',
        [TwoFactorChallengeController::class, 'resend']
    );
});

Route::middleware('auth')->group(function () {
    Route::get('/', fn () => inertia('Home'))->name('home');
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
});

if (app()->isLocal()) {
    Route::get(
        '/mail/otp',
        fn () => new Notifications\OneTimeLoginCode('042712')->toMail()
    );
}
