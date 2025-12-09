<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'preferences',
    ];

    protected $hidden = [
        'remember_token',
    ];

    /**
     * @return array{preferences: "array"}
     */
    protected function casts(): array
    {
        return [
            'preferences' => 'array',
        ];
    }

    /** @return BelongsToMany<User,$this> */
    public function friends(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'user_friends', 'user_id', 'friend_id');
    }

    /** @return HasMany<RegistrationInvitation,$this> */
    public function invitations(): HasMany
    {
        return $this->hasMany(RegistrationInvitation::class, 'inviter_user_id', 'id');
    }

    /** @return HasMany<UserOneTimePasswords,$this> */
    public function oneTimePasswords(): HasMany
    {
        return $this->hasMany(UserOneTimePasswords::class);
    }

    /** @return HasMany<Todo,$this> */
    public function todos(): HasMany
    {
        return $this->hasMany(Todo::class);
    }

    /** @return HasMany<Event,$this> */
    public function events(): HasMany
    {
        return $this->hasMany(Event::class);
    }

    /** @return HasMany<JournalEntry,$this> */
    public function journalEntries(): HasMany
    {
        return $this->hasMany(JournalEntry::class);
    }

    /** @return HasMany<MoodTrackerEntry,$this> */
    public function moodTrackerEntries(): HasMany
    {
        return $this->hasMany(MoodTrackerEntry::class);
    }
}
