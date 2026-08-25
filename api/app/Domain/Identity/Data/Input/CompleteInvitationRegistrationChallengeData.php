<?php

declare(strict_types=1);

namespace App\Domain\Identity\Data\Input;

use App\Domain\Identity\Models\RegistrationChallenge;
use App\Support\Data\JodiData;
use Spatie\LaravelData\Attributes\Validation\Digits;

class CompleteInvitationRegistrationChallengeData extends JodiData
{
    #[Digits(RegistrationChallenge::CODE_SIZE)]
    public string $code;
}
