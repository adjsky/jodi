<?php

declare(strict_types=1);

namespace App\Domain\Event\Models;

use App\Domain\Event\Builders\EventBuilder;
use App\Domain\Identity\Models\User;
use App\Domain\Recurrence\Concerns\HasRecurrence;
use App\Domain\Recurrence\Contracts\Recurrable;
use App\Domain\Recurrence\Models\RecurrenceException;
use App\Domain\Reminder\Enums\ReminderDeliveryStatus;
use Database\Factories\EventFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\UseEloquentBuilder;
use Illuminate\Database\Eloquent\Attributes\UseFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

#[Fillable([
    'title',
    'description',
    'starts_at',
    'ends_at',
    'notify_at',
    'notify_status',
    'color',
    'rrule',
])]
#[UseFactory(EventFactory::class)]
#[UseEloquentBuilder(EventBuilder::class)]
class Event extends Model implements Recurrable
{
    use HasFactory, HasRecurrence;

    const OVERRIDABLE_ATTRIBUTES = [
        'title',
        'description',
        'location',
        'color',
        'starts_at',
        'ends_at',
        'notify_at',
    ];

    protected function casts(): array
    {
        return [
            'notify_at' => 'datetime',
            'starts_at' => 'datetime',
            'ends_at' => 'datetime',
            'notify_status' => ReminderDeliveryStatus::class,
        ];
    }

    public function recurrenceStartKey(): string
    {
        return 'starts_at';
    }

    public function recurrenceEndKey(): ?string
    {
        return 'ends_at';
    }

    /** @return BelongsTo<User, $this> */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /** @return HasMany<EventAttendee, $this> */
    public function attendees(): HasMany
    {
        return $this->hasMany(EventAttendee::class);
    }

    /** @return MorphMany<RecurrenceException, $this> */
    public function recurrenceExceptions(): MorphMany
    {
        return $this->morphMany(RecurrenceException::class, 'recurrenceable');
    }
}
