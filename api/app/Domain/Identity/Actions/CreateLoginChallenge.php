<?php

declare(strict_types=1);

namespace App\Domain\Identity\Actions;

use App\Domain\Identity\Data\Input\CreateLoginChallengeData;
use App\Domain\Identity\Enums\UserChallengePurpose;
use App\Domain\Identity\Models\UserChallenge;
use App\Domain\Identity\Notifications\OneTimeLoginCode;
use App\Domain\Identity\Services\ThrottleService;
use App\Domain\Identity\Services\UserChallengeService;
use App\Domain\Identity\ValueObjects\LoginChallengeData;
use App\Support\Actions\JodiAction;
use App\Support\Http\JodiRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Str;

class CreateLoginChallenge extends JodiAction
{
    public function __construct(
        private UserChallengeService $challengeService,
        private ThrottleService $throttleService,
    ) {}

    public function handle(CreateLoginChallengeData $data, string $ip): ?UserChallenge
    {
        $this->throttleService->throttleBy(
            action: 'login:create',
            scope: 'ip',
            identifier: $ip,
            attempts: config('auth.throttle.limits.create.ip.attempts'),
            decaySeconds: config('auth.throttle.limits.create.ip.decay_seconds'),
        );

        $this->throttleService->throttleBy(
            action: 'login:create',
            scope: 'email',
            identifier: $data->email,
            attempts: config('auth.throttle.limits.create.subject.attempts'),
            decaySeconds: config('auth.throttle.limits.create.subject.decay_seconds'),
        );

        [$challenge, $code] = $this->challengeService->generate(
            UserChallengePurpose::Login,
            $data->email,
            LoginChallengeData::from([
                'credential' => $data->credential,
                'deviceName' => $data->deviceName,
            ]),
        );

        $challenge?->user->notify(new OneTimeLoginCode($code));

        return $challenge;
    }

    public function asController(JodiRequest $request): JsonResponse
    {
        $challenge = $this->handle(
            CreateLoginChallengeData::from($request),
            $request->clientIp(),
        );

        $id = $challenge->id ?? strtolower((string) Str::ulid());

        return response()->json(['id' => $id], 202);
    }
}
