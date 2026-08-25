<?php

declare(strict_types=1);

namespace App\Domain\Identity\Data\Input;

use App\Domain\Identity\Enums\NotificationChannel;
use App\Domain\Identity\Enums\WeekStart;
use App\Support\Data\JodiData;
use Spatie\LaravelData\Optional;

class UpdateUserPreferencesData extends JodiData
{
    public string|Optional $locale;

    #[Timezone]
    public string|Optional $timezone;

    public WeekStart|Optional $weekStartOn;

    public NotificationChannel|Optional $notifications;
}
