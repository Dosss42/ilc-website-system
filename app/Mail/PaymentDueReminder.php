<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use App\Models\Enrollment;

class PaymentDueReminder extends Mailable
{
    use Queueable, SerializesModels;

    public $enrollment;
    public $studentName;
    public $dueDate;
    public $daysUntilDue;
    public $amountDue;
    public $paymentType;

    public function __construct(Enrollment $enrollment, int $daysUntilDue)
    {
        $this->enrollment = $enrollment;
        $this->daysUntilDue = $daysUntilDue;
        $this->studentName = $enrollment->user->name ?? 'Student';
        $this->paymentType = $enrollment->payment_type ?? 'full';
        $this->amountDue = $this->paymentType === 'installment'
            ? $enrollment->remaining_balance
            : ($enrollment->total_fee - ($enrollment->payment_amount ?? 0));
        $this->dueDate = $this->paymentType === 'installment'
            ? $enrollment->next_installment_date
            : $enrollment->payment_due_date;
    }

    public function envelope(): Envelope
    {
        $urgency = $this->daysUntilDue <= 1 ? 'URGENT: ' : '';
        return new Envelope(
            subject: "{$urgency}Payment Due Reminder - IEMELIF Learning Center",
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.payment-due-reminder',
            with: [
                'studentName' => $this->studentName,
                'dueDate' => $this->dueDate?->format('F j, Y'),
                'daysUntilDue' => $this->daysUntilDue,
                'amountDue' => number_format($this->amountDue, 2),
                'paymentType' => $this->paymentType,
                'referenceNumber' => $this->enrollment->reference_number,
                'totalFee' => number_format($this->enrollment->total_fee, 2),
                'amountPaid' => number_format($this->enrollment->payment_amount ?? 0, 2),
                'remainingBalance' => number_format($this->enrollment->remaining_balance ?? 0, 2),
            ]
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
