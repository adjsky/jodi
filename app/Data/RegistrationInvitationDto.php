<?php

declare(strict_types=1);

namespace App\Data;

use App\Models\RegistrationInvitation;
use Spatie\LaravelData\Data;
use Spatie\TypeScriptTransformer\Attributes\TypeScript;

#[TypeScript]
class RegistrationInvitationDto extends Data
{
    public function __construct(
        public string $email,
    ) {}

    public static function fromModel(RegistrationInvitation $invitation): self
    {
        return new self(
            $invitation->email
        );
    }
}
