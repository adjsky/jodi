<?php

declare(strict_types=1);

namespace App\Domain\Todo\Notifications;

use App\Models\Todo;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use NotificationChannels\WebPush\DeclarativeWebPushMessage;
use NotificationChannels\WebPush\WebPushChannel;

class TodoReminder extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public Todo $todo) {}

    /** @return array<int, string> */
    public function via(User $user): array
    {
        return $user->preferences['notifications'] == 'push'
            ? [WebPushChannel::class]
            : ['mail'];
    }

    public function toWebPush(): DeclarativeWebPushMessage
    {
        $navigate = config('app.url').'?d='.$this->todo->scheduled_at->format('Y-m-d');

        return (new DeclarativeWebPushMessage)
            ->title(__(':title - time to start.', ['title' => $this->todo->title]))
            ->body(__('Scheduled for :time.', ['time' => $this->startsIn()]))
            ->data(['navigate' => $navigate])
            ->tag('todo-'.$this->todo->id.'-reminder')
            ->navigate($navigate);
    }

    public function toMail(): MailMessage
    {
        return (new MailMessage)
            ->subject(__('mail.todo_reminder.subject', ['title' => $this->todo->title, 'startsIn' => $this->startsIn()]))
            ->markdown('mail.todo-reminder', ['todo' => $this->todo, 'startsIn' => $this->startsIn()]);
    }

    protected function startsIn(): string
    {
        return $this->todo->scheduled_at->fromNow(parts: 2);
    }
}
