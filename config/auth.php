<?php

declare(strict_types=1);

use App\Domain\Identity\Models\User;

return [

    /*
    |--------------------------------------------------------------------------
    | Authentication Defaults
    |--------------------------------------------------------------------------
    |
    | This option defines the default authentication "guard" and password
    | reset "broker" for your application. You may change these values
    | as required, but they're a perfect start for most applications.
    |
    */

    'defaults' => [
        'guard' => env('AUTH_GUARD', 'web'),
        'passwords' => env('AUTH_PASSWORD_BROKER', 'users'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Authentication Guards
    |--------------------------------------------------------------------------
    |
    | Next, you may define every authentication guard for your application.
    | Of course, a great default configuration has been defined for you
    | which utilizes session storage plus the Eloquent user provider.
    |
    | All authentication guards have a user provider, which defines how the
    | users are actually retrieved out of your database or other storage
    | system used by the application. Typically, Eloquent is utilized.
    |
    | Supported: "session"
    |
    */

    'guards' => [
        'web' => [
            'driver' => 'session',
            'provider' => 'users',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | User Providers
    |--------------------------------------------------------------------------
    |
    | All authentication guards have a user provider, which defines how the
    | users are actually retrieved out of your database or other storage
    | system used by the application. Typically, Eloquent is utilized.
    |
    | If you have multiple user tables or models you may configure multiple
    | providers to represent the model / table. These providers may then
    | be assigned to any extra authentication guards you have defined.
    |
    | Supported: "database", "eloquent"
    |
    */

    'providers' => [
        'users' => [
            'driver' => 'eloquent',
            'model' => env('AUTH_MODEL', User::class),
        ],

        // 'users' => [
        //     'driver' => 'database',
        //     'table' => 'users',
        // ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Sign up
    |--------------------------------------------------------------------------
    */

    'signup' => [
        'invite_duration_in_days' => (int) env('SIGNUP_INVITE_DURATION_IN_DAYS', 7),
    ],

    /*
    |--------------------------------------------------------------------------
    | Two Factor Challenge
    |--------------------------------------------------------------------------
    */

    '2fa' => [
        'namespace' => '2fa',

        'throttle' => [
            'request' => [
                'email' => [
                    'attempts' => 3,
                    'decay_seconds' => 900,
                ],
                'ip' => [
                    'attempts' => 20,
                    'decay_seconds' => 900,
                ],
            ],
            'consume' => [
                'email' => [
                    'attempts' => 5,
                    'decay_seconds' => 300,
                ],
                'ip' => [
                    'attempts' => 30,
                    'decay_seconds' => 300,
                ],
            ],
            'resend' => [
                'email' => [
                    'attempts' => 1,
                    'decay_seconds' => 60,
                ],
                'ip' => [
                    'attempts' => 10,
                    'decay_seconds' => 600,
                ],
            ],
        ],
    ],
];
