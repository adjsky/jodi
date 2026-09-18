<?php

declare(strict_types=1);

namespace App\Domain\Identity\Models;

use App\Support\Concerns\HasSqid;
use Carbon\CarbonImmutable;
use Database\Factories\RegistrationInvitationFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\UseFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $inviter_user_id
 * @property string $email
 * @property string $code
 * @property CarbonImmutable $expires_at
 * @property CarbonImmutable|null $created_at
 * @property CarbonImmutable|null $updated_at
 * @property-read User $inviter
 * @property-read mixed $sqid
 *
 * @method static \Database\Factories\RegistrationInvitationFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RegistrationInvitation newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RegistrationInvitation newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RegistrationInvitation query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RegistrationInvitation whereCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RegistrationInvitation whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RegistrationInvitation whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RegistrationInvitation whereExpiresAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RegistrationInvitation whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RegistrationInvitation whereInviterUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RegistrationInvitation whereUpdatedAt($value)
 *
 * @mixin \Eloquent
 */
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
