<?php

declare(strict_types=1);

namespace App\Domain\Identity\Actions;

use App\Domain\Identity\Data\Output\RegistrationInvitationData;
use App\Domain\Identity\Models\RegistrationInvitation;
use App\Support\Actions\JodiAction;
use Illuminate\Http\JsonResponse;

class GetRegistrationInvitation extends JodiAction
{
    public function asController(RegistrationInvitation $invitation): JsonResponse
    {
        return response()->json(RegistrationInvitationData::fromModel($invitation));
    }
}
