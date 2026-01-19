<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Category;
use App\Models\User;

class CategoryPolicy
{
    public function destroy(User $user, Category $category): bool
    {
        return $user->id == $category->user_id;
    }
}
