<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use App\Models\User;

class TeacherAccountCreated extends Mailable
{
    use Queueable, SerializesModels;

    public $teacher;
    public $password;

    public function __construct(User $teacher, string $password)
    {
        $this->teacher = $teacher;
        $this->password = $password;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Your Teacher Account - IEMELIF Learning Center',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.teacher-account-created',
            with: [
                'teacherName' => $this->teacher->name,
                'teacherEmail' => $this->teacher->email,
                'password' => $this->password,
                'loginUrl' => route('login'),
            ]
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
