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

    public function delete(User $user, Todo $todo): bool
    {
        return $user->id == $todo->user_id;
    }
}
