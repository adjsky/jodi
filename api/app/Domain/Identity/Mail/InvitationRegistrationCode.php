<?php

declare(strict_types=1);

namespace App\Domain\Identity\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class InvitationRegistrationCode extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(public string $code) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: __('mail.invitation_registration_code.subject', [
                'code' => $this->code,
            ]),
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'mail.invitation-registration-code',
            with: [
                'code' => $this->code,
            ],
        );
    }

    /** @return array<int, Attachment> */
    public function attachments(): array
    {
        return [];
    }
}
