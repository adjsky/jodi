<?php

declare(strict_types=1);

namespace App\Services;

use App\Enums\OneTimePassword\ConsumeError;
use App\Enums\OneTimePassword\Purpose;
use App\Models\User;
use App\Models\UserOneTimePasswords;
use Nette\Utils\Random;

class OneTimePasswordService
{
    // TODO: timebox
    /** @return array{User,?ConsumeError} */
    public function consume(
        Purpose $purpose,
        string $email,
        string $password
    ): array {
        $user = User::where('email', $email)->first();

        if (! $user) {
            return [new User, ConsumeError::NoUser];
        }

        $otp = $user
            ->oneTimePasswords()
            ->where([
                'purpose' => $purpose->value,
                'password' => $password,
            ])
            ->first();

        if (! $otp) {
            return [new User, ConsumeError::InvalidPassword];
        }

        if ($otp->expires_at->isPast()) {
            return [new User, ConsumeError::PasswordExpired];
        }

        $otp->delete();

        return [$user, null];
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
