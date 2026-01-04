<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Category extends Model
{
    /** @use HasFactory<\Database\Factories\CategoryFactory> */
    use HasFactory;

    protected $fillable = [
        'name',
    ];

    protected $hidden = [];

    /**
     * @return array{id: "non-negative-int"}
     */
    protected function casts(): array
    {
        return [
            // TODO: https://github.com/larastan/larastan/issues/2402
            'id' => 'non-negative-int',
        ];
    }

    /** @return HasOne<User,$this> */
    public function user(): HasOne
    {
        return $this->hasOne(User::class);
    }
}
