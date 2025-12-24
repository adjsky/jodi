<?php

declare(strict_types=1);

return [
    /*
    |--------------------------------------------------------------------------
    | Application info
    |--------------------------------------------------------------------------
    |
    */

    'version' => '0.0.1',

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
];
