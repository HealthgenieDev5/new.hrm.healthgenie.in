<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class NoAbsenteesMail extends Mailable
{
    use Queueable, SerializesModels;

    public $mailData;

    public function __construct($mailData)
    {
        $this->mailData = $mailData;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: '🎉 Great News – Zero Long-Term Absentees Today!',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'mail.NoAbsentees',
            with: [
                'from_date'     => $this->mailData['from_date'],
                'to_date'       => $this->mailData['to_date'],
                'total_active'  => $this->mailData['total_active'],
            ],
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
