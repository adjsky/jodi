<?php

declare(strict_types=1);

namespace App\Domain\Identity\Actions;

use App\Domain\Identity\Data\Input\CompleteLoginChallengeData;
use App\Domain\Identity\Data\Output\AuthSessionData;
use App\Domain\Identity\Enums\AuthenticationCredential;
use App\Domain\Identity\Exceptions\InvalidOtpException;
use App\Domain\Identity\Exceptions\OtpExpiredException;
use App\Domain\Identity\Models\UserChallenge;
use App\Domain\Identity\Services\ThrottleService;
use App\Domain\Identity\Services\UserChallengeService;
use App\Domain\Identity\Support\AuthenticationCredentialIssuer;
use App\Domain\Identity\ValueObjects\LoginChallengeData;
use App\Support\Actions\JodiAction;
use App\Support\Http\JodiRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;

class CompleteLoginChallenge extends JodiAction
{
    public function __construct(
        private UserChallengeService $challengeService,
        private ThrottleService $throttleService,
        private AuthenticationCredentialIssuer $issuer,
    ) {}

    public function handle(
        CompleteLoginChallengeData $data,
        string $id,
        string $ip,
    ): UserChallenge {
        $this->throttleService->throttleBy(
            action: 'login:complete',
            scope: 'ip',
            identifier: $ip,
            attempts: config('auth.throttle.limits.complete.ip.attempts'),
            decaySeconds: config('auth.throttle.limits.complete.ip.decay_seconds'),
        );

        $this->throttleService->throttleBy(
            action: 'login:complete',
            scope: 'challenge',
            identifier: $id,
            attempts: config('auth.throttle.limits.complete.subject.attempts'),
            decaySeconds: config('auth.throttle.limits.complete.subject.decay_seconds'),
        );

        return $this->challengeService->consume($id, $data->code);
    }

    public function asController(
        JodiRequest $request,
        string $id,
    ): JsonResponse {
        try {
            $challenge = $this->handle(
                CompleteLoginChallengeData::from($request),
                $id,
                $request->clientIp(),
            );
            $challengeData = LoginChallengeData::from($challenge->data);

            $token = match ($challengeData->credential) {
                AuthenticationCredential::Token => $this->issuer
                    ->issueBearerToken($challenge->user, $challengeData->deviceName),
                AuthenticationCredential::Cookie => $this->issuer
                    ->startCookieSession($request, $challenge->user)
            };

            return response()->json(AuthSessionData::from(
                $challenge->user,
                $request->deviceId(),
                $token
            ));
        } catch (InvalidOtpException) {
            throw ValidationException::withMessages([
                'code' => __('The code is wrong.'),
            ]);
        } catch (OtpExpiredException) {
            throw ValidationException::withMessages([
                'code' => __('The code is expired.'),
            ]);
        }
    }
}
