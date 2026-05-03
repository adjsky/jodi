<?php

declare(strict_types=1);

namespace App\Domain\Identity\Casts;

use App\Domain\Identity\ValueObjects\UserPreferences;
use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Database\Eloquent\Model;

class UserPreferencesCast implements CastsAttributes
{
    public function get(Model $model, string $key, mixed $value, array $attributes): UserPreferences
    {
        return UserPreferences::from($value);
    }

    public function set(Model $model, string $key, mixed $value, array $attributes): mixed
    {
        return json_encode($value);
    }
}
