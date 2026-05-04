<?php

declare(strict_types=1);

namespace App\Domain\Identity\Data\Input;

use App\Domain\Identity\Enums\NotificationChannel;
use App\Domain\Identity\Enums\WeekStart;
use Spatie\LaravelData\Attributes\Validation\Email;
use Spatie\LaravelData\Attributes\Validation\Max;
use Spatie\LaravelData\Attributes\Validation\Min;
use Spatie\LaravelData\Attributes\Validation\Timezone;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Optional;

class UpdateUserData extends Data
{
    #[Min(1), Max(36)]
    public string|Optional $name;

    #[Email]
    public string|Optional $email;

    public UpdateUserPreferencesData|Optional $preferences;
}

class UpdateUserPreferencesData extends Data
{
    public string|Optional $locale;

    #[Timezone]
    public string|Optional $timezone;

    public WeekStart|Optional $weekStartOn;

    public NotificationChannel|Optional $notifications;
}
