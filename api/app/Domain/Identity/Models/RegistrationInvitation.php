<?php

declare(strict_types=1);

namespace App\Domain\Identity\Models;

use App\Support\Identifiers\Concerns\HasSqid;
use Database\Factories\RegistrationInvitationFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\UseFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'email',
    'code',
    'expires_at',
])]
#[UseFactory(RegistrationInvitationFactory::class)]
class RegistrationInvitation extends Model
{
    use HasFactory, HasSqid;

    const EXPIRES_IN_X_DAYS = 7;

    protected function casts(): array
    {
        return [
            'expires_at' => 'datetime',
        ];
    }

    /** @return BelongsTo<User, $this> */
    public function inviter(): BelongsTo
    {
        return $this->belongsTo(User::class, 'inviter_user_id');
    }
}
