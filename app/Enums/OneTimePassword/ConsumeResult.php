<?php

declare(strict_types=1);

namespace App\Enums\OneTimePassword;

use App\Models\User;

enum ConsumeResult
{
    case NoUser;
    case InvalidPassword;
    case PasswordExpired;
    case Ok;

    public User $user;

    public static function Ok(User $user): self
    {
        $result = self::Ok;
        $result->user = $user;

        return $result;
    }
}
