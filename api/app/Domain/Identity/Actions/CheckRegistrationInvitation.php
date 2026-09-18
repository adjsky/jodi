<?php

declare(strict_types=1);

namespace App\Domain\Identity\Actions;

use App\Domain\Identity\Data\Output\RegistrationInvitationValidityData;
use App\Domain\Identity\Models\RegistrationInvitation;
use App\Domain\Identity\Services\ThrottleService;
use App\Support\Actions\JodiAction;
use App\Support\Http\JodiRequest;
use Illuminate\Http\JsonResponse;

class CheckRegistrationInvitation extends JodiAction
{
    public function __construct(private ThrottleService $throttleService) {}

    public function handle(string $code, string $ip): bool
    {
        $this->throttleService->throttleBy(
            action: 'invitation-registration:validity',
            scope: 'ip',
            identifier: $ip,
            attempts: config('auth.throttle.limits.validity.ip.attempts'),
            decaySeconds: config('auth.throttle.limits.validity.ip.decay_seconds'),
        );

        return RegistrationInvitation::query()
            ->where('code', '=', $code)
            ->where('expires_at', '>', now())
            ->exists();
    }

    public function asController(JodiRequest $request, string $code): JsonResponse
    {
        $valid = $this->handle($code, $request->clientIp());

        return response()->json(new RegistrationInvitationValidityData($valid));
    }
}
