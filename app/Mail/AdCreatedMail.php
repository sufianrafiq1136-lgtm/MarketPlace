<?php

namespace App\Mail;

use App\Models\Ad;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class AdCreatedMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public Ad $ad;

    public function __construct(Ad $ad)
    {
        $this->ad = $ad;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Your Ad Has Been Created',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.ad-created',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
