<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Event;
use App\Models\User;

class EventPolicy
{
    public function update(User $user, Event $event): bool
    {
        return $user->id == $event->user_id;
    }

    public function destroy(User $user, Event $event): bool
    {
        return $user->id == $event->user_id;
    }
}
