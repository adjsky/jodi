<?php

declare(strict_types=1);

namespace App\Domain\Event\Notifications;

use App\Models\Event;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use NotificationChannels\WebPush\DeclarativeWebPushMessage;
use NotificationChannels\WebPush\WebPushChannel;

class EventReminder extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public Event $event) {}

    /** @return array<int, string> */
    public function via(User $user): array
    {
        return $user->preferences['notifications'] == 'push'
            ? [WebPushChannel::class]
            : ['mail'];
    }

    public function toWebPush(): DeclarativeWebPushMessage
    {
        return (new DeclarativeWebPushMessage)
            ->title('Event starts soon.')
            ->body('Event starts soon.')
            ->navigate('https://localhost:8000');
    }

    public function toMail(): MailMessage
    {
        return (new MailMessage)
            ->subject('Event starts soon.')
            ->markdown('mail.event-reminder');
    }
}
