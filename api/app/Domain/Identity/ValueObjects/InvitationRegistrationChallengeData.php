<?php

declare(strict_types=1);

namespace App\Domain\Identity\ValueObjects;

use App\Domain\Identity\Enums\AuthenticationCredential;
use App\Support\Data\JodiData;
use Spatie\LaravelData\Optional;

class InvitationRegistrationChallengeData extends JodiData
{
    public string $name;

    public string $locale;

    public string $timezone;

    public AuthenticationCredential $credential;

    public string|Optional $deviceName;
}
