<?php

declare(strict_types=1);

namespace App\Domain\MoodTracker\Models;

use Database\Factories\MoodTrackerEntryFactory;
use Illuminate\Database\Eloquent\Attributes\UseFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[UseFactory(MoodTrackerEntryFactory::class)]
class MoodTrackerEntry extends Model
{
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
