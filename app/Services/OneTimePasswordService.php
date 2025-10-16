<?php

declare(strict_types=1);

namespace App\Services;

use App\Enums\OneTimePassword\Purpose;
use App\Exceptions\Service\OneTimePassword\InvalidPasswordException;
use App\Exceptions\Service\OneTimePassword\NoUserException;
use App\Exceptions\Service\OneTimePassword\PasswordExpiredException;
use App\Models\User;
use App\Models\UserOneTimePasswords;
use Carbon\CarbonInterval;
use Illuminate\Support\Timebox;
use Nette\Utils\Random;

class OneTimePasswordService
{
    public function consume(Purpose $purpose, string $email, string $password): User
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
                throw new InvalidPasswordException;
            }

            if ($otp->expires_at->isPast()) {
                throw new PasswordExpiredException;
            }

            $otp->delete();

            return $user;
        };

        return (new Timebox)->call(
            $callback,
            microseconds: CarbonInterval::milliseconds(100)->microseconds
        );
    }

    public function generate(Purpose $purpose, User $user): string
    {
        $password = Random::generate(
            UserOneTimePasswords::SIZE,
            UserOneTimePasswords::CHARSET
        );

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
