<?php

declare(strict_types=1);

namespace App\Domain\Identity\Data\Output;

use App\Domain\Identity\Models\User;
use App\Support\Data\JodiData;
use Spatie\TypeScriptTransformer\Attributes\TypeScript;

#[TypeScript]
class AuthSessionData extends JodiData
{
    public function __construct(
        public ?string $token,
        public UserData $user,
        public ?PushSubscriptionData $pushSubscription
    ) {}

    public static function fromUser(User $user, string $deviceId, ?string $token = null): self
    {
        $pushSubscription = $user->pushSubscriptions()
            ->firstWhere('device_id', '=', $deviceId);

        return new self(
            $token,
            UserData::from($user),
            $pushSubscription ? PushSubscriptionData::from($pushSubscription) : null
        );
    }
}
