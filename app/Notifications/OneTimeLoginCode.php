<?php

declare(strict_types=1);

namespace App\Notifications;

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
            ->subject("{$this->otp} is your one-time login code")
            ->markdown(
                'mail.one-time-login-code',
                ['otp' => $this->otp]
            );
    }
}
