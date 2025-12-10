<?php

declare(strict_types=1);

namespace App\Models;

use App\Support\Sqids\HasSqid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RegistrationInvitation extends Model
{
    /** @use HasFactory<\Database\Factories\RegistrationInvitationFactory> */
    use HasFactory, HasSqid;

    protected $fillable = [
        'email',
        'code',
        'expires_at',
    ];

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
