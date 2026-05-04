<?php

declare(strict_types=1);

namespace App\Domain\Identity\Data\Input;

use Spatie\LaravelData\Attributes\Validation\Email;
use Spatie\LaravelData\Data;

class AuthenticateUserData extends Data
{
    #[Email]
    public string $email;
}
