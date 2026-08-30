<?php

namespace App\Mail;

use App\Models\PaymentInstallment;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class PortalBlockedMail extends Mailable
{
    use Queueable, SerializesModels;

    public $installment;
    public $student;
    public $enrollment;

    /**
     * Create a new message instance.
     */
    public function __construct(PaymentInstallment $installment)
    {
        $this->installment = $installment;
        $this->student = $installment->user;
        $this->enrollment = $installment->enrollment;
    }

    /**
     * Build the message.
     */
    public function build(): self
    {
        return $this->subject('URGENT: Portal Access Blocked - Payment Required')
            ->view('emails.portal-blocked')
            ->with([
                'installment' => $this->installment,
                'student' => $this->student,
                'enrollment' => $this->enrollment,
                'totalDue' => number_format($this->installment->amount + $this->installment->late_fee, 2),
                'dueDate' => $this->installment->due_date->format('F d, Y'),
            ]);
    }
}
