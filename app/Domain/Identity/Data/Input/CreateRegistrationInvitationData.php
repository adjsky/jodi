<?php

declare(strict_types=1);

namespace App\Domain\Identity\Data\Input;

use Spatie\LaravelData\Attributes\Validation\Email;
use Spatie\LaravelData\Attributes\Validation\Unique;
use Spatie\LaravelData\Data;

class CreateRegistrationInvitationData extends Data
{
    #[Email, Unique('registration_invitations')]
    public string $email;
}
