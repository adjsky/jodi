<?php

declare(strict_types=1);

namespace App\Domain\Identity\Services;

use App\Domain\Identity\Enums\OtpPurpose;
use App\Domain\Identity\Exceptions\InvalidOtpException;
use App\Domain\Identity\Exceptions\NoUserException;
use App\Domain\Identity\Exceptions\OtpExpiredException;
use App\Domain\Identity\Models\User;
use App\Domain\Identity\Models\UserOneTimePasswords;
use App\Domain\Identity\Support\OtpGenerator;
use Carbon\CarbonInterval;
use Illuminate\Support\Timebox;

class OtpService
{
    public function __construct(private OtpGenerator $otpGenerator) {}

    public function consume(OtpPurpose $purpose, string $email, string $password): User
    {
        $callback = function () use ($purpose, $email, $password) {
            $user = User::where('email', $email)->first();

            if (! $user) {
                throw new NoUserException;
            }

            $otp = $user->oneTimePasswords()->where([
                'purpose' => $purpose->value,
                'password' => $password,
            ])->first();

            if (! $otp) {
                throw new InvalidOtpException;
            }

            if ($otp->expires_at->isPast()) {
                throw new OtpExpiredException;
            }

            $otp->delete();

            return $user;
        };

        return (new Timebox)->call(
            $callback,
            microseconds: CarbonInterval::milliseconds(100)->microseconds
        );
    }

    public function generate(OtpPurpose $purpose, User $user): string
    {
        $password = $this->otpGenerator->numeric(UserOneTimePasswords::SIZE);

        $user->oneTimePasswords()->create([
            'purpose' => $purpose->value,
            'password' => $password,
            'expires_at' => now()->addMinutes(
                UserOneTimePasswords::EXPIRES_IN_X_MINUTES
            ),
        ]);

        return $password;
    }
}
