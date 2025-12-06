<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RegistrationInvitation extends Model
{
    /** @use HasFactory<\Database\Factories\RegistrationInvitationFactory> */
    use HasFactory;

    protected $fillable = [];

    protected $hidden = [];

    /**
     * @return array{registered_at: "datetime", expires_at: "datetime"}
     */
    protected function casts(): array
    {
        return [
            'registered_at' => 'datetime',
            'expires_at' => 'datetime',
        ];
    }
}
