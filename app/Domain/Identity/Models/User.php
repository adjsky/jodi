<?php

declare(strict_types=1);

namespace App\Domain\Identity\Models;

use App\Domain\Event\Models\Event;
use App\Domain\Identity\ValueObjects\UserPreferences;
use App\Domain\Journal\Models\JournalEntry;
use App\Domain\MoodTracker\Models\MoodTrackerEntry;
use App\Domain\Todo\Models\Category;
use App\Domain\Todo\Models\Todo;
use App\Support\Concerns\HasSqid;
use Database\Factories\UserFactory;
use Illuminate\Contracts\Translation\HasLocalePreference;
use Illuminate\Database\Eloquent\Attributes\UseFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

/**
 * TODO: find out the reason why larastan fails to resolve the preferences type
 *
 * @property UserPreferences $preferences
 */
#[UseFactory(UserFactory::class)]
class User extends Authenticatable implements HasLocalePreference
{
    use HasFactory, HasSqid, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'preferences',
    ];

    protected $hidden = [
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'preferences' => UserPreferences::class,
        ];
    }

    public function preferredLocale(): string
    {
        return $this->preferences->locale;
    }

    /**
     * @return string[]
     */
    public function routeNotificationForFcm(): array
    {
        return $this->pushSubscriptions->pluck('fcm_token')->toArray();
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

    /** @return HasMany<Category,$this> */
    public function categories(): HasMany
    {
        return $this->hasMany(Category::class);
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

    /** @return HasMany<PushSubscription,$this> */
    public function pushSubscriptions(): HasMany
    {
        return $this->hasMany(PushSubscription::class);
    }
}
