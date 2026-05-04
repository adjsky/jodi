<?php

declare(strict_types=1);

namespace App\Domain\Identity\Models;

use Database\Factories\PushSubscriptionFactory;
use Illuminate\Database\Eloquent\Attributes\UseFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $user_id
 * @property string $fcm_token
 * @property string $platform
 * @property string $device_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read User $user
 *
 * @method static \Database\Factories\PushSubscriptionFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PushSubscription newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PushSubscription newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PushSubscription query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PushSubscription whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PushSubscription whereDeviceId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PushSubscription whereFcmToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PushSubscription whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PushSubscription wherePlatform($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PushSubscription whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PushSubscription whereUserId($value)
 *
 * @mixin \Eloquent
 */
#[UseFactory(PushSubscriptionFactory::class)]
class PushSubscription extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'fcm_token',
        'platform',
        'device_id',
    ];

    protected $hidden = [];

    protected function casts(): array
    {
        return [];
    }

    /** @return BelongsTo<User,$this> */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
