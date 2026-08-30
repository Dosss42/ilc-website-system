<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use App\Models\Enrollment;

class EnrollmentApproved extends Mailable
{
    use Queueable, SerializesModels;

    public $enrollment;
    public $password;

    /**
     * Create a new message instance.
     */
    public function __construct(Enrollment $enrollment, string $password)
    {
        $this->enrollment = $enrollment;
        $this->password = $password;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Enrollment Approved - IEMELIF Learning Center',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.enrollment-approved',
            with: [
                'studentName' => $this->enrollment->full_name,
                'referenceNumber' => $this->enrollment->reference_number,
                'gradeLevel' => $this->enrollment->grade_level_display,
                'studentEmail' => $this->enrollment->student_data['student_email'],
                'password' => $this->password,
                'loginUrl' => route('login'),
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
