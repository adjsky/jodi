<?php

declare(strict_types=1);

use App\Domain\Identity;
use App\Domain\Identity\Mail;
use App\Domain\Identity\Models\User;
use Illuminate\Support\Facades\Route;

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
        fn () => new Identity\Notifications\OneTimeLoginCode('042712')->toMail()
    );

    Route::get(
        '/mail/invite-to-jodi',
        fn () => new Mail\InviteToJodi(
            $user,
            'http://example.com'
        )
    );
}
