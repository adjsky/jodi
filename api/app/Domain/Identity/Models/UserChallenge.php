<?php

declare(strict_types=1);

namespace App\Domain\Identity\Models;

use App\Domain\Identity\Enums\UserChallengePurpose;
use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property string $id
 * @property int $user_id
 * @property UserChallengePurpose $purpose
 * @property string $code_hash
 * @property array<array-key, mixed>|null $data
 * @property CarbonImmutable $expires_at
 * @property CarbonImmutable|null $created_at
 * @property CarbonImmutable|null $updated_at
 * @property-read User $user
 *
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserChallenge newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserChallenge newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserChallenge query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserChallenge whereCodeHash($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserChallenge whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserChallenge whereData($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserChallenge whereExpiresAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserChallenge whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserChallenge wherePurpose($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserChallenge whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserChallenge whereUserId($value)
 *
 * @mixin \Eloquent
 */
#[Fillable([
    'purpose',
    'code_hash',
    'data',
    'expires_at',
])]
#[Hidden([
    'code_hash',
    'data',
])]
class UserChallenge extends Model
{
    use HasUlids;

    public const int EXPIRES_IN_MINUTES = 15;

    public const int CODE_SIZE = 6;

    protected function casts(): array
    {
        return [
            'purpose' => UserChallengePurpose::class,
            'data' => 'encrypted:array',
            'expires_at' => 'datetime',
        ];
    }

    /** @return BelongsTo<User, $this> */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
