<?php

declare(strict_types=1);

namespace App\Domain\Identity\Actions;

use App\Domain\Identity\Data\Input\CreateInvitationRegistrationChallengeData;
use App\Domain\Identity\Exceptions\NoInvitationAvailable;
use App\Domain\Identity\Mail\InvitationRegistrationCode;
use App\Domain\Identity\Models\RegistrationChallenge;
use App\Domain\Identity\Models\RegistrationInvitation;
use App\Domain\Identity\Services\RegistrationChallengeService;
use App\Domain\Identity\Services\ThrottleService;
use App\Domain\Identity\ValueObjects\InvitationRegistrationChallengeData;
use App\Support\Actions\JodiAction;
use App\Support\Http\JodiRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class CreateInvitationRegistrationChallenge extends JodiAction
{
    public function __construct(
        private RegistrationChallengeService $challengeService,
        private ThrottleService $throttleService,
    ) {}

    public function handle(
        CreateInvitationRegistrationChallengeData $data,
        string $invitationCode,
        string $ip,
        string $locale,
        ?string $timezone
    ): RegistrationChallenge {
        $this->throttleService->throttleBy(
            action: 'invitation-registration:create',
            scope: 'ip',
            identifier: $ip,
            attempts: config('auth.throttle.limits.create.ip.attempts'),
            decaySeconds: config('auth.throttle.limits.create.ip.decay_seconds'),
        );

        $this->throttleService->throttleBy(
            action: 'invitation-registration:create',
            scope: 'invitation',
            identifier: $invitationCode,
            attempts: config('auth.throttle.limits.create.subject.attempts'),
            decaySeconds: config('auth.throttle.limits.create.subject.decay_seconds'),
        );

        [$challenge, $otpCode] = DB::transaction(function () use (
            $data,
            $invitationCode,
            $locale,
            $timezone
        ) {
            $invitation = RegistrationInvitation::query()
                ->where('code', '=', $invitationCode)
                ->where('expires_at', '>', now())
                ->lockForUpdate()
                ->first();

            if (! $invitation) {
                throw new NoInvitationAvailable;
            }

            return $this->challengeService->generate(
                $invitation->email,
                InvitationRegistrationChallengeData::from([
                    'name' => $data->name,
                    'locale' => $locale,
                    'timezone' => $timezone ?? config('jodi.preferences.timezone'),
                    'credential' => $data->credential,
                    'deviceName' => $data->deviceName,
                ]),
                $invitation
            );
        });

        Mail::to($challenge->email)
            ->locale($locale)
            ->send(new InvitationRegistrationCode($otpCode));

        return $challenge;
    }

    public function asController(JodiRequest $request, string $code): JsonResponse
    {
        $challenge = $this->handle(
            CreateInvitationRegistrationChallengeData::from($request),
            $code,
            $request->clientIp(),
            app()->getLocale(),
            $request->timezone(),
        );

        return response()->json(['id' => $challenge->id], 202);
    }
}
