<?php

declare(strict_types=1);

namespace App\Support\Builders;

use App\Domain\Identity\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * @template TModel of Model
 *
 * @extends Builder<TModel>
 */
class JodiBuilder extends Builder
{
    public function forUser(User $user): static
    {
        return $this->where('user_id', $user->id);
    }
}
