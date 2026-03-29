<?php

declare(strict_types=1);

namespace App\Models;

use App\Support\Traits\HasRecurrence;
use Database\Factories\EventFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Event extends Model
{
    /** @use HasFactory<EventFactory> */
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

    /**
     * @return array{notify_at: "datetime", starts_at: "datetime", ends_at: "datetime"}
     */
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
