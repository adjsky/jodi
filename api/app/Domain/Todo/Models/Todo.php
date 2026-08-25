<?php

declare(strict_types=1);

namespace App\Domain\Todo\Models;

use App\Domain\Identity\Models\User;
use App\Domain\Recurrence\Concerns\HasRecurrence;
use App\Domain\Recurrence\Contracts\Recurrable;
use App\Domain\Recurrence\Models\RecurrenceException;
use App\Domain\Reminder\Enums\ReminderDeliveryStatus;
use App\Domain\Todo\Builders\TodoBuilder;
use Database\Factories\TodoFactory;
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
    'category_id',
    'scheduled_at',
    'has_time',
    'notify_at',
    'notify_status',
    'color',
    'rrule',
])]
#[UseFactory(TodoFactory::class)]
#[UseEloquentBuilder(TodoBuilder::class)]
class Todo extends Model implements Recurrable
{
    use HasFactory, HasRecurrence;

    const OVERRIDABLE_ATTRIBUTES = [
        'title',
        'description',
        'color',
        'category_id',
        'scheduled_at',
        'has_time',
        'notify_at',
    ];

    protected function casts(): array
    {
        return [
            'notify_at' => 'datetime',
            'completed_at' => 'datetime',
            'scheduled_at' => 'datetime',
            'has_time' => 'boolean',
            'notify_status' => ReminderDeliveryStatus::class,
        ];
    }

    public function recurrenceStartKey(): string
    {
        return 'scheduled_at';
    }

    public function recurrenceEndKey(): ?string
    {
        return null;
    }

    /** @return BelongsTo<User, $this> */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /** @return BelongsTo<Category, $this> */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    /** @return HasMany<TodoPosition, $this> */
    public function positions(): HasMany
    {
        return $this->hasMany(TodoPosition::class);
    }

    /** @return MorphMany<RecurrenceException, $this> */
    public function recurrenceExceptions(): MorphMany
    {
        return $this->morphMany(RecurrenceException::class, 'recurrenceable');
    }
}
