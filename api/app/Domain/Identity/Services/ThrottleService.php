<?php

declare(strict_types=1);

namespace App\Domain\Identity\Services;

use App\Support\Cache\CacheKey;
use Illuminate\Http\Exceptions\ThrottleRequestsException;
use Illuminate\Support\Facades\RateLimiter;

class ThrottleService
{
    public function throttleBy(
        string $action,
        string $scope,
        int $attempts,
        int $decaySeconds,
        string $identifier
    ): void {
        $key = CacheKey::make(
            config('auth.throttle.namespace'),
            $action,
            $scope,
            hash('sha256', $identifier)
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

        throw new ThrottleRequestsException(
            __('Too many attempts.'),
            headers: ['retry-after' => RateLimiter::availableIn($key)]
        );
    }
}
