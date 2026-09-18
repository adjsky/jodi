<?php

declare(strict_types=1);

namespace App\Domain\Identity\Actions;

use App\Domain\Identity\Data\Input\CompleteInvitationRegistrationChallengeData;
use App\Domain\Identity\Data\Output\AuthSessionData;
use App\Domain\Identity\Enums\AuthenticationCredential;
use App\Domain\Identity\Exceptions\InvalidOtpException;
use App\Domain\Identity\Exceptions\NoInvitationAvailable;
use App\Domain\Identity\Exceptions\OtpExpiredException;
use App\Domain\Identity\Models\User;
use App\Domain\Identity\Services\RegistrationChallengeService;
use App\Domain\Identity\Services\ThrottleService;
use App\Domain\Identity\Support\AuthenticationCredentialIssuer;
use App\Domain\Identity\ValueObjects\InvitationRegistrationChallengeData;
use App\Domain\Identity\ValueObjects\UserPreferences;
use App\Support\Actions\JodiAction;
use App\Support\Http\JodiRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;

class CompleteInvitationRegistrationChallenge extends JodiAction
{
    public function __construct(
        private RegistrationChallengeService $challengeService,
        private ThrottleService $throttleService,
        private AuthenticationCredentialIssuer $issuer,
    ) {}

    /** @return array{User, InvitationRegistrationChallengeData} */
    public function handle(
        CompleteInvitationRegistrationChallengeData $data,
        string $id,
        string $ip
    ): array {
        $this->throttleService->throttleBy(
            action: 'invitation-registration:complete',
            scope: 'ip',
            identifier: $ip,
            attempts: config('auth.throttle.limits.complete.ip.attempts'),
            decaySeconds: config('auth.throttle.limits.complete.ip.decay_seconds'),
        );

        $this->throttleService->throttleBy(
            action: 'invitation-registration:complete',
            scope: 'challenge',
            identifier: $id,
            attempts: config('auth.throttle.limits.complete.subject.attempts'),
            decaySeconds: config('auth.throttle.limits.complete.subject.decay_seconds'),
        );

        return $this->challengeService->consume(
            $id,
            $data->code,
            function ($challenge) {
                $challengeData = InvitationRegistrationChallengeData::from($challenge->data);

                $invitation = $challenge->registrationInvitation()
                    ->with('inviter')
                    ->lockForUpdate()
                    ->first();

                if (! $invitation) {
                    throw new NoInvitationAvailable;
                }

                $user = User::create([
                    'email' => $challenge->email,
                    'name' => $challengeData->name,
                    'preferences' => UserPreferences::from([
                        ...config('jodi.preferences'),
                        'locale' => $challengeData->locale,
                        'timezone' => $challengeData->timezone,
                    ]),
                ]);

                $user->friends()->attach($invitation->inviter->id);
                $invitation->inviter->friends()->attach($user->id);

                $invitation->delete();

                return [$user, $challengeData];
            }
        );
    }

    public function asController(JodiRequest $request, string $id): JsonResponse
    {
        try {
            [$user, $challengeData] = $this->handle(
                CompleteInvitationRegistrationChallengeData::from($request),
                $id,
                $request->clientIp(),
            );

            $token = match ($challengeData->credential) {
                AuthenticationCredential::Token => $this->issuer
                    ->issueBearerToken($user, $challengeData->deviceName),
                AuthenticationCredential::Cookie => $this->issuer
                    ->startCookieSession($request, $user)
            };

            return response()->json(
                AuthSessionData::from(
                    $user,
                    $request->deviceId(),
                    $token
                ),
                201
            );
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
