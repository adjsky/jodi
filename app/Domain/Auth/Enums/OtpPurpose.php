<?php

declare(strict_types=1);

namespace App\Domain\Auth\Enums;

enum OtpPurpose: string
{
    case Login = 'login';
};
