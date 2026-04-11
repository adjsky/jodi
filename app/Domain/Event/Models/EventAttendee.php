<?php

declare(strict_types=1);

namespace App\Domain\Event\Models;

use App\Domain\Identity\Models\User;
use Database\Factories\EventAttendeeFactory;
use Illuminate\Database\Eloquent\Attributes\UseFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

#[UseFactory(EventAttendeeFactory::class)]
class EventAttendee extends Model
{
    use HasFactory;

    protected $fillable = [];

    protected $hidden = [];

    protected function casts(): array
    {
        return [];
    }

    /** @return HasOne<User,$this> */
    public function user(): HasOne
    {
        return $this->hasOne(User::class);
    }
}
