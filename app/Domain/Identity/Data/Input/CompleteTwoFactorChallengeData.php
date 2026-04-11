<?php

declare(strict_types=1);

namespace App\Domain\Identity\Data\Input;

use Spatie\LaravelData\Data;

class CompleteTwoFactorChallengeData extends Data
{
    public string $password;
}
