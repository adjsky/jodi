<?php

declare(strict_types=1);

namespace App\Domain\Todo\Policies;

use App\Domain\Identity\Models\User;
use App\Domain\Todo\Models\Todo;

class TodoPolicy
{
    public function update(User $user, Todo $todo): bool
    {
        return $user->id == $todo->user_id;
    }

    public function destroy(User $user, Todo $todo): bool
    {
        return $user->id == $todo->user_id;
    }

    public function complete(User $user, Todo $todo): bool
    {
        return $user->id == $todo->user_id;
    }

    public function reorder(User $user, array $todos): bool
    {
        $ids = collect($todos)->pluck('id')->toArray();

        $count = Todo::query()
            ->whereIn('id', $ids)
            ->where('user_id', $user->id)
            ->count();

        return $count == count($todos);
    }
}
