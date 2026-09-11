<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ForgotPasswordMail extends Mailable
{
    use Queueable, SerializesModels;

    public User $user;
    public string $resetUrl;

    public function __construct(User $user, string $resetUrl)
    {
        $this->user     = $user;
        $this->resetUrl = $resetUrl;
    }

    public function build(): self
    {
        return $this->subject('Reset Your Password - IEMELIF Learning Center')
            ->view('emails.forgot-password')
            ->with([
                'name'     => $this->user->name,
                'resetUrl' => $this->resetUrl,
            ]);
    }
}
