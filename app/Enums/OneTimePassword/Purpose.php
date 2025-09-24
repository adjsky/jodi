<?php

declare(strict_types=1);

namespace App\Enums\OneTimePassword;

enum Purpose: string
{
    case Login = 'login';
};
