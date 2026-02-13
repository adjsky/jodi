<?php

declare(strict_types=1);

namespace App\Domain\Firebase\Listeners;

use Illuminate\Notifications\Events\NotificationFailed;
use Illuminate\Support\Arr;
use NotificationChannels\Fcm\FcmChannel;

class DeleteExpiredFcmTokens
{
    public function __construct() {}

    public function handle(NotificationFailed $event): void
    {
        if ($event->channel == FcmChannel::class) {
            $report = Arr::get($event->data, 'report');
            $target = $report->target();

            $event->notifiable->pushSubscriptions()
                ->where('fcm_token', $target->value())
                ->delete();
        }
    }
}
