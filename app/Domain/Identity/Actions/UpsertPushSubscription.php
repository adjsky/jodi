<?php

declare(strict_types=1);

namespace App\Domain\Identity\Actions;

use App\Domain\Identity\Data\Input\UpsertPushSubscriptionData;
use App\Domain\Identity\Models\PushSubscription;
use App\Domain\Identity\Models\User;
use App\Support\Actions\JodiAction;
use App\Support\Http\JodiRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;

class UpsertPushSubscription extends JodiAction
{
    public function handle(User $user, string $deviceId, UpsertPushSubscriptionData $data): void
    {
        DB::transaction(function () use ($user, $deviceId, $data) {
            PushSubscription::query()
                ->where('fcm_token', $data->fcmToken)
                ->where(function ($query) use ($user, $deviceId): void {
                    $query
                        ->where('user_id', '!=', $user->id)
                        ->orWhere('device_id', '!=', $deviceId);
                })
                ->delete();

            $user->pushSubscriptions()->upsert(
                [
                    [
                        'fcm_token' => $data->fcmToken,
                        'platform' => $data->platform,
                        'device_id' => $deviceId,
                    ],
                ],
                uniqueBy: ['user_id', 'device_id'],
                update: ['fcm_token', 'platform']
            );
        });
    }

    public function asController(JodiRequest $request): RedirectResponse
    {
        $this->handle(
            $this->user(),
            $request->deviceId(),
            UpsertPushSubscriptionData::from($request),
        );

        return back();
    }
}
