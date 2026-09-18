<?php

declare(strict_types=1);

namespace App\Domain\Identity\ValueObjects;

use App\Domain\Identity\Enums\NotificationChannel;
use App\Domain\Identity\Enums\WeekStart;
use App\Support\Data\JodiData;
use Spatie\LaravelData\Attributes\MapName;

class UserPreferences extends JodiData
{
    public string $locale;

    public string $timezone;

    #[MapName('weekStartOn')]
    public WeekStart $weekStart;

    #[MapName('notifications')]
    public NotificationChannel $notificationChannel;

    public function merge(array $overrides): self
    {
        return self::from([
            ...$this->toArray(),
            ...$overrides,
        ]);
    }
}
