<?php

declare(strict_types=1);

namespace App\Domain\Identity\Data\Input;

use App\Domain\Identity\Models\UserChallenge;
use App\Support\Data\JodiData;
use Spatie\LaravelData\Attributes\Validation\Digits;

class CompleteLoginChallengeData extends JodiData
{
    #[Digits(UserChallenge::CODE_SIZE)]
    public string $code;
}
