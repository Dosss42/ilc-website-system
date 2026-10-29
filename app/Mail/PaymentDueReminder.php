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

    /**
     * $dueDate / $amountDue: the month coming due and what to have paid by then (that month + anything
     * still overdue) — passed by app:check-payment-due-dates; otherwise worked out from the enrollment.
     */
    public function __construct(Enrollment $enrollment, int $daysUntilDue, $dueDate = null, ?float $amountDue = null)
    {
        $this->enrollment = $enrollment;
        $this->daysUntilDue = $daysUntilDue;
        $this->studentName = $enrollment->user->name ?? 'Student';
        $this->paymentType = $enrollment->payment_type ?? 'full';
        // Installments: the amount due for this month (the same "due now" the cashier and the portal ask
        // for, PaymentService::paymentLimits) — it used to say the whole remaining balance
        $dueNow = $amountDue ?? (\App\Services\PaymentService::paymentLimits($enrollment)['min'] ?? null);
        $this->amountDue = ($amountDue !== null || $this->paymentType === 'installment') && $dueNow
            ? $dueNow
            : ($enrollment->total_fee - ($enrollment->payment_amount ?? 0));
        $this->dueDate = $dueDate ?? ($this->paymentType === 'installment'
            ? $enrollment->next_installment_date
            : $enrollment->payment_due_date);
    }

    public function envelope(): Envelope
    {
        $title = $this->daysUntilDue <= 0 ? 'Payment Due Today' : 'Payment Reminder';
        $on = !$this->dueDate ? '' : ($this->daysUntilDue <= 0 ? ', ' : ': Due ') . $this->dueDate->format('F j, Y');
        return new Envelope(
            subject: "{$title}{$on} - IEMELIF Learning Center",
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
