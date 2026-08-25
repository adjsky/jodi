<?php

declare(strict_types=1);

namespace App\Domain\Identity\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'email',
    'code_hash',
    'data',
    'expires_at',
])]
#[Hidden([
    'code_hash',
    'data',
])]
class RegistrationChallenge extends Model
{
    use HasUlids;

    public const int EXPIRES_IN_MINUTES = 15;

    public const int CODE_SIZE = 6;

    protected function casts(): array
    {
        return [
            'data' => 'encrypted:array',
            'expires_at' => 'datetime',
        ];
    }

    /** @return BelongsTo<RegistrationInvitation, $this> */
    public function registrationInvitation(): BelongsTo
    {
        return $this->belongsTo(RegistrationInvitation::class);
    }
}
