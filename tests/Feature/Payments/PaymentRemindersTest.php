<?php

namespace Tests\Feature\Payments;

use App\Mail\LateFeeAppliedMail;
use App\Mail\PaymentDueReminder;
use App\Mail\PaymentReminderMail;
use App\Models\Enrollment;
use App\Models\PaymentInstallment;
use App\Services\PaymentService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

/**
 * The late-payment timeline (docs/LATE-PAYMENTS-AND-REMINDERS.md): reminders 3 days before and on the due date,
 * ₱500 late fee the morning after the due date ends unpaid, then a reminder every week; results held at 3 months behind.
 */
class PaymentRemindersTest extends TestCase
{
    use RefreshDatabase;

    private function family(): Enrollment
    {
        $e = Enrollment::factory()->create([
            'payment_type' => 'installment', 'payment_option' => 'B', 'total_fee' => 15000, 'downpayment_amount' => 6000,
            'monthly_amount' => 1000, 'payment_amount' => 6000, 'remaining_balance' => 9000, 'payment_status' => 'partial',
            'next_installment_date' => '2027-07-10',
        ]);
        foreach (PaymentService::MONTHS as $k => $month) {
            PaymentInstallment::create(['enrollment_id' => $e->id, 'user_id' => $e->user_id, 'month_name' => $month,
                'due_date' => Carbon::create(2027, 7, 10)->addMonthsNoOverflow($k)->toDateString(), 'amount' => 1000, 'late_fee' => 0, 'status' => 'pending']);
        }
        return $e;
    }

    private function runDay(string $date): void
    {
        Carbon::setTestNow(Carbon::parse($date . ' 08:00'));
        Artisan::call('app:check-payment-due-dates');
        Carbon::setTestNow(Carbon::parse($date . ' 08:10'));
        Artisan::call('payments:check-overdue');
    }

    protected function tearDown(): void
    {
        Carbon::setTestNow();
        parent::tearDown();
    }

    public function test_reminders_go_out_three_days_before_and_on_the_due_date_once_each(): void
    {
        Mail::fake();
        $e = $this->family();

        $this->runDay('2027-07-07'); // 3 days before July
        $this->runDay('2027-07-08'); // same due date — no second early reminder
        Mail::assertSent(PaymentDueReminder::class, 1);
        Mail::assertSent(PaymentDueReminder::class, fn ($m) => $m->dueDate->isSameDay('2027-07-10') && $m->daysUntilDue === 3 && $m->amountDue == 1000);

        $this->runDay('2027-07-10'); // the due date itself → "due today"
        Mail::assertSent(PaymentDueReminder::class, 2);
        Mail::assertSent(PaymentDueReminder::class, fn ($m) => $m->daysUntilDue === 0);
        Mail::assertNotSent(LateFeeAppliedMail::class); // no fee on the due date itself

        // July paid on time → August gets its own reminders (the flag used to stay "sent" forever)
        $e->paymentInstallments()->where('month_name', 'July')->update(['status' => 'paid', 'amount_paid' => 1000, 'paid_at' => now()]);
        $e->update(['payment_amount' => 7000, 'remaining_balance' => 8000]);
        $this->runDay('2027-08-07');
        Mail::assertSent(PaymentDueReminder::class, 3);
        Mail::assertSent(PaymentDueReminder::class, fn ($m) => $m->dueDate->isSameDay('2027-08-10') && $m->amountDue == 1000);
        Mail::assertNotSent(LateFeeAppliedMail::class);
    }

