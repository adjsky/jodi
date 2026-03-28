<?php

declare(strict_types=1);

namespace App\Models;

use App\Support\Recurrence\HasRecurrence;
use Database\Factories\TodoFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Todo extends Model
{
    /** @use HasFactory<TodoFactory> */
    use HasFactory, HasRecurrence;

    protected $fillable = [
        'title',
        'description',
        'category_id',
        'scheduled_at',
        'has_time',
        'notify_at',
        'notify_status',
        'color',
        'rrule',
    ];

    protected $hidden = [];

    /**
     * @return array{notify_at: "datetime", completed_at: "datetime", scheduled_at: "datetime", has_time: "boolean"}
     */
    protected function casts(): array
    {
        return [
            'notify_at' => 'datetime',
            'completed_at' => 'datetime',
            'scheduled_at' => 'datetime',
            'has_time' => 'boolean',
        ];
    }

    protected function rkstart(): string
    {
        return 'scheduled_at';
    }

    /** @return BelongsTo<User,$this> */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /** @return BelongsTo<Category,$this> */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    /** @return HasMany<TodoPosition,$this> */
    public function positions(): HasMany
    {
        return $this->hasMany(TodoPosition::class);
    }

    /** @return MorphMany<RecurrenceException,$this> */
    public function recurrenceExceptions(): MorphMany
    {
        return $this->morphMany(RecurrenceException::class, 'recurrenceable');
    }
}
