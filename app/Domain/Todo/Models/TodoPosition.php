<?php

declare(strict_types=1);

namespace App\Domain\Todo\Models;

use Carbon\CarbonImmutable;
use Database\Factories\TodoPositionFactory;
use Illuminate\Database\Eloquent\Attributes\UseFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $todo_id
 * @property CarbonImmutable $date
 * @property int $position
 * @property CarbonImmutable|null $created_at
 * @property CarbonImmutable|null $updated_at
 * @property-read Todo $todo
 *
 * @method static \Database\Factories\TodoPositionFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TodoPosition newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TodoPosition newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TodoPosition query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TodoPosition whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TodoPosition whereDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TodoPosition whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TodoPosition wherePosition($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TodoPosition whereTodoId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TodoPosition whereUpdatedAt($value)
 *
 * @mixin \Eloquent
 */
#[UseFactory(TodoPositionFactory::class)]
class TodoPosition extends Model
{
    use HasFactory;

    protected $fillable = [
        'date',
        'position',
    ];

    protected $hidden = [];

    protected function casts(): array
    {
        return [
            'date' => 'date:Y-m-d',
        ];
    }

    /** @return BelongsTo<Todo,$this> */
    public function todo(): BelongsTo
    {
        return $this->belongsTo(Todo::class);
    }
}
