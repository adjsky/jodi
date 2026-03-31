<?php

declare(strict_types=1);

namespace App\Domain\Reminder\Notifications;

use App\Domain\Reminder\Support\Helpers;
use App\Models\Todo;
use App\Models\User;
use Carbon\CarbonImmutable;
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
        $scheduledAt = $this->scheduledAt();
        $startsIn = Helpers::startsIn($scheduledAt);
        $timezone = $user->preferences['timezone'];

        return new FcmMessage(notification: new FcmNotification(
            title: __(':title - time to start.', ['title' => $this->model->title]),
            body: __('Scheduled for :time.', ['time' => $startsIn]),
        ))
            ->data([
                'purpose' => 'reminder',
                'target' => 'todo',
                'd' => $scheduledAt->timezone($timezone)->toDateString(),
                'id' => (string) $this->model->id,
            ])
            ->custom([
                'webpush' => [
                    'fcm_options' => [
                        'link' => url('/'),
                    ],
                ],

            ]);
    }

    public function toMail(): MailMessage
    {
        $startsIn = Helpers::startsIn($this->scheduledAt());

        return (new MailMessage)
            ->subject(__('mail.todo_reminder.subject', ['title' => $this->model->title, 'startsIn' => $startsIn]))
            ->markdown('mail.todo-reminder', ['todo' => $this->model, 'startsIn' => $startsIn]);
    }

    private function scheduledAt(): CarbonImmutable
    {
        $scheduledAt = $this->model->scheduled_at->copy();

        if (! is_null($this->occursAt)) {
            $scheduledAt->setDateFrom($this->occursAt);
        }

        return $scheduledAt->toImmutable();
    }
}
