<?php

declare(strict_types=1);

namespace App\Domain\Identity\Models;

use App\Domain\Identity\Enums\OtpPurpose;
use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property int $user_id
 * @property OtpPurpose $purpose
 * @property string $password
 * @property CarbonImmutable $expires_at
 * @property CarbonImmutable|null $created_at
 * @property CarbonImmutable|null $updated_at
 *
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserOneTimePasswords newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserOneTimePasswords newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserOneTimePasswords query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserOneTimePasswords whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserOneTimePasswords whereExpiresAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserOneTimePasswords whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserOneTimePasswords wherePassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserOneTimePasswords wherePurpose($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserOneTimePasswords whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserOneTimePasswords whereUserId($value)
 *
 * @mixin \Eloquent
 */
#[Fillable(['purpose', 'password', 'expires_at'])]
class UserOneTimePasswords extends Model
{
    const EXPIRES_IN_X_MINUTES = 15;

    const SIZE = 6;

    protected function casts(): array
    {
        return [
            'purpose' => OtpPurpose::class,
            'expires_at' => 'datetime',
        ];
    }
}
