<?php

namespace App\Mail;

use App\Models\Backup;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class BackupCompletedMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Backup $backup,
        public bool   $success = true,
        public string $errorMessage = ''
    ) {}

    public function envelope(): Envelope
    {
        $subject = $this->success
            ? '[KusinaOMS] Database Backup Completed Successfully'
            : '[KusinaOMS] Database Backup FAILED';

        return new Envelope(subject: $subject);
    }

    public function content(): Content
    {
        return new Content(view: 'emails.backup-completed');
    }
}