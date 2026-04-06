<?php

declare(strict_types=1);

namespace App\Domain\Reminder\Notifications;

use App\Domain\Reminder\Support\Carbon\CalendarFormatter;
use App\Models\Todo;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use NotificationChannels\Fcm\FcmChannel;
use NotificationChannels\Fcm\FcmMessage;
use NotificationChannels\Fcm\Resources\Notification as FcmNotification;

class TodoReminder extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public Todo $model, public ?string $occursAt) {}

    public function via(User $user): array
    {
        return $user->preferences['notifications'] == 'push'
            ? [FcmChannel::class]
            : ['mail'];
    }

    public function toFcm(User $user): FcmMessage
    {
        $scheduledAt = $this->scheduledAt($user->preferences['timezone']);

        return new FcmMessage(notification: new FcmNotification(
            title: $this->model->title,
            body: CalendarFormatter::format($scheduledAt),
        ))
            ->data([
                'purpose' => 'reminder',
                'target' => 'todo',
                'd' => $scheduledAt->toDateString(),
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
        $scheduledAt = $this->scheduledAt($user->preferences['timezone']);

        return (new MailMessage)
            ->subject(__('mail.todo_reminder.subject', ['title' => $this->model->title]))
            ->markdown('mail.todo-reminder', ['todo' => $this->model, 'time' => mb_lcfirst(CalendarFormatter::format($scheduledAt))]);
    }

    private function scheduledAt(string $timezone): Carbon
    {
        $scheduledAt = $this->model->scheduled_at->copy();

        if (! is_null($this->occursAt)) {
            $scheduledAt->setDateFrom($this->occursAt);
        }

        return $scheduledAt->timezone($timezone);
    }
}
