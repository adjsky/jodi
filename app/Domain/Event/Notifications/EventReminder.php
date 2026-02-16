<?php

declare(strict_types=1);

namespace App\Domain\Event\Notifications;

use App\Models\Event;
use App\Models\User;
use App\Support\Reminder;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use NotificationChannels\Fcm\FcmChannel;
use NotificationChannels\Fcm\FcmMessage;
use NotificationChannels\Fcm\Resources\Notification as FcmNotification;

class EventReminder extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public Event $event) {}

    /** @return array<int, string> */
    public function via(User $user): array
    {
        return $user->preferences['notifications'] == 'push'
            ? [FcmChannel::class]
            : ['mail'];
    }

    public function toFcm(): FcmMessage
    {
        return new FcmMessage(notification: new FcmNotification(
            title: __(':title is upcoming.', ['title' => $this->event->title]),
            body: __('Starts :time.', ['time' => Reminder::startsIn($this->event->starts_at)]),
        ));
    }

    public function toMail(): MailMessage
    {
        return (new MailMessage)
            ->subject(__('mail.event_reminder.subject', ['title' => $this->event->title, 'startsIn' => Reminder::startsIn($this->event->starts_at)]))
            ->markdown('mail.event-reminder', ['event' => $this->event]);
    }
}
