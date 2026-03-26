<?php

declare(strict_types=1);

namespace App\Domain\Reminder\Notifications;

use App\Domain\Reminder\Support\Helpers;
use App\Models\Event;
use App\Models\User;
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

    public function __construct(public Event $model, public ?string $occursAt) {}

    public function via(User $user): array
    {
        return $user->preferences['notifications'] == 'push'
            ? [FcmChannel::class]
            : ['mail'];
    }

    public function toFcm(): FcmMessage
    {
        return new FcmMessage(notification: new FcmNotification(
            title: __(':title is upcoming.', ['title' => $this->model->title]),
            body: __('Starts :time.', ['time' => Helpers::startsIn($this->model->starts_at)]),
        ));
    }

    public function toMail(): MailMessage
    {
        return (new MailMessage)
            ->subject(__('mail.event_reminder.subject', ['title' => $this->model->title, 'startsIn' => Helpers::startsIn($this->model->starts_at)]))
            ->markdown('mail.event-reminder', ['event' => $this->model]);
    }
}
