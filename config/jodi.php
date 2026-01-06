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
    | Proxy configuration
    |--------------------------------------------------------------------------
    |
    */

    'trustedProxies' => env('TRUSTED_PROXIES'),
];
