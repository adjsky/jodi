<?php

declare(strict_types=1);

namespace App\Domain\Event\Models;

use App\Domain\Identity\Models\User;
use App\Domain\Recurrence\Concerns\HasRecurrence;
use App\Domain\Recurrence\Models\RecurrenceException;
use Database\Factories\EventFactory;
use Illuminate\Database\Eloquent\Attributes\UseFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

#[UseFactory(EventFactory::class)]
class Event extends Model
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

    protected function rkstart(): string
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
