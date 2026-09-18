<?php

declare(strict_types=1);

namespace App\Domain\Identity\Data\Output;

use App\Domain\Identity\Models\RegistrationInvitation;
use App\Support\Data\JodiData;
use Carbon\CarbonInterface;
use Spatie\TypeScriptTransformer\Attributes\TypeScript;

#[TypeScript]
class RegistrationInvitationData extends JodiData
{
    public function __construct(
        public string $id,
        public string $email,
        public ?CarbonInterface $expiresAt,
        public string $shareUrl
    ) {}

    public static function fromModel(RegistrationInvitation $invitation): self
    {
        return new self(
            $invitation->sqid,
            $invitation->email,
            $invitation->expires_at,
            sprintf('%s/invite/%s', config('app.url'), $invitation->code)
        );
    }
}