    public function test_the_late_fee_comes_the_morning_after_the_due_date_then_weekly_reminders(): void
    {
        Mail::fake();
        $e = $this->family(); // July due Jul 10

        $this->runDay('2027-07-10'); // due today — reminder only
        Mail::assertNotSent(LateFeeAppliedMail::class);
        $this->assertEquals(9000, (float) $e->fresh()->remaining_balance);

        $this->runDay('2027-07-11'); // the due date ended unpaid → fee this morning
        Mail::assertSent(LateFeeAppliedMail::class, 1);
        $this->assertEquals(500, (float) $e->paymentInstallments()->where('month_name', 'July')->value('late_fee'));
        $this->assertEquals(9500, (float) $e->fresh()->remaining_balance, 'the fee is added to what is owed');
        Mail::assertNotSent(PaymentReminderMail::class); // no 1-week warning / 2-week grace any more

        $this->runDay('2027-07-17'); // 1 week late → weekly reminder (month + fee)
        Mail::assertSent(PaymentReminderMail::class, 1);
        Mail::assertSent(PaymentReminderMail::class, fn ($m) => $m->build()->viewData['amount'] === '1,500.00');
        $this->runDay('2027-07-17'); // the job running twice in a day doesn't send it twice
        Mail::assertSent(PaymentReminderMail::class, 1);
        $this->runDay('2027-07-24'); // 2 weeks late
        Mail::assertSent(PaymentReminderMail::class, 2);
        Mail::assertSent(LateFeeAppliedMail::class, 1); // one fee per late month — never applied twice

        // Still unpaid on Aug 7: August's reminder asks for July (₱1,000 + ₱500 fee) + August = ₱2,500
        $this->runDay('2027-08-07');
        Mail::assertSent(PaymentDueReminder::class, fn ($m) => $m->dueDate->isSameDay('2027-08-10') && $m->amountDue == 2500);
        $this->runDay('2027-08-11'); // August's due date ended unpaid → its own fee
        Mail::assertSent(LateFeeAppliedMail::class, 2);
        $this->assertEquals(10000, (float) $e->fresh()->remaining_balance);
    }

    /** The payment that settles a late month says so in every portal: the month is linked and the fee is in the description. */
    public function test_paying_a_late_month_records_the_late_fee_on_the_payment(): void
    {
        Mail::fake();
        $e = $this->family();
        $this->runDay('2027-07-11'); // July late → ₱500 fee

        $this->payAtCashier($e, PaymentService::paymentLimits($e->fresh())['allowed'][0]['amount']); // ₱1,500
        $tx = \App\Models\PaymentTransaction::where('enrollment_id', $e->id)->latest('id')->first();
        $july = $e->paymentInstallments()->where('month_name', 'July')->first();

        $this->assertSame('paid', $july->status);
        $this->assertSame($tx->id, (int) $july->payment_transaction_id, 'the month knows which payment paid it');
        $this->assertSame('cash', $july->payment_method);
        $this->assertStringContainsString('July (incl. ₱500.00 late fee)', $tx->description);
        $this->assertEquals(500, $tx->fresh()->lateFeePortion());
    }

    private function note(Enrollment $e, string $promise): \App\Models\PromissoryNote
    {
        return \App\Models\PromissoryNote::create(['reference_number' => \App\Models\PromissoryNote::generateReference(), 'enrollment_id' => $e->id,
            'student_id' => $e->user_id, 'created_by' => \App\Models\User::factory()->create(['role' => 'finance'])->id, 'amount_overdue' => 4500,
            'amount_promised' => 4500, 'promise_date' => $promise, 'date_issued' => now()->toDateString(), 'parent_guardian' => 'Parent', 'status' => 'pending']);
    }

    private function payAtCashier(Enrollment $e, float $amount): void
    {
        \App\Models\PaymentTransaction::where('enrollment_id', $e->id)->update(['created_at' => now()->subMinute()]);
        $this->actingAs(\App\Models\User::factory()->cashier()->create(), 'cashier')
            ->postJson('/cashier/payment/cash', ['enrollment_id' => $e->id, 'amount' => $amount, 'payment_type' => 'Overdue'])->assertOk();
    }

