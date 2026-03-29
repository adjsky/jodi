<?php

declare(strict_types=1);

namespace App\Domain\Reminder\Listeners;

use App\Domain\Reminder\Notifications\EventReminder;
use App\Domain\Reminder\Notifications\TodoReminder;
use Illuminate\Notifications\Events\NotificationSent;

class MarkNotificationAsSent
{
    public function __construct() {}

    public function handle(NotificationSent $event): void
    {
        if (
            $event->notification instanceof EventReminder ||
            $event->notification instanceof TodoReminder
        ) {
            $model = $event->notification->model;
            $occursAt = $event->notification->occursAt;

            // if ($occursAt) {
            //     $model->applyException(
            //         $occursAt,
            //         ['notify_status' => 'sent'],
            //         $model->findException($occursAt)
            //     );
            // } else {
            //     $model->update(['notify_status' => 'sent']);
            // }
        }
    }
}
