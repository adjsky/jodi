<?php

declare(strict_types=1);

namespace App\Domain\MoodTracker\Models;

use Database\Factories\MoodTrackerEntryFactory;
use Illuminate\Database\Eloquent\Attributes\UseFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $user_id
 * @property int $mood_score
 * @property string|null $notes
 * @property Carbon $entry_date
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @method static \Database\Factories\MoodTrackerEntryFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MoodTrackerEntry newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MoodTrackerEntry newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MoodTrackerEntry query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MoodTrackerEntry whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MoodTrackerEntry whereEntryDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MoodTrackerEntry whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MoodTrackerEntry whereMoodScore($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MoodTrackerEntry whereNotes($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MoodTrackerEntry whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MoodTrackerEntry whereUserId($value)
 *
 * @mixin \Eloquent
 */
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
