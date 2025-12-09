<?php

declare(strict_types=1);

namespace App\Data;

use App\Models\RegistrationInvitation;
use Carbon\Carbon;
use Spatie\LaravelData\Data;
use Spatie\TypeScriptTransformer\Attributes\TypeScript;

#[TypeScript]
class RegistrationInvitationDto extends Data
{
    public function __construct(
        public int $id,
        public string $email,
        public ?Carbon $registeredAt
    ) {}

    public static function fromModel(RegistrationInvitation $invitation): self
    {
        return new self(
            $invitation->id,
            $invitation->email,
            $invitation->registered_at
        );
    }
}
