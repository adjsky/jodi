<?php

declare(strict_types=1);

namespace App\Domain\Identity\Models;

use App\Support\Concerns\HasSqid;
use Database\Factories\RegistrationInvitationFactory;
use Illuminate\Database\Eloquent\Attributes\UseFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[UseFactory(RegistrationInvitationFactory::class)]
class RegistrationInvitation extends Model
{
    use HasFactory, HasSqid;

    protected $fillable = [
        'email',
        'code',
        'expires_at',
    ];

    protected $hidden = [];

    protected function casts(): array
    {
        return [
            'registered_at' => 'datetime',
            'expires_at' => 'datetime',
        ];
    }
}
