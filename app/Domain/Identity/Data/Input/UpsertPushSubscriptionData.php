<?php

declare(strict_types=1);

namespace App\Domain\Identity\Data\Input;

use Spatie\LaravelData\Attributes\Validation\In;
use Spatie\LaravelData\Data;

class UpsertPushSubscriptionData extends Data
{
    public string $fcm_token;

    #[In('web', 'android')]
    public string $platform;

    public string $device_id;
}
