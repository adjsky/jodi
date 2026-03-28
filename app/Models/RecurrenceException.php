<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RecurrenceException extends Model
{
    /** @use HasFactory<\Database\Factories\RecurrenceExceptionFactory> */
    use HasFactory;

    protected $fillable = [
        'occurs_at',
        'is_cancelled',
        'overrides',
    ];

    protected $hidden = [];

    /**
     * @return array{occurs_at: "date:Y-m-d", is_cancelled: "boolean", overrides: "array"}
     */
    protected function casts(): array
    {
        return [
            'occurs_at' => 'date:Y-m-d',
            'is_cancelled' => 'boolean',
            'overrides' => 'array',
        ];
    }
}
