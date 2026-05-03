<?php

declare(strict_types=1);

namespace App\Domain\Event\Models;

use App\Domain\Identity\Models\User;
use Database\Factories\EventAttendeeFactory;
use Illuminate\Database\Eloquent\Attributes\UseFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $event_id
 * @property int $user_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read User|null $user
 *
 * @method static \Database\Factories\EventAttendeeFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EventAttendee newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EventAttendee newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EventAttendee query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EventAttendee whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EventAttendee whereEventId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EventAttendee whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EventAttendee whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EventAttendee whereUserId($value)
 *
 * @mixin \Eloquent
 */
#[UseFactory(EventAttendeeFactory::class)]
class EventAttendee extends Model
{
    use HasFactory;

    protected $fillable = [];

    protected $hidden = [];

    protected function casts(): array
    {
        return [];
    }

    /** @return HasOne<User,$this> */
    public function user(): HasOne
    {
        return $this->hasOne(User::class);
    }
}
