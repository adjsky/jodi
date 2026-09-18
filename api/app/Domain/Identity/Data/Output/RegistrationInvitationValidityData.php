<?php

declare(strict_types=1);

namespace App\Domain\Identity\Data\Output;

use App\Support\Data\JodiData;
use Spatie\TypeScriptTransformer\Attributes\TypeScript;

#[TypeScript]
class RegistrationInvitationValidityData extends JodiData
{
    public function __construct(public bool $valid) {}
}
