<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MoodTrackerEntry extends Model
{
    /** @use HasFactory<\Database\Factories\MoodTrackerEntryFactory> */
    use HasFactory;

    protected $fillable = [];

    protected $hidden = [];

    protected function casts(): array
    {
        return [
            'entry_date' => 'date:Y-m-d',
        ];
    }
}
