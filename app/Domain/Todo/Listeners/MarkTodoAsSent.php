<?php

declare(strict_types=1);

namespace App\Domain\Todo\Listeners;

use App\Domain\Todo\Notifications\TodoReminder;
use Illuminate\Notifications\Events\NotificationSent;

class MarkTodoAsSent
{
    public function __construct() {}

    public function handle(NotificationSent $event): void
    {
        if ($event->notification instanceof TodoReminder) {
            $model = $event->notification->todo;
            $model->notify_status = 'sent';
            $model->save();
        }
    }
}
