<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Event extends Model
{
    /** @use HasFactory<\Database\Factories\EventFactory> */
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'starts_at',
        'ends_at',
        'notify_at',
        'notify_status',
        'color',
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

    /** @return HasMany<EventAttendee,$this> */
    public function attendees(): HasMany
    {
        return $this->hasMany(EventAttendee::class);
    }

    /** @return BelongsTo<User,$this> */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
