<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class NewsletterEmail extends Mailable
{
    use Queueable, SerializesModels;

    public $data;

    /**
     * إنشاء كائن البريد مع البيانات.
     */
    public function __construct($data)
    {
        $this->data = $data;
    }

    /**
     * تحديد محتويات البريد.
     */
    public function content(): Content
    {
        return new Content(
            markdown: 'emails.newsletter',
            with: ['data' => $this->data],
        );
    }

    /**
     * عنوان البريد.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: $this->data['subject'], // الموضوع المرسل من API
        );
    }
}
