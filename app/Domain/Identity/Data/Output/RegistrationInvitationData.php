<?php

declare(strict_types=1);

namespace App\Domain\Identity\Data\Output;

use App\Domain\Identity\Models\RegistrationInvitation;
use App\Support\Data\JodiData;
use Carbon\CarbonInterface;
use Illuminate\Support\Facades\URL;
use Spatie\TypeScriptTransformer\Attributes\TypeScript;

#[TypeScript]
class RegistrationInvitationData extends JodiData
{
    public function __construct(
        public string $id,
        public string $email,
        public ?CarbonInterface $registeredAt,
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
