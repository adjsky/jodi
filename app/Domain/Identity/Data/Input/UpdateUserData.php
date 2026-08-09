<?php

declare(strict_types=1);

namespace App\Domain\Identity\Data\Input;

use App\Domain\Identity\Enums\NotificationChannel;
use App\Domain\Identity\Enums\WeekStart;
use App\Support\Data\Attributes\PreprocessWith;
use App\Support\Data\JodiData;
use App\Support\Data\Preprocessors\EmailPreprocessor;
use Spatie\LaravelData\Attributes\Validation\Email;
use Spatie\LaravelData\Attributes\Validation\Max;
use Spatie\LaravelData\Attributes\Validation\Min;
use Spatie\LaravelData\Attributes\Validation\Rule;
use Spatie\LaravelData\Attributes\Validation\Timezone;
use Spatie\LaravelData\Optional;

class UpdateUserData extends JodiData
{
    #[Min(1), Max(36)]
    public string|Optional $name;

    #[PreprocessWith(EmailPreprocessor::class)]
    #[Email, Max(254), Rule('unique:registration_invitations,email', 'unique:users,email')]
    public string|Optional $email;

    public UpdateUserPreferencesData|Optional $preferences;
}

class UpdateUserPreferencesData extends JodiData
{
    public string|Optional $locale;

    #[Timezone]
    public string|Optional $timezone;

    public WeekStart|Optional $weekStartOn;

    public NotificationChannel|Optional $notifications;
}
