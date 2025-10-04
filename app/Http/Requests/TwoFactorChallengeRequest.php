<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\ThrottleRequestsException;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\RateLimiter;

class TwoFactorChallengeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return ['password' => 'required|string'];
    }

    public function throttle(string $email): void
    {
        $key = '2fa'.'|'.str($email)->lower()->transliterate();

        $hits = RateLimiter::hit($key, config('auth.2fa.throttle.decay_seconds'));

        if ($hits <= config('auth.2fa.throttle.attempts')) {
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
