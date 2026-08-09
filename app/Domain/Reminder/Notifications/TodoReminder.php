<?php

declare(strict_types=1);

namespace App\Domain\Reminder\Notifications;

use App\Domain\Identity\Enums\NotificationChannel;
use App\Domain\Identity\Models\User;
use App\Domain\Reminder\Enums\ReminderDeliveryStatus;
use App\Domain\Reminder\Support\Carbon\CalendarFormatter;
use App\Domain\Todo\Models\Todo;
use Carbon\CarbonInterface;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Queue\Attributes\Backoff;
use Illuminate\Queue\Attributes\DeleteWhenMissingModels;
use Illuminate\Queue\Attributes\Timeout;
use Illuminate\Queue\Attributes\Tries;
use NotificationChannels\Fcm\FcmChannel;
use NotificationChannels\Fcm\FcmMessage;
use NotificationChannels\Fcm\Resources\Notification as FcmNotification;

#[Tries(2)]
#[Backoff(15)]
#[Timeout(30)]
#[DeleteWhenMissingModels]
class TodoReminder extends Notification implements ShouldQueue
{
    use Queueable;

    const MAX_DELIVERY_ATTEMPTS = 3;

    public function __construct(
        public Todo $model,
        public ?string $occursAt,
        public int $deliveryAttempts = 1
    ) {}

    public function via(User $user): array
    {
        return match ($user->preferences->notificationChannel) {
            NotificationChannel::Push => [FcmChannel::class],
            NotificationChannel::Mail => ['mail']
        };
    }

    public function failed(\Throwable $_): void
    {
        if ($this->occursAt) {
            $this->model->applyException(
                $this->occursAt,
                ['notify_status' => ReminderDeliveryStatus::Failed],
                $this->model->findException($this->occursAt),
            );
        } else {
            $this->model->update([
                'notify_status' => ReminderDeliveryStatus::Failed,
            ]);
        }
    }

    public function toFcm(User $user): FcmMessage
    {
        $scheduledAt = $this->scheduledAt($user->preferences->timezone);

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
        $scheduledAt = $this->scheduledAt($user->preferences->timezone);

        return (new MailMessage)
            ->subject(__('mail.todo_reminder.subject', ['title' => $this->model->title]))
            ->markdown('mail.todo-reminder', ['todo' => $this->model, 'time' => mb_lcfirst(CalendarFormatter::format($scheduledAt))]);
    }

    private function scheduledAt(string $timezone): CarbonInterface
    {
        $scheduledAt = $this->model->scheduled_at;

        if ($this->occursAt) {
            $scheduledAt = $scheduledAt->setDateFrom($this->occursAt);
        }

        return $scheduledAt->timezone($timezone);
    }
}
