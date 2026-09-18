<?php

declare(strict_types=1);

namespace App\Domain\Identity\Data\Input;

use App\Domain\Identity\Enums\AuthenticationCredential;
use App\Support\Data\JodiData;
use Spatie\LaravelData\Attributes\Validation\Max;
use Spatie\LaravelData\Attributes\Validation\Min;
use Spatie\LaravelData\Attributes\Validation\RequiredIf;
use Spatie\LaravelData\Optional;

class CreateInvitationRegistrationChallengeData extends JodiData
{
    #[Min(1), Max((36))]
    public string $name;

    public AuthenticationCredential $credential;

    #[RequiredIf('credential', 'token')]
    #[Min(1), Max(255)]
    public string|Optional $deviceName;
}
