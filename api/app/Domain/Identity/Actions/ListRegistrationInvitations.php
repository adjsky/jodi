<?php

declare(strict_types=1);

namespace App\Domain\Identity\Actions;

use App\Domain\Identity\Data\Output\RegistrationInvitationData;
use App\Domain\Identity\Models\User;
use App\Support\Actions\JodiAction;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Collection;

class ListRegistrationInvitations extends JodiAction
{
    public function handle(User $user): Collection
    {
        return RegistrationInvitationData::collect($user->invitations()->get());
    }

    public function asController(): JsonResponse
    {
        $invitations = $this->handle($this->user());

        return response()->json($invitations);
    }
}
