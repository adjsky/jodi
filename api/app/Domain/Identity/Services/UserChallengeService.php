<?php

declare(strict_types=1);

namespace App\Domain\Identity\Services;

use App\Domain\Identity\Enums\UserChallengePurpose;
use App\Domain\Identity\Exceptions\InvalidOtpException;
use App\Domain\Identity\Exceptions\OtpExpiredException;
use App\Domain\Identity\Models\User;
use App\Domain\Identity\Models\UserChallenge;
use App\Domain\Identity\Support\OtpGenerator;
use App\Support\Data\JodiData;
use Carbon\CarbonInterval;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Timebox;

class UserChallengeService
{
    public function __construct(private OtpGenerator $otpGenerator) {}

    /** @return array{?UserChallenge, string} */
    public function generate(
        UserChallengePurpose $purpose,
        string $email,
        ?JodiData $data = null,
    ): array {
        $code = $this->otpGenerator->numeric(UserChallenge::CODE_SIZE);
        $hash = Hash::make($code);

        $challenge = DB::transaction(function () use ($purpose, $email, $data, $hash) {
            $user = User::query()
                ->where('email', '=', $email)
                ->lockForUpdate()
                ->first();

            if (! $user) {
                return null;
            }

            $user->challenges()->where('purpose', '=', $purpose)->delete();

            $challenge = $user->challenges()->create([
                'purpose' => $purpose,
                'code_hash' => $hash,
                'data' => $data?->toArray(),
                'expires_at' => now()->addMinutes(UserChallenge::EXPIRES_IN_MINUTES),
            ]);

            $challenge->setRelation('user', $user);

            return $challenge;
        });

        return [$challenge, $code];
    }

    /** @return array{?UserChallenge, string} */
    public function regenerate(string $id): array
    {
        $code = $this->otpGenerator->numeric(UserChallenge::CODE_SIZE);
        $hash = Hash::make($code);

        $challenge = DB::transaction(function () use ($id, $hash) {
            $challenge = UserChallenge::query()
                ->with('user')
                ->where('id', '=', $id)
                ->lockForUpdate()
                ->first();

            $challenge?->update([
                'code_hash' => $hash,
                'expires_at' => now()->addMinutes(UserChallenge::EXPIRES_IN_MINUTES),
            ]);

            return $challenge;
        });

        return [$challenge, $code];
    }

    public function consume(string $id, string $code): UserChallenge
    {
        $callback = fn () => DB::transaction(
            function () use ($id, $code) {
                $challenge = UserChallenge::query()
                    ->with('user')
                    ->where('id', '=', $id)
                    ->lockForUpdate()
                    ->first();

                if (! $challenge || ! Hash::check($code, $challenge->code_hash)) {
                    throw new InvalidOtpException;
                }

                if ($challenge->expires_at->isPast()) {
                    throw new OtpExpiredException;
                }

                $challenge->delete();

                return $challenge;
            },
        );

        return (new Timebox)->call(
            $callback,
            microseconds: CarbonInterval::milliseconds(100)->microseconds,
        );
    }
}
