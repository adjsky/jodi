<?php

declare(strict_types=1);

namespace App\Domain\Event\Notifications;

use App\Models\Event;
use App\Models\User;
use Carbon\CarbonInterface;
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
        $navigate = config('app.url').'?d='.$this->event->starts_at->format('Y-m-d');

        return (new DeclarativeWebPushMessage)
            ->title(__(':title is upcoming.', ['title' => $this->event->title]))
            ->body(__('Starts :time.', ['time' => $this->startsIn()]))
            ->data(['navigate' => $navigate])
            ->tag('event-'.$this->event->id.'-reminder')
            ->navigate($navigate);
    }

    public function toMail(): MailMessage
    {
        return (new MailMessage)
            ->subject(__('mail.event_reminder.subject', ['title' => $this->event->title, 'startsIn' => $this->startsIn()]))
            ->markdown('mail.event-reminder', ['event' => $this->event]);
    }

    protected function startsIn(): string
    {
        return $this->event->starts_at->diffForHumans(
            $this->event->notify_at,
            syntax: CarbonInterface::DIFF_RELATIVE_TO_NOW,
            options: CarbonInterface::ONE_DAY_WORDS
        );
    }
}
