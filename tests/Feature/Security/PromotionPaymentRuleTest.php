<?php

namespace Tests\Feature\Security;

use App\Models\Enrollment;
use App\Models\Setting;
use App\Models\User;
use App\Services\FeeCalculator;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * A student only moves to the next school year once the old one is fully paid — every
 * grade (Nursery/Kinder skip the grade assessment, not the payment check) and every path:
 * assess (promote or retain), bulk promote, auto-advance, and portal re-enrollment.
 * Plus the payment-plan rule: A/B for all, C for Grade 1-6, D for Nursery/Kinder.
 */
class PromotionPaymentRuleTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();
        Setting::set('current_school_year', '2026-2027');
        Setting::set('enrollment_target_year', '2027-2028');
        Setting::set('enrollment_open', '1', 'boolean');
        $this->admin = User::factory()->admin()->create();
    }

    /** $paid: 'full' (balance 0), 'partial' (downpayment only), 'none' (no plan / fee) */
    private function enrollment(string $grade, string $paid): Enrollment
    {
        $attrs = [
            'grade_level'  => $grade,
            'student_data' => ['first_name' => 'Ana', 'last_name' => 'Cruz', 'grade_level' => $grade, 'student_type' => 'returning'],
        ];
        return match ($paid) {
            'full'    => Enrollment::factory()->fullyPaid()->withApprovedDocuments()->create($attrs),
            'partial' => Enrollment::factory()->withInstallment()->withApprovedDocuments()->create($attrs + [
                'payment_amount' => 3000, 'remaining_balance' => 11504, 'payment_status' => 'partial',
            ]),
            'none'    => Enrollment::factory()->withApprovedDocuments()->create($attrs + [
                'payment_option' => null, 'total_fee' => 0, 'remaining_balance' => 0, 'payment_status' => 'pending',
            ]),
        };
    }

    private function assess(Enrollment $e, string $action = 'promote')
    {
        return $this->actingAs($this->admin, 'web')->postJson("/admin/assessment/{$e->user_id}/promote", [
            'action' => $action, 'from_school_year' => '2026-2027', 'to_school_year' => '2027-2028',
        ]);
    }

    private function hasNextYear(Enrollment $e): bool
    {
        return Enrollment::where('user_id', $e->user_id)->where('school_year', '2027-2028')->exists();
    }

    /** Assessed out of 2026-2027 (the assessment records it; the student then re-enrolls) */
    private function isAssessed(Enrollment $e): bool
    {
        return \App\Models\Promotion::where('student_id', $e->user_id)->where('from_school_year', '2026-2027')->where('status', 'completed')->exists();
    }

    public function test_assess_refuses_promote_and_retain_until_fully_paid(): void
    {
        $owing = $this->enrollment('grade3', 'partial');
        $res = $this->assess($owing)->assertStatus(422)->assertJson(['success' => false]);
        $this->assertStringStartsWith('Unpaid balance of ₱11,504.00 for S.Y. 2026-2027', $res->json('message'));
        $this->assess($owing, 'retain')->assertStatus(422);
        $this->assertFalse($this->hasNextYear($owing));
        $this->assertSame('enrolled', $owing->fresh()->status, 'the old year is left untouched');

        $noFee = $this->enrollment('grade3', 'none');
        $this->assertStringStartsWith('No tuition fee recorded', $this->assess($noFee)->assertStatus(422)->json('message'));

        $this->assertFalse($this->isAssessed($owing));

        $paid = $this->enrollment('grade3', 'full');
        $this->assess($paid)->assertOk()->assertJson(['success' => true]);
        $this->assertTrue($this->isAssessed($paid));
        $this->assertFalse($this->hasNextYear($paid), 'the student re-enrolls themselves');
    }

    public function test_nursery_and_kinder_auto_advance_skips_students_with_a_balance(): void
    {
        $paid  = $this->enrollment('kindergarten', 'full');
        $owing = $this->enrollment('kindergarten', 'partial');
        $noFee = $this->enrollment('kindergarten', 'none');
        $q = ['grade' => 'kindergarten', 'from_school_year' => '2026-2027', 'to_school_year' => '2027-2028'];

        $preview = $this->actingAs($this->admin, 'web')->getJson('/admin/assessment/auto-advance-preview?' . http_build_query($q))->assertOk();
        $this->assertSame(1, $preview->json('count'));
        $this->assertSame([$paid->user_id], array_column($preview->json('students'), 'id'));
        $this->assertSame(2, $preview->json('excluded_count'));
        $this->assertEqualsCanonicalizing([$owing->user_id, $noFee->user_id], array_column($preview->json('excluded'), 'id'));

        $this->actingAs($this->admin, 'web')->postJson('/admin/assessment/auto-advance', $q)
            ->assertOk()->assertJson(['promoted_count' => 1, 'skipped_count' => 2]);
        $this->assertTrue($this->isAssessed($paid));
        $this->assertFalse($this->isAssessed($owing));
        $this->assertFalse($this->isAssessed($noFee));
        // Already advanced: not listed again
        $this->assertSame(0, $this->actingAs($this->admin, 'web')->getJson('/admin/assessment/auto-advance-preview?' . http_build_query($q))->json('count'));
    }

    public function test_bulk_promote_no_longer_accepts_a_downpayment_as_paid(): void
    {
        $owing = $this->enrollment('grade2', 'partial');
        $preview = $this->actingAs($this->admin, 'web')
            ->getJson('/admin/assessment/bulk-preview?grade=grade2&from_school_year=2026-2027')->assertOk();

        $row = collect($preview->json('excluded'))->firstWhere('id', $owing->user_id);
        $this->assertNotNull($row, 'an installment student with only the downpayment paid is skipped');
        $this->assertContains('Unpaid balance of ₱11,504.00 for S.Y. 2026-2027', $row['reasons']);
    }

    public function test_portal_re_enrollment_is_refused_until_the_old_year_is_paid(): void
    {
        $owing = $this->enrollment('nursery', 'partial');
        $this->actingAs($owing->user, 'web')->post('/student/reenroll', ['target_school_year' => '2027-2028'])
            ->assertSessionHas('error', fn ($m) => str_contains($m, 'Unpaid balance of ₱11,504.00'));
        $this->assertFalse($this->hasNextYear($owing));

        // The portal card says re-enrollment opens once the fees are complete, with the balance — not "Open"
        $this->actingAs($owing->user, 'web')->get('/student/portal')->assertOk()
            ->assertSee('Opens once your S.Y. 2026-2027 fees are complete', false)
            ->assertSee('₱11,504.00</strong> left of', false)
            ->assertDontSee('Enrollment for S.Y. 2027-2028 is Open', false);

        $paid = $this->enrollment('nursery', 'full');
        $this->actingAs($paid->user, 'web')->post('/student/reenroll', ['target_school_year' => '2027-2028']);
        $this->assertSame('kindergarten', Enrollment::where('user_id', $paid->user_id)->where('school_year', '2027-2028')->value('grade_level'));
    }

    /** Fees AND all four documents — the same checklist the student sees — before moving up, on every path. */
    public function test_moving_up_needs_all_four_documents_and_the_student_then_sees_the_result(): void
    {
        // Grade 1, fully paid, only 3 of 4 documents approved → the school can't assess yet
        $e = Enrollment::factory()->fullyPaid()->create(['grade_level' => 'grade1',
            'student_data' => ['first_name' => 'Ana', 'last_name' => 'Cruz', 'grade_level' => 'grade1', 'student_type' => 'returning']]);
        foreach (array_slice(\App\Models\StudentDocument::REQUIRED_TYPES, 0, 3) as $t) {
            \App\Models\StudentDocument::create(['user_id' => $e->user_id, 'document_type' => $t, 'file_path' => 'x', 'original_name' => 'x', 'status' => 'approved']);
        }
        $this->assess($e)->assertStatus(422)->assertJson(['message' => "Required documents incomplete (3/4 approved). Approve the student's documents before moving them to the next school year."]);
        $this->assertFalse($this->hasNextYear($e));

        // 4th approved → assessed → the student re-enrolls → the portal says what happened and what's next
        \App\Models\StudentDocument::create(['user_id' => $e->user_id, 'document_type' => 'two_by_two_picture', 'file_path' => 'x', 'original_name' => 'x', 'status' => 'approved']);
        $this->assess($e)->assertOk();
        $this->actingAs($e->user, 'web')->post('/student/reenroll')->assertSessionHas('success');
        $this->actingAs($e->user, 'web')->get('/student/portal')->assertOk()
            ->assertSee('S.Y. 2027-2028 &mdash; Promoted to Grade 2', false)
            ->assertSee('Next: choose a payment plan and pay the first payment', false);

        // Nursery re-enrolls from the portal: also refused without the documents
        $nursery = Enrollment::factory()->fullyPaid()->create(['grade_level' => 'nursery',
            'student_data' => ['first_name' => 'Ben', 'last_name' => 'Reyes', 'grade_level' => 'nursery', 'student_type' => 'returning']]);
        $this->actingAs($nursery->user, 'web')->post('/student/reenroll', ['target_school_year' => '2027-2028'])
            ->assertSessionHas('error', fn ($m) => str_contains($m, '(0/4 approved)'));
        $this->assertFalse($this->hasNextYear($nursery));
    }

    public function test_payment_plans_are_limited_by_grade(): void
    {
        foreach (['nursery', 'kindergarten'] as $g) {
            $this->assertSame(['A', 'B', 'D'], FeeCalculator::PLANS_BY_GRADE[$g]);
        }
        foreach (['grade1', 'grade2', 'grade3', 'grade4', 'grade5', 'grade6'] as $g) {
            $this->assertSame(['A', 'B', 'C'], FeeCalculator::PLANS_BY_GRADE[$g]);
        }

        // Student portal: Kinder can't pick C, Grade 1 can't pick D (rejected before any invoice)
        config(['services.xendit.secret_key' => 'xnd_test']);
        $kinder = Enrollment::factory()->create(['grade_level' => 'kindergarten', 'payment_option' => null, 'total_fee' => 0, 'remaining_balance' => 0]);
        $this->actingAs($kinder->user, 'web')->postJson('/student/payment/xendit-link', [
            'enrollment_id' => $kinder->id, 'amount' => 1, 'payment_method' => 'gcash', 'payment_type' => 'installment', 'payment_option' => 'C',
        ])->assertStatus(422)->assertJson(['message' => 'Option C is for Grade 1-6 only.']);
        $this->assertNull($kinder->fresh()->payment_option);

        // Admin approval: an invalid plan on the form isn't priced
        $pending = Enrollment::factory()->create([
            'status' => 'pending', 'grade_level' => 'grade1', 'payment_option' => null, 'total_fee' => 0, 'remaining_balance' => 0,
            'student_data' => ['first_name' => 'Ben', 'last_name' => 'Reyes', 'grade_level' => 'grade1', 'payment_option' => 'D'],
        ]);
        \Illuminate\Support\Facades\Mail::fake();
        $this->actingAs($this->admin, 'web')->postJson("/admin/enrollments/{$pending->id}/approve");
        $this->assertEquals(0, (float) $pending->fresh()->total_fee);

        // Cashier: Option D isn't offered for Grade 1
        $cashier = User::factory()->cashier()->create();
        $options = $this->actingAs($cashier, 'cashier')->getJson('/cashier/payment-options?grade=grade1')->assertOk()->json();
        $this->assertEqualsCanonicalizing(['A', 'B', 'C'], array_keys($options));
    }
}
