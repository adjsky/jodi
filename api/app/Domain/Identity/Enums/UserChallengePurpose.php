<?php

declare(strict_types=1);

namespace App\Domain\Identity\Enums;

enum UserChallengePurpose: string
{
    case Login = 'login';
};
