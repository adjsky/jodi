<?php

declare(strict_types=1);

namespace App\Domain\Identity\Models;

use Illuminate\Database\Eloquent\Model;

class UserOneTimePasswords extends Model
{
    const EXPIRES_IN_X_MINUTES = 15;

    const SIZE = 6;

    protected $fillable = ['purpose', 'password', 'expires_at'];

    protected $hidden = [];

    protected function casts(): array
    {
        return [
            'expires_at' => 'datetime',
        ];
    }
}
