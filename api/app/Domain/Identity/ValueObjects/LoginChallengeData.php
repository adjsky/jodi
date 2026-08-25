<?php

declare(strict_types=1);

namespace App\Domain\Identity\ValueObjects;

use App\Domain\Identity\Enums\AuthenticationCredential;
use App\Support\Data\JodiData;
use Spatie\LaravelData\Optional;

class LoginChallengeData extends JodiData
{
    public AuthenticationCredential $credential;

    public string|Optional $deviceName;
}
