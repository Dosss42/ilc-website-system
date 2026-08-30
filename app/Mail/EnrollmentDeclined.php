<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use App\Models\Enrollment;

class EnrollmentDeclined extends Mailable
{
    use Queueable, SerializesModels;

    public $enrollment;
    public $reason;

    /**
     * Create a new message instance.
     */
    public function __construct(Enrollment $enrollment, string $reason)
    {
        $this->enrollment = $enrollment;
        $this->reason = $reason;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Enrollment Update - IEMELIF Learning Center',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.enrollment-declined',
            with: [
                'studentName' => $this->enrollment->full_name,
                'referenceNumber' => $this->enrollment->reference_number,
                'gradeLevel' => $this->enrollment->grade_level_display,
                'reason' => $this->reason,
                'contactEmail' => 'info@iemelif-ilc.edu.ph',
                'contactPhone' => '(046) 000-0000',
            ]
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
