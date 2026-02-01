<?php

declare(strict_types=1);

use App\Domain\Auth\Mail;
use App\Domain\Auth\Notifications as AuthNotifications;
use App\Domain\Event\Notifications as EventNotifications;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CurrentUserController;
use App\Http\Controllers\DaySummaryController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\FriendController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\PushSubscriptionController;
use App\Http\Controllers\RegistrationInvitationController;
use App\Http\Controllers\SignupController;
use App\Http\Controllers\TodoController;
use App\Http\Controllers\TwoFactorChallengeController;
use App\Models\Event;
use App\Models\User;
use Carbon\Carbon;
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
            // TODO: a regular string syntax coflicts with resend-laravel
            // package for some reason. Maybe there is a better solution? idk,
            // maybe i should create a bug report.
            Route::post('/resend', [TwoFactorChallengeController::class, 'resend']);
        });
});

Route::middleware('auth')->group(function () {
    Route::get('/', [HomeController::class, 'show'])->name('home');
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

    Route::prefix('/categories')
        ->controller(CategoryController::class)
        ->group(function () {
            Route::post('/', 'create');
            Route::delete('/{name}', 'destroy');
        });

    Route::prefix('/todos')
        ->controller(TodoController::class)
        ->group(function () {
            Route::post('/', 'create');
            Route::post('/reorder', 'reorder');
            Route::patch('/{todo}', 'update');
            Route::delete('/{todo}', 'destroy');
            Route::post('/{todo}/complete', 'complete');
        });

    Route::prefix('/events')
        ->controller(EventController::class)
        ->group(function () {
            Route::post('/', 'create');
            Route::patch('/{event}', 'update');
            Route::delete('/{event}', 'destroy');
        });

    Route::prefix('/me')
        ->controller(CurrentUserController::class)
        ->group(function () {
            Route::patch('/', 'update');
        });

    Route::prefix('/me/invitations')
        ->controller(RegistrationInvitationController::class)
        ->group(function () {
            Route::get('/', 'getAll');
            Route::get('/{invitation}', 'get');
            Route::delete('/{invitation}', 'destroy');
            Route::post('/invite', 'invite');
        });

    Route::prefix('/me/friends')
        ->controller(FriendController::class)
        ->group(function () {
            Route::get('/', 'getAll');
        });

    Route::prefix('/push-subscriptions')
        ->controller(PushSubscriptionController::class)
        ->group(function () {
            Route::post('/', 'create');
            Route::delete('/', 'destroy');
        });

    Route::get('/day-summary/{year}', [DaySummaryController::class, 'get']);
});

if (app()->isLocal()) {
    Route::get(
        '/mail/otp',
        fn () => new AuthNotifications\OneTimeLoginCode('042712')->toMail()
    );
    Route::get(
        '/mail/invite-to-jodi',
        fn () => new Mail\InviteToJodi(
            new User(['email' => 'kirill.t@tuta.io', 'name' => 'Kirill T.']),
            'http://example.com'
        )
    );
    Route::get(
        '/mail/event-reminder',
        fn () => new EventNotifications\EventReminder(
            new Event(['title' => 'Take pills', 'starts_at' => Carbon::now()->addHours(3)])
        )->toMail()
    );
}
