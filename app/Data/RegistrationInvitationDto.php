<?php

declare(strict_types=1);

namespace App\Data;

use App\Models\RegistrationInvitation;
use Carbon\Carbon;
use Illuminate\Support\Facades\URL;
use Spatie\LaravelData\Data;
use Spatie\TypeScriptTransformer\Attributes\TypeScript;

#[TypeScript]
class RegistrationInvitationDto extends Data
{
    public function __construct(
        public string $id,
        public string $email,
        public ?Carbon $registeredAt,
        public string $shareUrl
    ) {}

    public static function fromModel(RegistrationInvitation $invitation): self
    {
        return new self(
            $invitation->sqid,
            $invitation->email,
            $invitation->registered_at,
            URL::temporarySignedRoute(
                'signup',
                $invitation->expires_at,
                ['code' => $invitation->code]
            )
        );
    }
}
