<?php

declare(strict_types=1);

namespace App\Domain\Identity\Data\Input;

use App\Domain\Identity\Models\UserOneTimePasswords;
use App\Support\Data\JodiData;
use Spatie\LaravelData\Attributes\Validation\Digits;

class CompleteTwoFactorChallengeData extends JodiData
{
    #[Digits(UserOneTimePasswords::SIZE)]
    public string $password;
}
