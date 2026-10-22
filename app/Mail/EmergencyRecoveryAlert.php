<?php

namespace App\Mail;

use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;

/** Sent to every active Super Admin whenever the Emergency Recovery page is used or tried. */
class EmergencyRecoveryAlert extends Mailable
{
    public function __construct(public string $description, public string $ip)
    {
    }

    public function envelope(): Envelope
    {
        return new Envelope(subject: 'ILC — Emergency Recovery was used');
    }

    public function content(): Content
    {
        return new Content(view: 'emails.emergency-recovery-alert', with: [
            'description' => $this->description,
            'ip'          => $this->ip,
            'when'        => now()->format('M d, Y h:i A'),
        ]);
    }
}
