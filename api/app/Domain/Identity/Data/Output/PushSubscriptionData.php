<?php

declare(strict_types=1);

namespace App\Domain\Identity\Data\Output;

use App\Domain\Identity\Models\PushSubscription;
use App\Support\Data\JodiData;
use Spatie\TypeScriptTransformer\Attributes\TypeScript;

#[TypeScript]
class PushSubscriptionData extends JodiData
{
    public function __construct(
        public string $deviceId,
        public string $fcmToken
    ) {}

    public static function fromModel(PushSubscription $subscription): self
    {
        return new self(
            $subscription->device_id,
            $subscription->fcm_token
        );
    }
}
