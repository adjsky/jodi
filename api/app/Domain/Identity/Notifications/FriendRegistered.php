<?php

declare(strict_types=1);

namespace App\Domain\Identity\Notifications;

use App\Domain\Identity\Enums\NotificationChannel;
use App\Domain\Identity\Models\User;
use App\Support\Firebase\Concerns\RetriesFcmDeliveryFailure;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use NotificationChannels\Fcm\FcmChannel;
use NotificationChannels\Fcm\FcmMessage;
use NotificationChannels\Fcm\Resources\Notification as FcmNotification;

class FriendRegistered extends Notification implements ShouldQueue
{
    use Queueable, RetriesFcmDeliveryFailure;

    public function __construct(
        public User $friend,
    ) {}

    public function via(User $user): array
    {
        return match ($user->preferences->notificationChannel) {
            NotificationChannel::Push => [FcmChannel::class],
            NotificationChannel::Mail => ['mail'],
        };
    }

    public function toFcm(): FcmMessage
    {
        return new FcmMessage(notification: new FcmNotification(
            title: __('notifications.friend_registered.title'),
            body: __('notifications.friend_registered.body', [
                'name' => $this->friend->name,
            ]),
        ))->custom(['android' => ['priority' => 'high']]);
    }

    public function toMail(): MailMessage
    {
        return new MailMessage()
            ->subject(__('mail.friend_registered.subject'))
            ->markdown(
                'mail.friend-registered',
                ['name' => $this->friend->name],
            );
    }
}
