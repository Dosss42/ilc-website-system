<?php

namespace Tests\Feature\Security;

use App\Models\Enrollment;
use App\Models\PaymentTransaction;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Regression tests for two real, severe access-control bugs found during
 * Phase 7 manual QA — both let any logged-in student reach staff-only
 * financial/academic actions because a route group was missing its role
 * middleware (not a missing feature, an actual live security hole):
 *
 *  1. /admin/payments/{id}/approve (and reject/destroy) and
 *     /admin/enrollments/{id}/payment only checked `auth` (any signed-in
 *     web-guard user), not `admin` — confirmed exploitable: a student
 *     could approve their own unpaid transaction, marking it "completed".
 *  2. The entire /teacher/* route group (grades, attendance, etc.) only
 *     checked `auth` + `maintenance` — TeacherMiddleware existed, was
 *     correctly registered, and was simply never applied to the group.
 */
class AccessControlTest extends TestCase
{
    use RefreshDatabase;

    public function test_a_student_cannot_approve_a_payment_transaction(): void
    {
        $student = User::factory()->student()->create();
        $enrollment = Enrollment::factory()->create(['user_id' => $student->id]);
        $tx = PaymentTransaction::create([
            'enrollment_id' => $enrollment->id, 'user_id' => $student->id,
            'payment_type' => 'walkin', 'payment_method' => 'cash', 'amount' => 500,
            'reference_number' => 'SEC-TEST-1', 'status' => 'pending',
        ]);

        $this->actingAs($student, 'web')
            ->postJson("/admin/payments/{$tx->id}/approve")
            ->assertStatus(403);

        $this->assertDatabaseHas('payment_transactions', ['id' => $tx->id, 'status' => 'pending']);
    }

    public function test_a_student_cannot_reject_or_delete_a_payment_transaction(): void
    {
        $student = User::factory()->student()->create();
        $enrollment = Enrollment::factory()->create(['user_id' => $student->id]);
        $tx = PaymentTransaction::create([
            'enrollment_id' => $enrollment->id, 'user_id' => $student->id,
            'payment_type' => 'walkin', 'payment_method' => 'cash', 'amount' => 500,
            'reference_number' => 'SEC-TEST-2', 'status' => 'pending',
        ]);

        $this->actingAs($student, 'web')->postJson("/admin/payments/{$tx->id}/reject")->assertStatus(403);
        $this->actingAs($student, 'web')->deleteJson("/admin/payments/{$tx->id}")->assertStatus(403);
        $this->assertDatabaseHas('payment_transactions', ['id' => $tx->id, 'status' => 'pending']);
    }

    public function test_a_student_cannot_use_the_admin_payment_processing_endpoint(): void
    {
        $student = User::factory()->student()->create();
        $enrollment = Enrollment::factory()->create(['user_id' => $student->id]);

        $this->actingAs($student, 'web')
            ->postJson("/admin/enrollments/{$enrollment->id}/payment", [
                'payment_option' => 'A', 'payment_method' => 'cash', 'amount' => '100',
            ])
            ->assertStatus(403);
    }

    public function test_admin_can_still_approve_payments_after_the_fix(): void
    {
        $admin = User::factory()->admin()->create();
        $student = User::factory()->student()->create();
        $enrollment = Enrollment::factory()->create(['user_id' => $student->id]);
        $tx = PaymentTransaction::create([
            'enrollment_id' => $enrollment->id, 'user_id' => $student->id,
            'payment_type' => 'walkin', 'payment_method' => 'cash', 'amount' => 500,
            'reference_number' => 'SEC-TEST-3', 'status' => 'pending',
        ]);

        $this->actingAs($admin, 'web')
            ->postJson("/admin/payments/{$tx->id}/approve")
            ->assertOk()
            ->assertJson(['success' => true]);

        $this->assertDatabaseHas('payment_transactions', ['id' => $tx->id, 'status' => 'completed']);
    }

    public function test_a_student_cannot_reach_any_teacher_route(): void
    {
        $student = User::factory()->student()->create();

        $this->actingAs($student, 'web')->get('/teacher/dashboard')->assertStatus(403);
        $this->actingAs($student, 'web')->postJson('/teacher/grades/save', [])->assertStatus(403);
        $this->actingAs($student, 'web')->postJson('/teacher/attendance/load', [])->assertStatus(403);
    }

    public function test_teacher_can_still_reach_their_own_dashboard_after_the_fix(): void
    {
        $teacher = User::factory()->teacher()->create();

        $this->actingAs($teacher, 'web')->get('/teacher/dashboard')->assertOk();
    }

    public function test_admin_and_superadmin_can_still_reach_teacher_routes(): void
    {
        $admin = User::factory()->admin()->create();
        $superadmin = User::factory()->superadmin()->create();

        $this->actingAs($admin, 'web')->get('/teacher/dashboard')->assertOk();
        $this->actingAs($superadmin, 'web')->get('/teacher/dashboard')->assertOk();
    }
}