    /** Term 1 unpaid → held → Promissory Note → paid in full: the note is kept automatically and the permit stays clear. */
    public function test_paying_everything_fulfils_the_promissory_note_and_clears_the_exam_permit(): void
    {
        Mail::fake();
        $e = $this->family();
        $this->runDay('2027-10-01'); // July, Aug, Sep unpaid → 3 fees, 3 months behind
        $this->assertEquals(1500, (float) $e->paymentInstallments()->sum('late_fee'));
        $permit = fn () => PaymentService::getExamPermitStatus($e->fresh(['paymentInstallments', 'promissoryNotes']));
        $this->assertTrue($permit()['held']);

        $note = $this->note($e, '2027-10-15');
        $this->assertFalse($permit()['held'], 'a Promissory Note in good standing lifts the hold');

        $options = PaymentService::paymentLimits($e->fresh())['allowed'];
        $this->assertSame('Overdue July – September', $options[2]['label']);
        $this->payAtCashier($e, $options[2]['amount']); // ₱4,500 = 3 months + 3 fees
        $this->assertSame('fulfilled', $note->fresh()->status, 'kept automatically — Finance no longer has to remember');

        Carbon::setTestNow('2027-10-20'); // after the promised date: before the fix, the still-"pending" note held the permit
        $this->assertFalse($permit()['held']);
    }

    /** A broken promise holds the permit; a partial payment doesn't lift it; catching up does. */
    public function test_a_broken_promise_lifts_only_when_the_family_catches_up(): void
    {
        Mail::fake();
        $e = $this->family();
        $this->runDay('2027-09-11');
        $note = $this->note($e, '2027-09-30');
        $this->runDay('2027-10-02');
        $permit = fn () => PaymentService::getExamPermitStatus($e->fresh(['paymentInstallments', 'promissoryNotes']));
        $this->assertSame('broken_promise', $permit()['reason']);

        $this->payAtCashier($e, PaymentService::paymentLimits($e->fresh())['allowed'][0]['amount']); // July only
        $this->assertTrue($permit()['held'], 'still behind — still held');
        $this->assertSame('pending', $note->fresh()->status);

        $this->payAtCashier($e, PaymentService::paymentLimits($e->fresh())['allowed'][1]['amount']); // August + September
        $this->assertSame('fulfilled', $note->fresh()->status);
        $this->assertFalse($permit()['held']);
    }

    /** RA 11984: the student always takes the exam; 3 months behind → the RESULTS are held in the portal, then released. */
    public function test_results_are_held_in_the_portal_not_the_exam_and_released_after_catching_up(): void
    {
        Mail::fake();
        $e = $this->family(); // Grade 1
        $subject = \App\Models\Subject::create(['name' => 'Mathematics', 'code' => 'MATH1', 'grade_level' => 'grade1', 'is_active' => true]);
        \App\Models\Grade::create(['student_id' => $e->user_id, 'subject_id' => $subject->id, 'term' => 1, 'grade' => 88,
            'teacher_id' => \App\Models\User::factory()->teacher()->create()->id,
            'status' => 'approved', 'school_year' => $e->school_year]);
        $student = \App\Models\User::find($e->user_id);
        $grades = fn () => $this->actingAs($student, 'web')->getJson('/api/student/grades?quarter=1&school_year=' . $e->school_year)->assertOk();

        $this->runDay('2027-09-11'); // 3 months behind
        $held = $grades();
        $this->assertNotNull($held->json('data.results_hold'));
        $this->assertNull($held->json('data.subjects.0.final_grade'), 'the grade is not sent while on hold');
        $this->assertSame('Mathematics', $held->json('data.subjects.0.name'), 'the subjects are still listed');

        // Catching up releases the results
        $this->payAtCashier($e, PaymentService::paymentLimits($e->fresh())['allowed'][2]['amount']);
        $released = $grades();
        $this->assertNull($released->json('data.results_hold'));
        $this->assertEquals(88, $released->json('data.subjects.0.final_grade'));
    }

    public function test_exam_permit_is_held_only_at_three_months_behind(): void
    {
        Mail::fake();
        $e = $this->family();
        foreach (['2027-08-31', '2027-09-09'] as $day) { // July and August past due = 2 months behind
            $this->runDay($day);
            $this->assertFalse(PaymentService::getExamPermitStatus($e->fresh(['paymentInstallments', 'promissoryNotes']))['held'], $day);
        }
        $this->runDay('2027-09-11'); // September's due date (Sep 10) passed too — 3 months behind
        $status = PaymentService::getExamPermitStatus($e->fresh(['paymentInstallments', 'promissoryNotes']));
        $this->assertTrue($status['held']);
        $this->assertSame(3, $status['overdue_months']);
    }
}
