<?php

namespace Tests\Feature\Payments;

use App\Models\Enrollment;
use App\Models\PaymentTransaction;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class XenditPaymentTest extends TestCase
{
    use RefreshDatabase;

    public function test_cashier_can_generate_a_xendit_payment_link(): void
    {
        $cashier = User::factory()->cashier()->create();
        $enrollment = Enrollment::factory()->create();

        Http::fake([
            'api.xendit.co/v2/invoices' => Http::response([
                'id' => 'inv-test-123',
                'invoice_url' => 'https://checkout.xendit.co/web/inv-test-123',
                'expiry_date' => now()->addDay()->toIso8601String(),
            ], 200),
        ]);

        $response = $this->actingAs($cashier, 'cashier')
            ->postJson('/cashier/payment/xendit-link', [
                'enrollment_id' => $enrollment->id,
                'amount' => 14504, // pay-in-full plan: the whole balance is due
                'payment_type' => 'Full Payment',
                'payment_method' => 'gcash',
                'student_name' => 'Juan Cruz',
            ]);

        $response->assertOk();
        $response->assertJson(['success' => true, 'invoice_id' => 'inv-test-123']);

        $this->assertDatabaseHas('payment_transactions', [
            'enrollment_id' => $enrollment->id,
            'xendit_invoice_id' => 'inv-test-123',
            'status' => 'pending',
        ]);
    }

    /** An installment plan student: ₱4,083.33 down payment paid, then 5 months of ₱2,083.33 (balance ₱10,416.67). */
    private function installmentEnrollment(): Enrollment
    {
        $enrollment = Enrollment::factory()->create([
            'payment_type' => 'installment', 'payment_option' => 'B', 'total_fee' => 14500,
            'downpayment_amount' => 4083.33, 'monthly_amount' => 2083.33, 'payment_amount' => 4083.33, 'remaining_balance' => 10416.67,
        ]);
        foreach (['July', 'August', 'September', 'October', 'November'] as $k => $month) {
            \App\Models\PaymentInstallment::create(['enrollment_id' => $enrollment->id, 'user_id' => $enrollment->user_id, 'month_name' => $month,
                'due_date' => now()->addMonths($k + 1)->toDateString(), 'amount' => 2083.33, 'late_fee' => 0, 'status' => 'pending']);
        }
        return $enrollment;
    }

    /** Installments have centavos (₱2,083.33): Xendit is charged that exact amount, and credited exactly that. */
    public function test_student_link_charges_the_exact_amount_with_centavos(): void
    {
        $enrollment = $this->installmentEnrollment();
        Http::fake(['api.xendit.co/v2/invoices' => Http::response(['id' => 'inv-stu-1', 'invoice_url' => 'https://checkout.xendit.co/web/inv-stu-1'], 200)]);
        $payload = ['enrollment_id' => $enrollment->id, 'amount' => 2083.33, 'payment_method' => 'gcash', 'payment_type' => 'Monthly'];
        $student = User::find($enrollment->user_id);

        $this->actingAs($student, 'web')->postJson('/student/payment/xendit-link', $payload)->assertOk()->assertJson(['invoice_id' => 'inv-stu-1', 'amount' => 2083.33]);
        Http::assertSent(fn ($req) => $req['amount'] == 2083.33);
        $this->assertEquals(2083.33, PaymentTransaction::where('xendit_invoice_id', 'inv-stu-1')->value('amount'), 'credited = charged');

        // Asking again while that link is still open returns the same link — no second payable invoice
        $this->actingAs($student, 'web')->postJson('/student/payment/xendit-link', $payload)->assertOk()->assertJson(['invoice_id' => 'inv-stu-1', 'reused' => true]);
        Http::assertSentCount(1);
        $this->assertSame(1, PaymentTransaction::where('enrollment_id', $enrollment->id)->count());
    }

    /** Student Portal = Cashier rule: the same accepted amounts — paying ahead online works, odd amounts never charged. */
    public function test_student_portal_uses_the_cashier_amounts(): void
    {
        $enrollment = $this->installmentEnrollment(); // due now ₱2,083.33, + 1 month ₱4,166.66
        $student = User::find($enrollment->user_id);
        Http::fake(['api.xendit.co/v2/invoices' => Http::sequence()
            ->push(['id' => 'inv-adv', 'invoice_url' => 'https://checkout.xendit.co/web/inv-adv'], 200)
            ->push(['id' => 'inv-odd', 'invoice_url' => 'https://checkout.xendit.co/web/inv-odd'], 200),
            'api.xendit.co/invoices/*' => Http::response(['status' => 'EXPIRED'], 200)]);
        $pay = fn ($amount) => $this->actingAs($student, 'web')->postJson('/student/payment/xendit-link', [
            'enrollment_id' => $enrollment->id, 'amount' => $amount, 'payment_method' => 'gcash', 'payment_type' => 'Installment',
        ]);

        // + 1 month in advance — one of the cashier's amounts, so it is charged as asked
        $pay(4166.66)->assertOk()->assertJson(['invoice_id' => 'inv-adv', 'amount' => 4166.66]);

        // An odd amount (₱2,500) is never charged — "due now" is used instead
        $pay(2500)->assertOk()->assertJson(['invoice_id' => 'inv-odd', 'amount' => 2083.33]);
        $this->assertEquals(2083.33, PaymentTransaction::where('xendit_invoice_id', 'inv-odd')->value('amount'));
    }

    /** One open link per student: a link for a different amount cancels the old one at Xendit first. */
    public function test_a_new_amount_cancels_the_open_link_so_both_cannot_be_paid(): void
    {
        $cashier = User::factory()->cashier()->create();
        $enrollment = $this->installmentEnrollment();
        Http::fake(['api.xendit.co/v2/invoices' => Http::sequence()
            ->push(['id' => 'inv-due', 'invoice_url' => 'https://checkout.xendit.co/web/inv-due'], 200)
            ->push(['id' => 'inv-full', 'invoice_url' => 'https://checkout.xendit.co/web/inv-full'], 200),
            'api.xendit.co/invoices/inv-due/expire!' => Http::response(['status' => 'EXPIRED'], 200)]);
        $link = fn ($amount) => $this->actingAs($cashier, 'cashier')->postJson('/cashier/payment/xendit-link', [
            'enrollment_id' => $enrollment->id, 'amount' => $amount, 'payment_type' => 'Monthly', 'payment_method' => 'gcash', 'student_name' => 'Juan Cruz',
        ]);

        $link(2083.33)->assertOk()->assertJson(['invoice_id' => 'inv-due']);
        $link(2083.33)->assertOk()->assertJson(['invoice_id' => 'inv-due', 'reused' => true, 'amount' => 2083.33]); // same amount → same link
        $link(10416.67)->assertOk()->assertJson(['invoice_id' => 'inv-full', 'amount' => 10416.67]);                // full balance → new link

        Http::assertSent(fn ($req) => str_ends_with($req->url(), '/invoices/inv-due/expire!'));
        $this->assertDatabaseHas('payment_transactions', ['xendit_invoice_id' => 'inv-due', 'status' => 'expired']);
        $this->assertSame(1, PaymentTransaction::where('enrollment_id', $enrollment->id)->where('status', 'pending')->count(), 'only one open link');
    }

    /** If the open link can't be cancelled (e.g. it was just paid), no second link is made. */
    public function test_no_new_link_when_the_open_one_cannot_be_cancelled(): void
    {
        $cashier = User::factory()->cashier()->create();
        $enrollment = $this->installmentEnrollment();
        Http::fake(['api.xendit.co/v2/invoices' => Http::response(['id' => 'inv-due2', 'invoice_url' => 'https://checkout.xendit.co/web/inv-due2'], 200),
            'api.xendit.co/invoices/*' => Http::response(['error_code' => 'INVOICE_NOT_FOUND_ERROR'], 404)]);
        $link = fn ($amount) => $this->actingAs($cashier, 'cashier')->postJson('/cashier/payment/xendit-link', [
            'enrollment_id' => $enrollment->id, 'amount' => $amount, 'payment_type' => 'Monthly', 'payment_method' => 'gcash', 'student_name' => 'Juan Cruz',
        ]);

        $link(2083.33)->assertOk();
        $link(10416.67)->assertStatus(422);
        $this->assertDatabaseHas('payment_transactions', ['xendit_invoice_id' => 'inv-due2', 'status' => 'pending']);
        $this->assertSame(1, PaymentTransaction::where('enrollment_id', $enrollment->id)->count());
    }

    /** A ₱500 late fee is owed on top of tuition: it raises the balance, and paying it doesn't eat into tuition. */
    public function test_late_fee_is_added_to_the_balance_and_paid_on_top_of_tuition(): void
    {
        $cashier = User::factory()->cashier()->create();
        $enrollment = $this->installmentEnrollment(); // balance ₱10,416.67, July ₱2,083.33
        $july = $enrollment->paymentInstallments()->orderBy('due_date')->first();
        $july->update(['status' => 'overdue', 'due_date' => now()->subWeeks(4)]);

        \App\Services\PaymentService::applyLateFee($july->fresh());
        $this->assertEquals(10916.67, (float) $enrollment->fresh()->remaining_balance, 'the late fee is added to what is owed');
        $this->assertEquals(2583.33, \App\Services\PaymentService::paymentLimits($enrollment->fresh())['min'], 'due now = month + late fee');

        $this->actingAs($cashier, 'cashier')->postJson('/cashier/payment/cash', [
            'enrollment_id' => $enrollment->id, 'amount' => 2583.33, 'payment_type' => 'Monthly',
        ])->assertOk();
        $enrollment->refresh();
        $this->assertEquals('paid', $july->fresh()->status);
        // ₱10,416.67 tuition − ₱2,083.33 for July = ₱8,333.34: the ₱500 paid the late fee, not tuition
        // (before the fix the balance came out ₱500 lower, ₱7,833.34)
        $this->assertEquals(8333.34, (float) $enrollment->remaining_balance, 'no ₱500 taken off tuition');
        $this->assertSame('partial', $enrollment->payment_status);

        // Waiving a late fee takes it off again
        $august = $enrollment->paymentInstallments()->whereIn('status', ['pending', 'overdue'])->orderBy('due_date')->first();
        $before = (float) $enrollment->fresh()->remaining_balance;
        \App\Services\PaymentService::applyLateFee($august);
        \App\Services\PaymentService::waiveLateFee($august->fresh());
        $this->assertEquals($before, (float) $enrollment->fresh()->remaining_balance);
    }

    /** If Xendit refuses centavos, it retries once in whole pesos (rounded up) and credits THAT amount. */
    public function test_link_falls_back_to_whole_pesos_if_xendit_refuses_centavos(): void
    {
        $enrollment = $this->installmentEnrollment();
        Http::fake(['api.xendit.co/v2/invoices' => Http::sequence()
            ->push(['error_code' => 'API_VALIDATION_ERROR', 'message' => 'amount must be an integer'], 400)
            ->push(['id' => 'inv-stu-2', 'invoice_url' => 'https://checkout.xendit.co/web/inv-stu-2'], 200)]);

        $this->actingAs(User::find($enrollment->user_id), 'web')->postJson('/student/payment/xendit-link', [
            'enrollment_id' => $enrollment->id, 'amount' => 2083.33, 'payment_method' => 'gcash', 'payment_type' => 'Monthly',
        ])->assertOk()->assertJson(['invoice_id' => 'inv-stu-2', 'amount' => 2084]);
        $this->assertEquals(2084, PaymentTransaction::where('xendit_invoice_id', 'inv-stu-2')->value('amount'));
    }

    /** Cashier: only exact amounts — what's due now, that plus whole months in advance, or the full balance. */
    public function test_cashier_accepts_only_whole_months_due_now_in_advance_or_the_full_balance(): void
    {
        $cashier = User::factory()->cashier()->create();
        $enrollment = $this->installmentEnrollment(); // due now ₱2,083.33 · balance ₱10,416.67
        Http::fake(['api.xendit.co/v2/invoices' => Http::response(['id' => 'inv-csh-1', 'invoice_url' => 'https://checkout.xendit.co/web/inv-csh-1'], 200)]);
        $link = fn ($amount) => $this->actingAs($cashier, 'cashier')->postJson('/cashier/payment/xendit-link', [
            'enrollment_id' => $enrollment->id, 'amount' => $amount, 'payment_type' => 'Monthly', 'payment_method' => 'gcash', 'student_name' => 'Juan Cruz',
        ]);
        $cash = fn ($amount) => $this->actingAs($cashier, 'cashier')->postJson('/cashier/payment/cash', [
            'enrollment_id' => $enrollment->id, 'amount' => $amount, 'payment_type' => 'Monthly',
        ]);

        // The accepted amounts
        $allowed = array_column(\App\Services\PaymentService::paymentLimits($enrollment)['allowed'], 'amount');
        // (+4 months would be ₱10,416.65 — 2 centavos short of the balance, so only the full balance is offered)
        $this->assertEquals([2083.33, 4166.66, 6249.99, 8333.32, 10416.67], $allowed);

        $link(2000)->assertStatus(422);       // short of the month
        $cash(2500)->assertStatus(422)        // a month plus an odd ₱416.67 — would shift every later month
            ->assertJsonFragment(['success' => false]);
        $cash(5000)->assertStatus(422);       // not whole months
        $link(10416.68)->assertStatus(422);   // over the balance
        Http::assertNothingSent();
        $this->assertSame(0, PaymentTransaction::where('enrollment_id', $enrollment->id)->count());

        $link(2083.33)->assertOk();           // exactly the month due
        Http::assertSent(fn ($req) => $req['amount'] == 2083.33);
        $this->assertEquals(2083.33, PaymentTransaction::where('xendit_invoice_id', 'inv-csh-1')->value('amount'));

        $cash(4166.66)->assertOk();           // this month + 1 month in advance
        $enrollment->refresh();
        $this->assertEquals(8249.99, (float) $enrollment->payment_amount);
        $this->assertSame(2, $enrollment->paymentInstallments()->where('status', 'paid')->count(), 'the two whole months are marked paid');
    }

    /** Pay-in-full plan (A): the right amount is the whole remaining balance. */
    public function test_cashier_full_plan_must_pay_the_whole_balance(): void
    {
        $cashier = User::factory()->cashier()->create();
        $enrollment = Enrollment::factory()->create(['payment_option' => 'A', 'payment_type' => 'full', 'total_fee' => 14504, 'payment_amount' => 0, 'remaining_balance' => 14504]);
        $cash = fn ($amount) => $this->actingAs($cashier, 'cashier')->postJson('/cashier/payment/cash', [
            'enrollment_id' => $enrollment->id, 'amount' => $amount, 'payment_type' => 'Full Payment',
        ]);
        $cash(5000)->assertStatus(422);
        $cash(14504)->assertOk();
        $this->assertSame('paid', $enrollment->fresh()->payment_status);
    }

    /** A "paid" notice for a different amount (or someone else's reference) is never credited. */
    public function test_webhook_does_not_credit_an_invoice_that_does_not_match(): void
    {
        $enrollment = Enrollment::factory()->create(['total_fee' => 14504, 'payment_amount' => 0, 'remaining_balance' => 14504]);
        PaymentTransaction::create([
            'enrollment_id' => $enrollment->id, 'user_id' => $enrollment->user_id, 'payment_type' => 'online', 'payment_method' => 'gcash',
            'amount' => 14504, 'reference_number' => 'REF-MATCH', 'xendit_invoice_id' => 'inv-mismatch', 'status' => 'pending',
        ]);
        $send = fn (array $extra) => $this->postJson('/cashier/webhook/xendit', ['id' => 'inv-mismatch', 'status' => 'PAID'] + $extra,
            ['x-callback-token' => config('services.xendit.webhook_token')])->assertOk();

        $send(['external_id' => 'REF-MATCH', 'paid_amount' => 100, 'currency' => 'PHP']);      // wrong amount
        $send(['external_id' => 'SOMEONE-ELSE', 'paid_amount' => 14504, 'currency' => 'PHP']);  // wrong reference
        $send(['external_id' => 'REF-MATCH', 'paid_amount' => 14504, 'currency' => 'USD']);     // wrong currency
        $send(['external_id' => 'REF-MATCH']);                                                  // no amount at all
        $this->assertDatabaseHas('payment_transactions', ['xendit_invoice_id' => 'inv-mismatch', 'status' => 'pending']);
        $this->assertEquals(0, (float) $enrollment->fresh()->payment_amount);
        $this->assertTrue(\App\Models\ActivityLog::where('event_type', 'error')->where('description', 'like', '%NOT credited%')->exists(), 'logged for Admin');

        $send(['external_id' => 'REF-MATCH', 'paid_amount' => 14504, 'currency' => 'PHP']);    // the real one
        $this->assertEquals(14504, (float) $enrollment->fresh()->payment_amount);
    }

    public function test_webhook_completes_a_pending_xendit_payment(): void
    {
        $enrollment = Enrollment::factory()->create([
            'total_fee' => 14504, 'payment_amount' => 0, 'remaining_balance' => 14504,
        ]);
        $transaction = PaymentTransaction::create([
            'enrollment_id' => $enrollment->id, 'user_id' => $enrollment->user_id,
            'payment_type' => 'online', 'payment_method' => 'gcash', 'amount' => 5000,
            'reference_number' => 'TEST-REF', 'xendit_invoice_id' => 'inv-webhook-test',
            'status' => 'pending',
        ]);

        $response = $this->postJson('/cashier/webhook/xendit', [
            'id' => 'inv-webhook-test',
            'external_id' => 'TEST-REF', // what Xendit sends back: our reference and the amount paid
            'status' => 'PAID',
            'paid_amount' => 5000,
            'currency' => 'PHP',
        ], [
            'x-callback-token' => config('services.xendit.webhook_token'),
        ]);

        $response->assertOk();

        $transaction->refresh();
        $this->assertEquals('completed', $transaction->status);

        $enrollment->refresh();
        $this->assertEquals(5000, $enrollment->payment_amount);
        $this->assertEquals(9504, $enrollment->remaining_balance);
    }

    public function test_webhook_rejects_requests_with_wrong_signature(): void
    {
        $enrollment = Enrollment::factory()->create();
        PaymentTransaction::create([
            'enrollment_id' => $enrollment->id, 'user_id' => $enrollment->user_id,
            'payment_type' => 'online', 'payment_method' => 'gcash', 'amount' => 5000,
            'reference_number' => 'TEST-REF-2', 'xendit_invoice_id' => 'inv-bad-sig',
            'status' => 'pending',
        ]);

        $response = $this->postJson('/cashier/webhook/xendit', [
            'id' => 'inv-bad-sig', 'status' => 'PAID',
        ], ['x-callback-token' => 'wrong-token']);

        $response->assertStatus(401);
        $this->assertDatabaseHas('payment_transactions', ['xendit_invoice_id' => 'inv-bad-sig', 'status' => 'pending']);
    }

    /** XENDIT_WEBHOOK_TOKEN missing on the server: an empty token must not "match" a request with no token. */
    public function test_webhook_is_refused_when_no_token_is_configured(): void
    {
        config(['services.xendit.webhook_token' => '']);
        $enrollment = Enrollment::factory()->create();
        PaymentTransaction::create([
            'enrollment_id' => $enrollment->id, 'user_id' => $enrollment->user_id,
            'payment_type' => 'online', 'payment_method' => 'gcash', 'amount' => 5000,
            'reference_number' => 'TEST-REF-3', 'xendit_invoice_id' => 'inv-no-token',
            'status' => 'pending',
        ]);

        $this->postJson('/cashier/webhook/xendit', ['id' => 'inv-no-token', 'status' => 'PAID'])->assertStatus(401);
        $this->postJson('/cashier/webhook/xendit', ['id' => 'inv-no-token', 'status' => 'PAID'], ['x-callback-token' => ''])->assertStatus(401);
        $this->assertDatabaseHas('payment_transactions', ['xendit_invoice_id' => 'inv-no-token', 'status' => 'pending']);
    }

    /**
     * This is the core resilience feature: on a local/non-public-URL dev
     * environment, Xendit's webhook can never be delivered — the polling
     * endpoint the browser calls must be able to self-heal by asking
     * Xendit's own API directly whenever our local copy still says pending.
     */
    public function test_status_check_reconciles_against_xendit_when_webhook_never_arrived(): void
    {
        $enrollment = Enrollment::factory()->create([
            'total_fee' => 14504, 'payment_amount' => 0, 'remaining_balance' => 14504,
        ]);
        $student = User::find($enrollment->user_id);
        PaymentTransaction::create([
            'enrollment_id' => $enrollment->id, 'user_id' => $student->id,
            'payment_type' => 'online', 'payment_method' => 'gcash', 'amount' => 5000,
            'reference_number' => 'TEST-REF-3', 'xendit_invoice_id' => 'inv-reconcile-test',
            'status' => 'pending',
        ]);

        // No webhook ever fires. Xendit's own API says PAID when we ask it directly.
        Http::fake([
            'api.xendit.co/*' => Http::response(['id' => 'inv-reconcile-test', 'status' => 'PAID', 'amount' => 5000, 'currency' => 'PHP'], 200),
        ]);

        $response = $this->actingAs($student, 'web')
            ->getJson('/student/payment/xendit-status?invoice_id=inv-reconcile-test');

        $response->assertOk();
        $response->assertJson(['status' => 'completed']);

        $enrollment->refresh();
        $this->assertEquals(5000, $enrollment->payment_amount);
        $this->assertEquals('partial', $enrollment->payment_status);
    }

    public function test_status_check_marks_expired_invoice_as_expired(): void
    {
        $enrollment = Enrollment::factory()->create();
        $student = User::find($enrollment->user_id);
        PaymentTransaction::create([
            'enrollment_id' => $enrollment->id, 'user_id' => $student->id,
            'payment_type' => 'online', 'payment_method' => 'gcash', 'amount' => 5000,
            'reference_number' => 'TEST-REF-4', 'xendit_invoice_id' => 'inv-expired-test',
            'status' => 'pending',
        ]);

        Http::fake([
            'api.xendit.co/*' => Http::response(['id' => 'inv-expired-test', 'status' => 'EXPIRED'], 200),
        ]);

        $this->actingAs($student, 'web')
            ->getJson('/student/payment/xendit-status?invoice_id=inv-expired-test')
            ->assertOk()
            ->assertJson(['status' => 'expired']);

        $this->assertDatabaseHas('payment_transactions', [
            'xendit_invoice_id' => 'inv-expired-test', 'status' => 'expired',
        ]);
    }

    public function test_status_check_does_not_leak_another_students_transaction(): void
    {
        $enrollment = Enrollment::factory()->create();
        $owner = User::find($enrollment->user_id);
        $intruder = User::factory()->student()->create();

        PaymentTransaction::create([
            'enrollment_id' => $enrollment->id, 'user_id' => $owner->id,
            'payment_type' => 'online', 'payment_method' => 'gcash', 'amount' => 5000,
            'reference_number' => 'TEST-REF-5', 'xendit_invoice_id' => 'inv-private-test',
            'status' => 'pending',
        ]);

        $this->actingAs($intruder, 'web')
            ->getJson('/student/payment/xendit-status?invoice_id=inv-private-test')
            ->assertStatus(404);
    }
}
