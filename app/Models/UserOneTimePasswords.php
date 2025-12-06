<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserOneTimePasswords extends Model
{
    const EXPIRES_IN_X_MINUTES = 2;

    const SIZE = 6;

    protected $fillable = ['purpose', 'password', 'expires_at'];

    protected $hidden = [];

    /**
     * @return array{expires_at: "datetime"}
     */
    protected function casts(): array
    {
        return [
            'expires_at' => 'datetime',
        ];
    }
}
