<?php

declare(strict_types=1);

namespace App\Domain\Identity\Models;

use App\Domain\Event\Models\Event;
use App\Domain\Identity\Casts\UserPreferencesCast;
use App\Domain\Identity\ValueObjects\UserPreferences;
use App\Domain\Journal\Models\JournalEntry;
use App\Domain\MoodTracker\Models\MoodTrackerEntry;
use App\Domain\Todo\Models\Category;
use App\Domain\Todo\Models\Todo;
use App\Support\Concerns\HasSqid;
use Database\Factories\UserFactory;
use Illuminate\Contracts\Translation\HasLocalePreference;
use Illuminate\Database\Eloquent\Attributes\UseFactory;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Notifications\DatabaseNotificationCollection;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property string $name
 * @property string $email
 * @property string|null $avatar_url
 * @property UserPreferences $preferences
 * @property string|null $remember_token
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Collection<int, Category> $categories
 * @property-read int|null $categories_count
 * @property-read Collection<int, Event> $events
 * @property-read int|null $events_count
 * @property-read Collection<int, User> $friends
 * @property-read int|null $friends_count
 * @property-read Collection<int, RegistrationInvitation> $invitations
 * @property-read int|null $invitations_count
 * @property-read Collection<int, JournalEntry> $journalEntries
 * @property-read int|null $journal_entries_count
 * @property-read Collection<int, MoodTrackerEntry> $moodTrackerEntries
 * @property-read int|null $mood_tracker_entries_count
 * @property-read DatabaseNotificationCollection<int, DatabaseNotification> $notifications
 * @property-read int|null $notifications_count
 * @property-read Collection<int, UserOneTimePasswords> $oneTimePasswords
 * @property-read int|null $one_time_passwords_count
 * @property-read Collection<int, PushSubscription> $pushSubscriptions
 * @property-read int|null $push_subscriptions_count
 * @property-read mixed $sqid
 * @property-read Collection<int, Todo> $todos
 * @property-read int|null $todos_count
 *
 * @method static \Database\Factories\UserFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereAvatarUrl($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User wherePreferences($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereRememberToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereUpdatedAt($value)
 *
 * @mixin \Eloquent
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
            'preferences' => UserPreferencesCast::class,
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
