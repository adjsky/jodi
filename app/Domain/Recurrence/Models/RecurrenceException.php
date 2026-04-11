<?php

declare(strict_types=1);

namespace App\Domain\Recurrence\Models;

use Database\Factories\RecurrenceExceptionFactory;
use Illuminate\Database\Eloquent\Attributes\UseFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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
