<?php

declare(strict_types=1);

namespace App\Domain\Identity\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $user_id
 * @property string $purpose
 * @property string $password
 * @property Carbon $expires_at
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
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
class UserOneTimePasswords extends Model
{
    const EXPIRES_IN_X_MINUTES = 15;

    const SIZE = 6;

    protected $fillable = ['purpose', 'password', 'expires_at'];

    protected $hidden = [];

    protected function casts(): array
    {
        return [
            'expires_at' => 'datetime',
        ];
    }
}
