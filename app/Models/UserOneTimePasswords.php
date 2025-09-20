<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserOneTimePasswords extends Model
{
    public static $EXPIRES_IN_X_MINUTES = 2;

    public static $SIZE = 6;

    public static $CHARSET = '0-9';

    protected $fillable = ['purpose', 'password', 'expires_at'];

    protected $hidden = [];

    protected function casts(): array
    {
        return [
            'expires_at' => 'datetime',
        ];
    }
}
