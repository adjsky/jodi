<?php

declare(strict_types=1);

namespace App\Domain\Identity\ValueObjects;

use App\Domain\Identity\Enums\NotificationChannel;
use App\Domain\Identity\Enums\WeekStart;
use Spatie\LaravelData\Attributes\MapName;
use Spatie\LaravelData\Data;

class UserPreferences extends Data
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
