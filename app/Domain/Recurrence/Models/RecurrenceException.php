<?php

declare(strict_types=1);

namespace App\Domain\Recurrence\Models;

use Database\Factories\RecurrenceExceptionFactory;
use Illuminate\Database\Eloquent\Attributes\UseFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property string $recurrenceable_type
 * @property int $recurrenceable_id
 * @property Carbon $occurs_at
 * @property bool $is_cancelled
 * @property array<array-key, mixed> $overrides
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @method static \Database\Factories\RecurrenceExceptionFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RecurrenceException newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RecurrenceException newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RecurrenceException query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RecurrenceException whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RecurrenceException whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RecurrenceException whereIsCancelled($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RecurrenceException whereOccursAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RecurrenceException whereOverrides($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RecurrenceException whereRecurrenceableId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RecurrenceException whereRecurrenceableType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RecurrenceException whereUpdatedAt($value)
 *
 * @mixin \Eloquent
 */
#[UseFactory(RecurrenceExceptionFactory::class)]
class RecurrenceException extends Model
{
    use HasFactory;

    protected $fillable = [
        'occurs_at',
        'is_cancelled',
        'overrides',
    ];

    protected $hidden = [];

    protected function casts(): array
    {
        return [
            'occurs_at' => 'date:Y-m-d',
            'is_cancelled' => 'boolean',
            'overrides' => 'array',
        ];
    }
}
