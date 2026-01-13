<?php

declare(strict_types=1);

namespace App\Domain\Auth\Notifications;

use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class OneTimeLoginCode extends Notification
{
    public function __construct(public string $otp) {}

    /** @return string[] */
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
