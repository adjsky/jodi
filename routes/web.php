<?php

declare(strict_types=1);

use App\Domain\Event\Actions\CreateEvent;
use App\Domain\Event\Actions\DestroyEvent;
use App\Domain\Event\Actions\GetDaySummary;
use App\Domain\Event\Actions\UpdateEvent;
use App\Domain\Event\Models\Event;
use App\Domain\Identity\Actions\AuthenticateUser;
use App\Domain\Identity\Actions\CompleteTwoFactorChallenge;
use App\Domain\Identity\Actions\CreateRegistrationInvitation;
use App\Domain\Identity\Actions\DestroyRegistrationInvitation;
use App\Domain\Identity\Actions\GetRegistrationInvitation;
use App\Domain\Identity\Actions\ListFriends;
use App\Domain\Identity\Actions\ListRegistrationInvitations;
use App\Domain\Identity\Actions\LogoutUser;
use App\Domain\Identity\Actions\RegisterUser;
use App\Domain\Identity\Actions\ResendTwoFactorChallengeCode;
use App\Domain\Identity\Actions\UpdateUser;
use App\Domain\Identity\Actions\UpsertPushSubscription;
use App\Domain\Identity\Mail;
use App\Domain\Identity\Models\User;
use App\Domain\Identity\Notifications as AuthNotifications;
use App\Domain\Reminder\Notifications as ReminderNotications;
use App\Domain\Todo\Actions\CompleteTodo;
use App\Domain\Todo\Actions\CreateCategory;
use App\Domain\Todo\Actions\CreateTodo;
use App\Domain\Todo\Actions\DestroyCategory;
use App\Domain\Todo\Actions\DestroyTodo;
use App\Domain\Todo\Actions\ReorderTodos;
use App\Domain\Todo\Actions\UpdateTodo;
use App\Domain\Todo\Models\Todo;
use App\Http\Controllers\FirebaseServiceWorkerController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\SignupController;
use App\Http\Controllers\TwoFactorChallengeController;
use Carbon\Carbon;
use Illuminate\Support\Facades\Route;

Route::get('/firebase-messaging-sw.js', FirebaseServiceWorkerController::class);

Route::middleware('guest')->group(function () {
    Route::prefix('/login')
        ->group(function () {
            Route::get('/', LoginController::class)->name('login');
            Route::post('/', AuthenticateUser::class);
        });

    Route::prefix('/signup')
        ->group(function () {
            Route::get('/{code}', SignupController::class)->name('signup');
            Route::post('/{code}', RegisterUser::class);
        });

    Route::prefix('/two-factor-challenge')
        ->group(function () {
            Route::get('/', TwoFactorChallengeController::class)->name('two-factor-challenge');
            Route::post('/', CompleteTwoFactorChallenge::class);
            Route::post('/resend', ResendTwoFactorChallengeCode::class);
        });
});

Route::middleware('auth')->group(function () {
    Route::get('/', HomeController::class)->name('home');
    Route::post('/logout', LogoutUser::class);

    Route::prefix('/categories')
        ->group(function () {
            Route::post('/', CreateCategory::class);
            Route::delete('/{category}', DestroyCategory::class);
        });

    Route::prefix('/todos')
        ->group(function () {
            Route::post('/', CreateTodo::class);
            Route::post('/reorder', ReorderTodos::class);
            Route::put('/{todo}', UpdateTodo::class);
            Route::delete('/{todo}', DestroyTodo::class);
            Route::post('/{todo}/complete', CompleteTodo::class);
        });

    Route::prefix('/events')
        ->group(function () {
            Route::post('/', CreateEvent::class);
            Route::put('/{event}', UpdateEvent::class);
            Route::delete('/{event}', DestroyEvent::class);
        });

    Route::prefix('/me')
        ->group(function () {
            Route::patch('/', UpdateUser::class);
        });

    Route::prefix('/me/invitations')
        ->group(function () {
            Route::post('/', CreateRegistrationInvitation::class);
            Route::get('/', ListRegistrationInvitations::class);
            Route::get('/{invitation}', GetRegistrationInvitation::class);
            Route::delete('/{invitation}', DestroyRegistrationInvitation::class);
        });

    Route::prefix('/me/friends')
        ->group(function () {
            Route::get('/', ListFriends::class);
        });

    Route::prefix('/push-subscriptions')
        ->group(function () {
            Route::post('/', UpsertPushSubscription::class);
        });

    Route::get('/day-summary/{year}', GetDaySummary::class);
});

if (app()->isLocal()) {
    $user = new User([
        'email' => 'kirill.t@tuta.io',
        'name' => 'Kirill T.',
        'preferences' => [
            'timezone' => 'Europe/Moscow',
        ],
    ]);

    Route::get(
        '/mail/otp',
        fn () => new AuthNotifications\OneTimeLoginCode('042712')->toMail()
    );

    Route::get(
        '/mail/invite-to-jodi',
        fn () => new Mail\InviteToJodi(
            $user,
            'http://example.com'
        )
    );

    Route::get(
        '/mail/event-reminder',
        fn () => new ReminderNotications\EventReminder(
            new Event(['title' => 'Take pills', 'starts_at' => Carbon::now()->addHours(3)]),
            null
        )->toMail($user)
    );

    Route::get(
        '/mail/todo-reminder',
        fn () => new ReminderNotications\TodoReminder(
            new Todo(['title' => 'Take pills', 'scheduled_at' => Carbon::now()->addHours(3)]),
            null
        )->toMail($user)
    );
}
