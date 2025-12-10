<?php

declare(strict_types=1);

namespace App\Support\Sqids;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;

trait HasSqid
{
    /**
     * Get the obfuscated version of the model Id.
     *
     * @see https://sqids.org
     *
     * @return Attribute<string, never>
     */
    protected function sqid(): Attribute
    {
        return Attribute::make(
            get: fn () => Sqid::encode($this->id)
        );
    }

    /**
     * Get the route key for the model.
     */
    public function getRouteKeyName(): string
    {
        return 'sqid';
    }

    /**
     * Retrieve the model for a bound value.
     *
     * @param  mixed  $value
     * @param  ?string  $field
     */
    public function resolveRouteBinding($value, $field = null): ?Model
    {
        return $this->resolveRouteBindingQuery($this, Sqid::decode($value), 'id')->first();
    }
}
