<?php

declare(strict_types=1);

namespace App\Domain\Auth\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class InviteToJodi extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public User $inviter, public string $url) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: __('mail.invite_to_jodi.subject', [
                'app' => config('app.name'),
                'email' => $this->inviter->email,
            ]),
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'mail.invite-to-jodi',
            with: [
                'app' => config('app.name'),
                'inviter' => $this->inviter,
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
