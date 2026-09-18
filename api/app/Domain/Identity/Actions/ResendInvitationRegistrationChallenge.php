<?php

declare(strict_types=1);

namespace App\Domain\Identity\Actions;

use App\Domain\Identity\Mail\InvitationRegistrationCode;
use App\Domain\Identity\Services\RegistrationChallengeService;
use App\Domain\Identity\Services\ThrottleService;
use App\Support\Actions\JodiAction;
use App\Support\Http\JodiRequest;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Mail;

class ResendInvitationRegistrationChallenge extends JodiAction
{
    public function __construct(
        private RegistrationChallengeService $challengeService,
        private ThrottleService $throttleService,
    ) {}

    public function handle(string $id, string $ip, string $locale): void
    {
        $this->throttleService->throttleBy(
            action: 'invitation-registration:resend',
            scope: 'ip',
            identifier: $ip,
            attempts: config('auth.throttle.limits.resend.ip.attempts'),
            decaySeconds: config('auth.throttle.limits.resend.ip.decay_seconds'),
        );

        $this->throttleService->throttleBy(
            action: 'invitation-registration:resend',
            scope: 'challenge',
            identifier: $id,
            attempts: config('auth.throttle.limits.resend.subject.attempts'),
            decaySeconds: config('auth.throttle.limits.resend.subject.decay_seconds'),
        );

        [$challenge, $code] = $this->challengeService->regenerate($id);

        if ($challenge) {
            Mail::to($challenge->email)
                ->locale($locale)
                ->send(new InvitationRegistrationCode($code));
        }
    }

    public function asController(
        JodiRequest $request,
        string $id
    ): Response {
        $this->handle($id, $request->clientIp(), app()->getLocale());

        return response()->noContent();
    }
}
