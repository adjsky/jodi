<?php

declare(strict_types=1);

namespace App\Domain\Identity\Data\Input;

use App\Support\Data\JodiData;
use Spatie\LaravelData\Attributes\Validation\In;
use Spatie\LaravelData\Attributes\Validation\Max;

class UpsertPushSubscriptionData extends JodiData
{
    #[Max(4096)]
    public string $fcmToken;

    #[In('web', 'android')]
    public string $platform;
}
