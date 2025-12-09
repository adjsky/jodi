<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\RegistrationInvitation;
use App\Models\User;

class RegistrationInvitationPolicy
{
    public function destroy(User $user, RegistrationInvitation $invitation): bool
    {
        return $user->id == $invitation->inviter_user_id;
    }
}
