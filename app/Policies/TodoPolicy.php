<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Todo;
use App\Models\User;

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

        $count = Todo::whereIn('id', $ids)
            ->where('user_id', $user->id)
            ->count();

        return $count == count($todos);
    }
}
