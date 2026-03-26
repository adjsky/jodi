<?php

declare(strict_types=1);

use Composer\InstalledVersions;

return [
    /*
    |--------------------------------------------------------------------------
    | Application info
    |--------------------------------------------------------------------------
    |
    */

    'version' => InstalledVersions::getPrettyVersion('adjsky/jodi'),

    /*
    |--------------------------------------------------------------------------
    | Default values for user preferences
    |--------------------------------------------------------------------------
    |
    */

    'preferences' => [
        'weekStartOn' => env('PREFERENCES_WEEK_START_ON', 'monday'),
        'notifications' => env('PREFERENCES_NOTIFICATIONS', 'push'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Config for reminders
    |--------------------------------------------------------------------------
    |
    */

    'reminders' => [
        'window' => [
            'days' => (int) env('MAX_REMINDER_WINDOW_DAYS', 31),
            'hours' => (int) env('MAX_REMINDER_WINDOW_HOURS', 120),
            'minutes' => (int) env('MAX_REMINDER_WINDOW_MINUTES', 600),
        ],
    ],
];
