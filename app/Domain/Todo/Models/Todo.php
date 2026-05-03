<?php

declare(strict_types=1);

namespace App\Domain\Todo\Models;

use App\Domain\Identity\Models\User;
use App\Domain\Recurrence\Concerns\HasRecurrence;
use App\Domain\Recurrence\Contracts\Recurrable;
use App\Domain\Recurrence\Models\RecurrenceException;
use App\Domain\Todo\Builders\TodoBuilder;
use Database\Factories\TodoFactory;
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
 * @property int|null $category_id
 * @property Carbon $scheduled_at
 * @property bool $has_time
 * @property string|null $color
 * @property Carbon|null $completed_at
 * @property Carbon|null $notify_at
 * @property string|null $notify_status
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property string|null $rrule
 * @property-read Category|null $category
 * @property-read Collection<int, TodoPosition> $positions
 * @property-read int|null $positions_count
 * @property-read Collection<int, RecurrenceException> $recurrenceExceptions
 * @property-read int|null $recurrence_exceptions_count
 * @property-read User $user
 *
 * @method static \Database\Factories\TodoFactory factory($count = null, $state = [])
 * @method static TodoBuilder<static>|Todo newModelQuery()
 * @method static TodoBuilder<static>|Todo newQuery()
 * @method static TodoBuilder<static>|Todo query()
 * @method static TodoBuilder<static>|Todo whereCategoryId($value)
 * @method static TodoBuilder<static>|Todo whereColor($value)
 * @method static TodoBuilder<static>|Todo whereCompletedAt($value)
 * @method static TodoBuilder<static>|Todo whereCreatedAt($value)
 * @method static TodoBuilder<static>|Todo whereDescription($value)
 * @method static TodoBuilder<static>|Todo whereHasTime($value)
 * @method static TodoBuilder<static>|Todo whereId($value)
 * @method static TodoBuilder<static>|Todo whereNotifyAt($value)
 * @method static TodoBuilder<static>|Todo whereNotifyStatus($value)
 * @method static TodoBuilder<static>|Todo whereRrule($value)
 * @method static TodoBuilder<static>|Todo whereScheduledAt($value)
 * @method static TodoBuilder<static>|Todo whereTitle($value)
 * @method static TodoBuilder<static>|Todo whereUpdatedAt($value)
 * @method static TodoBuilder<static>|Todo whereUserId($value)
 * @method static TodoBuilder<static>|Todo withPossibleOccurrencesBetween(\Carbon\CarbonInterface $viewStart, \Carbon\CarbonInterface $viewEnd)
 *
 * @mixin \Eloquent
 */
#[UseFactory(TodoFactory::class)]
#[UseEloquentBuilder(TodoBuilder::class)]
class Todo extends Model implements Recurrable
{
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

    protected function casts(): array
    {
        return [
            'notify_at' => 'datetime',
            'completed_at' => 'datetime',
            'scheduled_at' => 'datetime',
            'has_time' => 'boolean',
        ];
    }

    public function recurrenceStartKey(): string
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
