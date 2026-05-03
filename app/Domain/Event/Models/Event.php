<?php

declare(strict_types=1);

namespace App\Domain\Event\Models;

use App\Domain\Event\Builders\EventBuilder;
use App\Domain\Identity\Models\User;
use App\Domain\Recurrence\Concerns\HasRecurrence;
use App\Domain\Recurrence\Contracts\Recurrable;
use App\Domain\Recurrence\Models\RecurrenceException;
use Database\Factories\EventFactory;
use Illuminate\Database\Eloquent\Attributes\UseEloquentBuilder;
use Illuminate\Database\Eloquent\Attributes\UseFactory;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $user_id
 * @property string $title
 * @property string|null $description
 * @property string|null $location
 * @property string|null $color
 * @property Carbon $starts_at
 * @property Carbon $ends_at
 * @property Carbon $notify_at
 * @property string $notify_status
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property string|null $rrule
 * @property-read Collection<int, EventAttendee> $attendees
 * @property-read int|null $attendees_count
 * @property-read Collection<int, RecurrenceException> $recurrenceExceptions
 * @property-read int|null $recurrence_exceptions_count
 * @property-read User $user
 *
 * @method static \Database\Factories\EventFactory factory($count = null, $state = [])
 * @method static EventBuilder<static>|Event newModelQuery()
 * @method static EventBuilder<static>|Event newQuery()
 * @method static EventBuilder<static>|Event query()
 * @method static EventBuilder<static>|Event whereColor($value)
 * @method static EventBuilder<static>|Event whereCreatedAt($value)
 * @method static EventBuilder<static>|Event whereDescription($value)
 * @method static EventBuilder<static>|Event whereEndsAt($value)
 * @method static EventBuilder<static>|Event whereId($value)
 * @method static EventBuilder<static>|Event whereLocation($value)
 * @method static EventBuilder<static>|Event whereNotifyAt($value)
 * @method static EventBuilder<static>|Event whereNotifyStatus($value)
 * @method static EventBuilder<static>|Event whereRrule($value)
 * @method static EventBuilder<static>|Event whereStartsAt($value)
 * @method static EventBuilder<static>|Event whereTitle($value)
 * @method static EventBuilder<static>|Event whereUpdatedAt($value)
 * @method static EventBuilder<static>|Event whereUserId($value)
 * @method static EventBuilder<static>|Event withPossibleOccurrencesBetween(\Carbon\CarbonInterface $viewStart, \Carbon\CarbonInterface $viewEnd)
 *
 * @mixin \Eloquent
 */
#[UseFactory(EventFactory::class)]
#[UseEloquentBuilder(EventBuilder::class)]
class Event extends Model implements Recurrable
{
    use HasFactory, HasRecurrence;

    protected $fillable = [
        'title',
        'description',
        'starts_at',
        'ends_at',
        'notify_at',
        'notify_status',
        'color',
        'rrule',
    ];

    protected $hidden = [];

    protected function casts(): array
    {
        return [
            'notify_at' => 'datetime',
            'starts_at' => 'datetime',
            'ends_at' => 'datetime',
        ];
    }

    public function recurrenceStartKey(): string
    {
        return 'starts_at';
    }

    /** @return BelongsTo<User,$this> */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /** @return HasMany<EventAttendee,$this> */
    public function attendees(): HasMany
    {
        return $this->hasMany(EventAttendee::class);
    }

    /** @return MorphMany<RecurrenceException,$this> */
    public function recurrenceExceptions(): MorphMany
    {
        return $this->morphMany(RecurrenceException::class, 'recurrenceable');
    }
}
