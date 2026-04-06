<?php

declare(strict_types=1);

namespace App\Domain\Reminder\Notifications;

use App\Domain\Reminder\Support\Carbon\CalendarFormatter;
use App\Models\Event;
use App\Models\User;
use Carbon\Carbon;
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

    public function toFcm(User $user): FcmMessage
    {
        $startsAt = $this->startsAt($user->preferences['timezone']);

        return new FcmMessage(notification: new FcmNotification(
            title: $this->model->title,
            body: CalendarFormatter::format($startsAt),
        ))
            ->data([
                'purpose' => 'reminder',
                'target' => 'event',
                'd' => $startsAt->toDateString(),
                'id' => (string) $this->model->id,
            ])
            ->custom([
                'webpush' => [
                    'fcm_options' => [
                        'link' => url('/'),
                    ],
                ],
                'android' => [
                    'priority' => 'high',
                ],
            ]);
    }

    public function toMail(User $user): MailMessage
    {
        $startsAt = $this->startsAt($user->preferences['timezone']);
        $time = mb_lcfirst(CalendarFormatter::format($startsAt));

        return (new MailMessage)
            ->subject(__('mail.event_reminder.subject', ['title' => $this->model->title, 'time' => $time]))
            ->markdown('mail.event-reminder', ['event' => $this->model, 'time' => $time]);
    }

    private function startsAt(string $timezone): Carbon
    {
        $startsAt = $this->model->starts_at->copy();

        if (! is_null($this->occursAt)) {
            $startsAt->setDateFrom($this->occursAt);
        }

        return $startsAt->timezone($timezone);
    }
}
