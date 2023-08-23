<?php

declare(strict_types=1);

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class PostmanBackupMail extends Mailable
{
    use Queueable;
    use SerializesModels;

    // @phpstan-ignore-next-line
    public function __construct(
        protected array $fileNames,
    ) {
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(subject: 'PXM Postman backup');
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(markdown: 'emails.postman_backup');
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, Attachment>
     */
    public function attachments(): array
    {
        $attachments = [];

        foreach ($this->fileNames as $fileName) {
            $attachments[] = Attachment::fromStorage($fileName)
                ->withMime('application/json');
        }

        return $attachments;
    }
}
