<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Recurrence extends Model
{
    /** @use HasFactory<\Database\Factories\RecurrenceFactory> */
    use HasFactory;

    protected $fillable = [];

    protected $hidden = [];

    protected function casts(): array
    {
        return [
            'recurring_since' => 'date:Y-m-d',
            'generated_until' => 'date:Y-m-d',
        ];
    }
}
