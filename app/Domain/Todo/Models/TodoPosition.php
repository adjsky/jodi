<?php

declare(strict_types=1);

namespace App\Domain\Todo\Models;

use Database\Factories\TodoPositionFactory;
use Illuminate\Database\Eloquent\Attributes\UseFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $todo_id
 * @property string $date
 * @property int $position
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
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
        'occurs_at',
        'position',
    ];

    protected $hidden = [];

    protected function casts(): array
    {
        return [
            'occurs_at' => 'date:Y-m-d',
        ];
    }
}
