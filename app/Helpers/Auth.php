<?php

declare(strict_types=1);

namespace App\Helpers;

use Illuminate\Http\Exceptions\ThrottleRequestsException;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\RateLimiter;

class Auth
{
    public static function throttle(
        string $action,
        int $attempts,
        int $decaySeconds,
        string $email
    ): void {
        $key = '2fa'.'|'.$action.'|'.str($email)->lower()->transliterate();

        $hits = RateLimiter::hit($key, $decaySeconds);

        if ($hits <= $attempts) {
            return;
        }

        $retryAfter = RateLimiter::availableIn($key);
        $resetAt = Carbon::now()->addSeconds($retryAfter)->getTimestamp();

        throw new ThrottleRequestsException(
            'Too many attempts',
            headers: ['retry-after' => $retryAfter, 'x-ratelimit-reset' => $resetAt]
        );
    }
}
