<?php

declare(strict_types=1);

namespace App\Domain\Identity\Actions;

use App\Domain\Identity\Data\Output\RegistrationInvitationData;
use App\Support\Actions\JodiAction;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Collection;

class ListRegistrationInvitations extends JodiAction
{
    public function handle(): Collection
    {
        return RegistrationInvitationData::collect($this->user()->invitations()->get());
    }

    public function asController(): JsonResponse
    {
        $invitations = $this->handle();

        return response()->json($invitations);
    }
}
