<?php

declare(strict_types=1);

namespace App\Domain\Identity\Models;

use App\Support\Concerns\HasSqid;
use Database\Factories\RegistrationInvitationFactory;
use Illuminate\Database\Eloquent\Attributes\UseFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $inviter_user_id
 * @property string $email
 * @property string $code
 * @property Carbon $expires_at
 * @property int|null $registered_user_id
 * @property Carbon|null $registered_at
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
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
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RegistrationInvitation whereRegisteredAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RegistrationInvitation whereRegisteredUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RegistrationInvitation whereUpdatedAt($value)
 *
 * @mixin \Eloquent
 */
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
