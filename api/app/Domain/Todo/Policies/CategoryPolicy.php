<?php

declare(strict_types=1);

namespace App\Domain\Todo\Policies;

use App\Domain\Identity\Models\User;
use App\Domain\Todo\Models\Category;

class CategoryPolicy
{
    public function destroy(User $user, Category $category): bool
    {
        return $user->id == $category->user_id;
    }
}
