<?php

declare(strict_types=1);

return [
    'hello' => 'Hello,',
    'thanks' => 'Thanks',

    'otp' => [
        'subject' => ':code — is your one-time login code',
        'paragraphs' => [
            'hello' => 'Hello,',
            'code' => '**:code** — is your one-time login code. To log in, enter this code on the login screen. The code is valid for 15 minutes.',
            'ahtung' => 'Do not **share** this code with anyone. We **never** ask for this code. If you did not request this code, no action is required. If you have received multiple codes that you did not request, we strongly recommend linking a different email address to your account.',
        ],
    ],
    'invite_to_jodi' => [
        'subject' => 'Invitation to :app from :email',
        'paragraphs' => [
            'who' => 'User **:email** has invited you to join **:app** — a simple space for managing tasks and events.',
            'join' => 'Join **:name** to plan your time effectively and reach your goals together.',
            'ahtung' => 'You received this email because **:email** provided your address. If you do not know this person or do not wish to register, please simply ignore this message.',
        ],
        'actions' => [
            'create' => 'Create an account',
        ],
    ],
    'event_reminder' => [
        'subject' => ':title starts :startsIn',
        'paragraphs' => [
            'reminder' => 'This is a reminder that **:title** is scheduled for **:date**.',
        ],
    ],
];
