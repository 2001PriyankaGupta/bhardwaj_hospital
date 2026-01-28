<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class NotificationMail extends Mailable
{
    use Queueable, SerializesModels;

    public $subject;
    public $emailData;

    public function __construct($subject, $emailData)
    {
        $this->subject = $subject;
        $this->emailData = $emailData;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: $this->subject,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'admin.notification.mailtemplate',
            with: [
                'content' => $this->emailData['content'],
                'template' => $this->emailData['template'],
                'scheduled_at' => $this->emailData['scheduled_at'],
                'variables' => $this->emailData['variables']
            ]
        );
    }

    public function attachments(): array
    {
        return [];
    }
}