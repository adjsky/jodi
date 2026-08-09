<?php

declare(strict_types=1);

namespace App\Domain\Reminder\Listeners;

use Illuminate\Notifications\Events\NotificationFailed;
use Illuminate\Support\Arr;
use Kreait\Firebase\Messaging\MessageTarget;
use Kreait\Firebase\Messaging\SendReport;
use NotificationChannels\Fcm\FcmChannel;

class DeleteInvalidFcmToken
{
    public function __construct() {}

    public function handle(NotificationFailed $event): void
    {
        if ($event->channel != FcmChannel::class) {
            return;
        }

        $report = Arr::get($event->data, 'report');

        if (! $report instanceof SendReport) {
            return;
        }

        $target = $report->target();

        if ($target->type() != MessageTarget::TOKEN) {
            return;
        }

        if (
            ! $report->messageWasSentToUnknownToken() &&
            ! $report->messageTargetWasInvalid()
        ) {
            return;
        }

        $event->notifiable
            ->pushSubscriptions()
            ->where('fcm_token', $target->value())
            ->delete();
    }
}
