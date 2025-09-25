<?php

declare(strict_types=1);

namespace App\Enums\OneTimePassword;

enum ConsumeError
{
    case NoUser;
    case InvalidPassword;
    case PasswordExpired;
}
