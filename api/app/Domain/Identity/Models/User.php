<?php

declare(strict_types=1);

namespace App\Domain\Identity\Models;

use App\Domain\Event\Models\Event;
use App\Domain\Identity\Casts\UserPreferencesCast;
use App\Domain\Journal\Models\JournalEntry;
use App\Domain\MoodTracker\Models\MoodTrackerEntry;
use App\Domain\Todo\Models\Category;
use App\Domain\Todo\Models\Todo;
use App\Support\Identifiers\Concerns\HasSqid;
use Database\Factories\UserFactory;
use Illuminate\Contracts\Translation\HasLocalePreference;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Attributes\UseFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Laravel\Sanctum\PersonalAccessToken;
use Laravel\Sanctum\TransientToken;

#[Fillable([
    'name',
    'email',
    'preferences',
])]
#[Hidden(['remember_token'])]
#[UseFactory(UserFactory::class)]
class User extends Authenticatable implements HasLocalePreference
{
    /** @use HasApiTokens<PersonalAccessToken|TransientToken> */
    use HasApiTokens, HasFactory, HasSqid, Notifiable;

    protected function casts(): array
    {
        return [
            'preferences' => UserPreferencesCast::class,
        ];
    }

    public function preferredLocale(): string
    {
        return $this->preferences->locale;
    }

    public function routeNotificationForFcm(): array
    {
        return $this->pushSubscriptions->pluck('fcm_token')->toArray();
    }

    /** @return BelongsToMany<User, $this> */
    public function friends(): BelongsToMany
    {
        return $this->belongsToMany(
            User::class,
            'user_friends',
            'user_id',
            'friend_id',
        );
    }

    /** @return HasMany<RegistrationInvitation, $this> */
    public function invitations(): HasMany
    {
        return $this->hasMany(
            RegistrationInvitation::class,
            'inviter_user_id',
            'id',
        );
    }

    /** @return HasMany<UserChallenge, $this> */
    public function challenges(): HasMany
    {
        return $this->hasMany(UserChallenge::class);
    }

    /** @return HasMany<Category, $this> */
    public function categories(): HasMany
    {
        return $this->hasMany(Category::class);
    }

    /** @return HasMany<Todo, $this> */
    public function todos(): HasMany
    {
        return $this->hasMany(Todo::class);
    }

    /** @return HasMany<Event, $this> */
    public function events(): HasMany
    {
        return $this->hasMany(Event::class);
    }

    /** @return HasMany<JournalEntry, $this> */
    public function journalEntries(): HasMany
    {
        return $this->hasMany(JournalEntry::class);
    }

    /** @return HasMany<MoodTrackerEntry, $this> */
    public function moodTrackerEntries(): HasMany
    {
        return $this->hasMany(MoodTrackerEntry::class);
    }

    /** @return HasMany<PushSubscription, $this> */
    public function pushSubscriptions(): HasMany
    {
        return $this->hasMany(PushSubscription::class);
    }
}
