<?php

declare(strict_types=1);

namespace App\Domain\Recurrence\Models;

use Database\Factories\RecurrenceExceptionFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\UseFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

#[Fillable([
    'occurs_at',
    'is_cancelled',
    'overrides',
])]
#[UseFactory(RecurrenceExceptionFactory::class)]
class RecurrenceException extends Model
{
    use HasFactory;

    protected function casts(): array
    {
        return [
            'occurs_at' => 'date:Y-m-d',
            'is_cancelled' => 'boolean',
            'overrides' => 'array',
        ];
    }

    public function recurrenceable(): MorphTo
    {
        return $this->morphTo();
    }
}
