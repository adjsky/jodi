<?php

declare(strict_types=1);

namespace App\Domain\Identity\Enums;

enum AuthenticationCredential: string
{
    case Cookie = 'cookie';
    case Token = 'token';
}
