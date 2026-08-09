<?php

declare(strict_types=1);

namespace App\Domain\Identity\Services;

use App\Support\Cache\CacheKey;
use Illuminate\Http\Exceptions\ThrottleRequestsException;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\RateLimiter;

class ThrottleService
{
    public function throttleByEmail(
        string $action,
        int $attempts,
        int $decaySeconds,
        string $email
    ): void {
        $key = CacheKey::make(
            config('auth.2fa.namespace'),
            $action,
            'email',
            hash('sha256', $email)
        );

        $this->throttle($key, $attempts, $decaySeconds);
    }

    public function throttleByIp(
        string $action,
        int $attempts,
        int $decaySeconds,
        string $ip
    ): void {
        $key = CacheKey::make(
            config('auth.2fa.namespace'),
            $action,
            'ip',
            hash('sha256', $ip)
        );

        $this->throttle($key, $attempts, $decaySeconds);
    }

    private function throttle(
        string $key,
        int $attempts,
        int $decaySeconds,
    ): void {
        $hits = RateLimiter::hit($key, $decaySeconds);

        if ($hits <= $attempts) {
            return;
        }

        $retryAfter = RateLimiter::availableIn($key);
        $resetAt = Carbon::now()->addSeconds($retryAfter)->getTimestamp();

        throw new ThrottleRequestsException(
            __('Too many attempts.'),
            headers: ['retry-after' => $retryAfter, 'x-ratelimit-reset' => $resetAt]
        );
    }
}
