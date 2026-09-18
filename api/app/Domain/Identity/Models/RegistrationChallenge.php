<?php

declare(strict_types=1);

namespace App\Domain\Identity\Models;

use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property string $id
 * @property int|null $registration_invitation_id
 * @property string $email
 * @property string $code_hash
 * @property array<array-key, mixed> $data
 * @property CarbonImmutable $expires_at
 * @property CarbonImmutable|null $created_at
 * @property CarbonImmutable|null $updated_at
 * @property-read RegistrationInvitation|null $registrationInvitation
 *
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RegistrationChallenge newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RegistrationChallenge newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RegistrationChallenge query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RegistrationChallenge whereCodeHash($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RegistrationChallenge whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RegistrationChallenge whereData($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RegistrationChallenge whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RegistrationChallenge whereExpiresAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RegistrationChallenge whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RegistrationChallenge whereRegistrationInvitationId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RegistrationChallenge whereUpdatedAt($value)
 *
 * @mixin \Eloquent
 */
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
