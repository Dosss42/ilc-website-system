<?php

namespace Tests\Feature\Security;

use App\Models\Enrollment;
use App\Models\GuidanceRecord;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Certificate of Good Moral Character (school decisions, October 30, 2026): Guidance Counselor certifies,
 * School Head notes, the admin types the purpose; balance / guidance records warn, never block.
 */
class GoodMoralCertificateTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();
        Setting::set('current_school_year', '2026-2027');
        $this->admin = User::factory()->admin()->create();
    }

    public function test_the_admin_sees_what_to_know_first_then_prints_the_certificate(): void
    {
        $e = Enrollment::factory()->withInstallment()->create(['grade_level' => 'grade3', 'school_year' => '2026-2027',
            'payment_amount' => 3000, 'remaining_balance' => 11504, 'payment_status' => 'partial',
            'student_data' => ['first_name' => 'Juan', 'last_name' => 'Dela Cruz', 'grade_level' => 'grade3', 'student_type' => 'returning']]);
        GuidanceRecord::create(['student_id' => $e->user_id, 'counselor_id' => $this->admin->id, 'date' => '2026-09-15',
            'concern_type' => 'Behavioral', 'concern_description' => 'x', 'status' => 'resolved']);

        $c = $this->actingAs($this->admin, 'web')->getJson("/admin/students/{$e->user_id}/good-moral/check")->assertOk()->json();
        $this->assertSame('Grade 3, S.Y. 2026-2027', $c['covers']);
        $this->assertEquals([['school_year' => '2026-2027', 'grade' => 'Grade 3', 'amount' => 11504]], $c['balances']);
        $this->assertSame('Behavioral', $c['guidance'][0]['concern']);

        // Warned, not blocked: it still prints, and the print is logged
        $res = $this->actingAs($this->admin, 'web')->get("/admin/students/{$e->user_id}/good-moral?purpose=transfer+to+another+school&print=1")->assertOk();
        $this->assertSame('application/pdf', $res->headers->get('Content-Type'));
        $this->assertDatabaseHas('activity_logs', ['description' => "Printed Certificate of Good Moral Character for {$e->user->name} (purpose: transfer to another school)"]);
    }

    public function test_settings_show_the_guidance_counselor_before_it_was_ever_saved(): void
    {
        $school = collect($this->actingAs($this->admin, 'web')->getJson('/admin/settings')->assertOk()->json('settings.school'));
        $this->assertSame('Dss. Celia B. Cortez', $school->firstWhere('key', 'guidance_counselor_name')['value'] ?? null);
        $this->assertDatabaseMissing('settings', ['key' => 'guidance_counselor_name']); // shown, not saved, until Save
    }

    public function test_a_student_without_an_enrollment_has_nothing_to_certify_and_students_cannot_print(): void
    {
        $nobody = User::factory()->create(['role' => 'student']);
        $this->actingAs($this->admin, 'web')->getJson("/admin/students/{$nobody->id}/good-moral/check")->assertStatus(422);

        $e = Enrollment::factory()->fullyPaid()->create();
        $this->actingAs($e->user, 'web')->get("/admin/students/{$e->user_id}/good-moral")->assertForbidden();
    }
}
