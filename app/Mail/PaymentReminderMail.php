<?php

namespace App\Mail;

use App\Models\PaymentInstallment;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class PaymentReminderMail extends Mailable
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
        return $this->subject('Overdue Payment Reminder: ' . $this->installment->due_date->format('F Y') . ' Tuition - IEMELIF Learning Center')
            ->view('emails.payment-reminder')
            ->with([
                'installment' => $this->installment,
                'student' => $this->student,
                'enrollment' => $this->enrollment,
                'dueDate' => $this->installment->due_date->format('F d, Y'),
                // the month + its late fee — the late fee is already added by the time this reminder goes out
                'amount' => number_format((float) $this->installment->amount + (float) ($this->installment->late_fee ?? 0), 2),
            ]);
    }
}
