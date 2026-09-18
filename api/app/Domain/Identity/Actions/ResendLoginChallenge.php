<?php

declare(strict_types=1);

namespace App\Domain\Identity\Actions;

use App\Domain\Identity\Notifications\OneTimeLoginCode;
use App\Domain\Identity\Services\ThrottleService;
use App\Domain\Identity\Services\UserChallengeService;
use App\Support\Actions\JodiAction;
use App\Support\Http\JodiRequest;
use Illuminate\Http\Response;

class ResendLoginChallenge extends JodiAction
{
    public function __construct(
        private UserChallengeService $challengeService,
        private ThrottleService $throttleService,
    ) {}

    public function handle(string $id, string $ip): void
    {
        $this->throttleService->throttleBy(
            action: 'login:resend',
            scope: 'ip',
            identifier: $ip,
            attempts: config('auth.throttle.limits.resend.ip.attempts'),
            decaySeconds: config('auth.throttle.limits.resend.ip.decay_seconds'),
        );

        $this->throttleService->throttleBy(
            action: 'login:resend',
            scope: 'challenge',
            identifier: $id,
            attempts: config('auth.throttle.limits.resend.subject.attempts'),
            decaySeconds: config('auth.throttle.limits.resend.subject.decay_seconds'),
        );

        [$challenge, $code] = $this->challengeService->regenerate($id);

        $challenge?->user->notify(new OneTimeLoginCode($code));
    }

    public function asController(
        JodiRequest $request,
        string $id
    ): Response {
        $this->handle($id, $request->clientIp());

        return response()->noContent();
    }
}
