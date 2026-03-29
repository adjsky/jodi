<?php

declare(strict_types=1);

namespace App\Domain\Reminder\Notifications;

use App\Domain\Reminder\Support\Helpers;
use App\Models\Todo;
use App\Models\User;
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

    public function toFcm(): FcmMessage
    {
        return new FcmMessage(notification: new FcmNotification(
            title: __(':title - time to start.', ['title' => $this->model->title]),
            body: __('Scheduled for :time.', ['time' => $this->startsIn()]),
        ))
            ->data([
                'todo_id' => (string) $this->model->id,
            ])
            ->custom([
                'webpush' => [
                    'notification' => [
                        'actions' => [
                            [
                                'action' => 'complete-todo',
                                'title' => __('Complete'),
                            ],
                        ],
                    ],
                    'fcm_options' => [
                        'link' => config('app.url'),
                    ],
                ],
            ]);
    }

    public function toMail(): MailMessage
    {
        return (new MailMessage)
            ->subject(__('mail.todo_reminder.subject', ['title' => $this->model->title, 'startsIn' => $this->startsIn()]))
            ->markdown('mail.todo-reminder', ['todo' => $this->model, 'startsIn' => $this->startsIn()]);
    }

    private function startsIn(): string
    {
        $scheduledAt = $this->model->scheduled_at->copy();

        if (! is_null($this->occursAt)) {
            $scheduledAt->setDateFrom($this->occursAt);
        }

        return Helpers::startsIn($scheduledAt);
    }
}
