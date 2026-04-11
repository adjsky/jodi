<?php

declare(strict_types=1);

namespace App\Domain\Identity\Actions;

use App\Domain\Identity\Data\Input\UpsertPushSubscriptionData;
use App\Support\Actions\Action;
use App\Support\Http\JodiRequest;
use Illuminate\Http\RedirectResponse;

class UpsertPushSubscription extends Action
{
    public function handle(UpsertPushSubscriptionData $data): void
    {
        $this->user()->pushSubscriptions()->updateOrCreate(
            ['device_id' => $data->device_id],
            $data->toArray(),
        );
    }

    public function asController(JodiRequest $request): RedirectResponse
    {
        $this->handle(UpsertPushSubscriptionData::from($request));

        return back();
    }
}
