<?php

declare(strict_types=1);

namespace App\Domain\Event\Listeners;

use App\Domain\Event\Notifications\EventReminder;
use Illuminate\Notifications\Events\NotificationSent;

class MarkEventAsSent
{
    public function __construct() {}

    public function handle(NotificationSent $event): void
    {
        if ($event->notification instanceof EventReminder) {
            $model = $event->notification->event;
            $model->notify_status = 'sent';
            $model->save();
        }
    }
}
