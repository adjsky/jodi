<?php

declare(strict_types=1);

use App\Domain\Event\Models\Event;
use App\Domain\Identity\Mail;
use App\Domain\Identity\Models\User;
use App\Domain\Identity\Notifications as AuthNotifications;
use App\Domain\Reminder\Notifications as ReminderNotifications;
use App\Domain\Todo\Models\Todo;
use App\Infrastructure\Firebase\Actions\GetFirebaseMessagingServiceWorker;
use Carbon\Carbon;
use Illuminate\Support\Facades\Route;

Route::get('/firebase-messaging-sw.js', GetFirebaseMessagingServiceWorker::class);

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
        fn () => new ReminderNotifications\EventReminder(
            new Event(['title' => 'Take pills', 'starts_at' => Carbon::now()->addHours(3)]),
            null
        )->toMail($user)
    );

    Route::get(
        '/mail/todo-reminder',
        fn () => new ReminderNotifications\TodoReminder(
            new Todo(['title' => 'Take pills', 'scheduled_at' => Carbon::now()->addHours(3)]),
            null
        )->toMail($user)
    );
}
