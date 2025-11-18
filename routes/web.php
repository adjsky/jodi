<?php

declare(strict_types=1);

use App\Domain\Auth\Notifications;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\TodoController;
use App\Http\Controllers\TwoFactorChallengeController;
use Illuminate\Support\Facades\Route;

Route::middleware('guest')->group(function () {
    Route::prefix('/login')
        ->controller(LoginController::class)
        ->group(function () {
            Route::get('/', 'show')->name('login');
            Route::post('/', 'login');
        });

    Route::prefix('/two-factor-challenge')
        ->controller(TwoFactorChallengeController::class)
        ->group(function () {
            Route::get('/', 'show')->name('two-factor-challenge');
            Route::post('/consume', 'consume');
            Route::post('/resend', 'resend');
        });
});

Route::middleware('auth')->group(function () {
    Route::get('/', [HomeController::class, 'show'])->name('home');
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

    Route::prefix('/todo')
        ->controller(TodoController::class)
        ->group(function () {
            Route::post('/', 'store');
            Route::patch('/{id}', 'update');
            Route::delete('/{id}', 'destroy');
            Route::post('/{id}/complete', 'complete');
        });
});

if (app()->isLocal()) {
    Route::get(
        '/mail/otp',
        fn () => new Notifications\OneTimeLoginCode('042712')->toMail()
    );
}
