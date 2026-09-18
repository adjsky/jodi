<?php

declare(strict_types=1);

namespace App\Domain\Identity\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class OneTimeLoginCode extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public string $otp) {}

    public function via(): array
    {
        return ['mail'];
    }

    public function toMail(): MailMessage
    {
        return (new MailMessage)
            ->subject(__('mail.otp.subject', ['code' => $this->otp]))
            ->markdown(
                'mail.one-time-login-code',
                ['otp' => $this->otp]
            );
    }
}
