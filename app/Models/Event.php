<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Event extends Model
{
    /** @use HasFactory<\Database\Factories\EventFactory> */
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'is_all_day',
        'starts_at',
        'ends_at',
        'color',
    ];

    protected $hidden = [];

    /**
     * @return array{is_all_day: "boolean", notify_at: "datetime", starts_at: "datetime", ends_at: "datetime"}
     */
    protected function casts(): array
    {
        return [
            'is_all_day' => 'boolean',
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
}
