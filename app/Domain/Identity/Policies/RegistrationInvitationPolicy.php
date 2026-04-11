<?php

declare(strict_types=1);

namespace App\Domain\Identity\Policies;

use App\Domain\Identity\Models\RegistrationInvitation;
use App\Domain\Identity\Models\User;

class RegistrationInvitationPolicy
{
    public function destroy(User $user, RegistrationInvitation $invitation): bool
    {
        return $user->id == $invitation->inviter_user_id;
    }
}
