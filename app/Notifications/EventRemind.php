<?php

declare(strict_types=1);

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use NotificationChannels\WebPush\DeclarativeWebPushMessage;
use NotificationChannels\WebPush\WebPushChannel;

class EventRemind extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct() {}

    /** @return array<int, string> */
    public function via(): array
    {
        return [WebPushChannel::class];
    }

    public function toWebPush(): DeclarativeWebPushMessage
    {
        return (new DeclarativeWebPushMessage)
            ->title('Event starts soon.')
            ->body('Event starts soon.');
    }
}
