<?php

declare(strict_types=1);

namespace App\Domain\Event\Policies;

use App\Domain\Event\Models\Event;
use App\Domain\Identity\Models\User;

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
