<?php

declare(strict_types=1);

use App\Domain\Auth\Mail;
use App\Domain\Auth\Notifications;
use App\Http\Controllers\CurrentUserController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\RegistrationInvitationController;
use App\Http\Controllers\SignupController;
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

    Route::prefix('/signup')
        ->controller(SignupController::class)
        ->group(function () {
            Route::get('/{code}', 'show')->name('signup');
            Route::post('/{code}', 'signup');
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
            Route::post('/', 'create');
            Route::patch('/{todo}', 'update');
            Route::delete('/{todo}', 'destroy');
            Route::post('/{todo}/complete', 'complete');
            Route::post('/{todo}/reorder', 'reorder');
        });

    Route::prefix('/me')
        ->controller(CurrentUserController::class)
        ->group(function () {
            Route::get('/', 'index')->name('me');
            Route::patch('/', 'update');
            Route::get('/name', 'name');
            Route::get('/email', 'email');
            Route::get('/friends', 'friends');
            Route::get('/language', 'language');
            Route::get('/week-start', 'weekStart');
        });

    Route::prefix('/me/invitations')
        ->controller(RegistrationInvitationController::class)
        ->group(function () {
            Route::get('/', 'index')->name('invitations');
            Route::get('/{invitation}', 'show');
            Route::delete('/{invitation}', 'destroy');
            Route::post('/invite', 'invite');
        });
});

if (app()->isLocal()) {
    Route::get(
        '/mail/otp',
        fn () => new Notifications\OneTimeLoginCode('042712')->toMail()
    );
    Route::get(
        '/mail/invite-to-jodi',
        fn () => new Mail\InviteToJodi('kirill.t@tuta.io', 'http://example.com')
    );
}
