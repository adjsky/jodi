<?php

declare(strict_types=1);

namespace App\Domain\Identity\Services;

use App\Domain\Identity\Exceptions\InvalidOtpException;
use App\Domain\Identity\Exceptions\OtpExpiredException;
use App\Domain\Identity\Models\RegistrationChallenge;
use App\Domain\Identity\Models\RegistrationInvitation;
use App\Domain\Identity\Support\OtpGenerator;
use App\Support\Data\JodiData;
use Carbon\CarbonInterval;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Timebox;

class RegistrationChallengeService
{
    public function __construct(private OtpGenerator $otpGenerator) {}

    /** @return array{RegistrationChallenge, string} */
    public function generate(
        string $email,
        JodiData $data,
        ?RegistrationInvitation $invitation = null
    ): array {
        $code = $this->otpGenerator->numeric(RegistrationChallenge::CODE_SIZE);
        $hash = Hash::make($code);

        $challenge = DB::transaction(function () use ($email, $data, $invitation, $hash) {
            RegistrationChallenge::query()
                ->where('email', '=', $email)
                ->delete();

            $challenge = new RegistrationChallenge;

            $challenge->fill([
                'email' => $email,
                'code_hash' => $hash,
                'data' => $data->toArray(),
                'expires_at' => now()->addMinutes(RegistrationChallenge::EXPIRES_IN_MINUTES),
            ]);

            if ($invitation != null) {
                $challenge->registrationInvitation()->associate($invitation);
            }

            $challenge->save();

            return $challenge;
        });

        return [$challenge, $code];
    }

    /** @return array{?RegistrationChallenge, string} */
    public function regenerate(string $id): array
    {
        $code = $this->otpGenerator->numeric(RegistrationChallenge::CODE_SIZE);
        $hash = Hash::make($code);

        $challenge = DB::transaction(function () use ($id, $hash) {
            $challenge = RegistrationChallenge::query()
                ->where('id', '=', $id)
                ->lockForUpdate()
                ->first();

            $challenge?->update([
                'code_hash' => $hash,
                'expires_at' => now()->addMinutes(RegistrationChallenge::EXPIRES_IN_MINUTES),
            ]);

            return $challenge;
        });

        return [$challenge, $code];
    }

    /**
     * @template TResult
     *
     * @param  \Closure(RegistrationChallenge): TResult  $complete
     * @return TResult
     */
    public function consume(string $id, string $code, \Closure $complete)
    {
        $callback = fn () => DB::transaction(
            function () use ($complete, $id, $code) {
                $challenge = RegistrationChallenge::query()
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

                return $complete($challenge);
            },
        );

        return (new Timebox)->call(
            $callback,
            microseconds: CarbonInterval::milliseconds(100)->microseconds,
        );
    }
}
