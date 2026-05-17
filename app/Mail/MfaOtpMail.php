<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class MfaOtpMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public string $code,
        public string $userName
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(subject: 'Your KusinaOMS Login Verification Code');
    }

    public function content(): Content
    {
        return new Content(view: 'emails.mfa-otp');
    }
}