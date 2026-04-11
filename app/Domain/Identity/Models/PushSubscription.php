<?php

declare(strict_types=1);

namespace App\Domain\Identity\Models;

use Database\Factories\PushSubscriptionFactory;
use Illuminate\Database\Eloquent\Attributes\UseFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[UseFactory(PushSubscriptionFactory::class)]
class PushSubscription extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'fcm_token',
        'platform',
        'device_id',
    ];

    protected $hidden = [];

    protected function casts(): array
    {
        return [];
    }

    /** @return BelongsTo<User,$this> */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
