<?php

declare(strict_types=1);

namespace App\Infrastructure\Firebase\Configuration;

use App\Infrastructure\Firebase\Exceptions\InvalidFirebaseConfiguration;

class FirebaseConfigurationProvider
{
    public static function web(): FirebaseWebConfiguration
    {
        $raw_config = config('services.firebase.web');

        if (! $raw_config) {
            throw new InvalidFirebaseConfiguration('Web configuration is missing.');
        }

        try {
            $config = json_decode(
                $raw_config,
                true,
                flags: JSON_THROW_ON_ERROR
            );
        } catch (\JsonException) {
            throw new InvalidFirebaseConfiguration('Web configuration must be a valid JSON.');
        }

        if (! is_array($config)) {
            throw new InvalidFirebaseConfiguration('Web configuration must be a JSON object.');
        }

        return new FirebaseWebConfiguration(
            self::requireNonEmptyString($config, 'apiKey'),
            self::requireNonEmptyString($config, 'authDomain'),
            self::requireNonEmptyString($config, 'projectId'),
            self::requireNonEmptyString($config, 'storageBucket'),
            self::requireNonEmptyString($config, 'messagingSenderId'),
            self::requireNonEmptyString($config, 'appId'),
        );
    }

    public static function vapidKey(): string
    {
        $vapidKey = config('services.firebase.vapid_key');

        if (! is_string($vapidKey) || trim($vapidKey) == '') {
            throw new InvalidFirebaseConfiguration('VAPID key must be a non-empty string.');
        }

        return $vapidKey;
    }

    private static function requireNonEmptyString(array $config, string $field): string
    {
        if (! isset($config[$field])) {
            throw new InvalidFirebaseConfiguration("\"{$field}\" field must be present.");
        }

        $value = $config[$field];

        if (trim($value) == '') {
            throw new InvalidFirebaseConfiguration("\"{$field}\" field must be a non-empty string.");
        }

        return $value;
    }
}
