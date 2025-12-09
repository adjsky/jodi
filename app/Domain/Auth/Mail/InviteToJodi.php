<?php

declare(strict_types=1);

namespace App\Domain\Auth\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class InviteToJodi extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public string $inviter, public string $url) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: __('You were invited to Jodi'),
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'mail.invite-to-jodi',
            with: [
                'email' => $this->inviter,
                'url' => $this->url,
            ]
        );
    }

    /**  @return array<int, \Illuminate\Mail\Mailables\Attachment>*/
    public function attachments(): array
    {
        return [];
    }
}
