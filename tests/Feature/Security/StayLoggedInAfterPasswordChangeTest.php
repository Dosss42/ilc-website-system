<?php

namespace Tests\Feature\Security;

use App\Mail\PasswordChangeOtpMail;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

/**
 * Sanctum's AuthenticateSession (on the /api routes) compares the password hash
 * it stored in the session with the user's current one and logs the session out
 * on a mismatch. Changing your OWN password used to leave the old hash there, so
 * the next API call returned 401 — on the Student Portal the reload after the OTP
 * showed "Failed to calculate fees" and the student was silently logged out.
 * (Reproduced end-to-end with real cookies; see the PR notes.)
 *
 * App\Support\SessionPasswordHash::refresh() stores the new hash, so these
 * assert the session holds the NEW password's hash after each change.
 */
class StayLoggedInAfterPasswordChangeTest extends TestCase
{
    use RefreshDatabase;

    private function assertSessionHashMatchesCurrentPassword(User $user): void
    {
        $expected = Auth::guard('web')->hashPasswordForCookie($user->fresh()->password);
        $this->assertSame($expected, session('password_hash_web'), 'Session still holds the old password hash — the next API call would log this user out.');
    }

    private function seedOldHash(User $user): void
    {
        // What Sanctum stores after the page's first API call
        session(['password_hash_web' => Auth::guard('web')->hashPasswordForCookie($user->password)]);
    }

    public function test_student_otp_password_change_keeps_the_session_valid(): void
    {
        Mail::fake();
        $student = User::factory()->student()->create(['password' => 'OldPass123', 'is_active' => true]);
        $this->actingAs($student, 'web');
        $this->seedOldHash($student);

        $token = $this->postJson('/student/settings/password/send-otp', [
            'current_password' => 'OldPass123', 'password' => 'NewPass123', 'password_confirmation' => 'NewPass123',
        ])->assertOk()->json('token');
        $code = null;
        Mail::assertSent(PasswordChangeOtpMail::class, function ($m) use (&$code) { $code = $m->otp; return true; });

        $this->from('/student/portal')->put('/student/settings/password', ['otp_token' => $token, 'otp_code' => $code])
            ->assertSessionHas('password_success');

        $this->assertSessionHashMatchesCurrentPassword($student);
    }

    public function test_teacher_otp_password_change_keeps_the_session_valid(): void
    {
        Mail::fake();
        $teacher = User::factory()->teacher()->create(['password' => 'OldPass123', 'is_active' => true]);
        $this->actingAs($teacher, 'web');
        $this->seedOldHash($teacher);

        $token = $this->postJson('/teacher/settings/password/send-otp', [
            'current_password' => 'OldPass123', 'password' => 'NewPass123', 'password_confirmation' => 'NewPass123',
        ])->assertOk()->json('token');
        $code = null;
        Mail::assertSent(PasswordChangeOtpMail::class, function ($m) use (&$code) { $code = $m->otp; return true; });

        $this->from('/x')->put('/teacher/settings/password', ['otp_token' => $token, 'otp_code' => $code])
            ->assertSessionHas('password_success');

        $this->assertSessionHashMatchesCurrentPassword($teacher);
    }

    public function test_admin_and_superadmin_password_change_keeps_the_session_valid(): void
    {
        foreach (['admin' => '/admin/settings/password', 'superadmin' => '/superadmin/settings/password'] as $role => $url) {
            $user = User::factory()->{$role}()->create(['password' => 'OldPass123', 'is_active' => true]);
            $this->actingAs($user, 'web');
            $this->seedOldHash($user);

            $this->from('/x')->put($url, [
                'current_password' => 'OldPass123', 'password' => 'NewPass123', 'password_confirmation' => 'NewPass123',
            ])->assertSessionHas('password_success');

            $this->assertSessionHashMatchesCurrentPassword($user);
        }
    }

    public function test_superadmin_resetting_their_own_account_keeps_the_session_valid(): void
    {
        $super = User::factory()->superadmin()->create(['password' => 'OldPass123', 'is_active' => true]);
        $this->actingAs($super, 'web');
        $this->seedOldHash($super);

        $this->postJson("/superadmin/users/{$super->id}/reset-password", [
            'password' => 'NewPass123', 'password_confirmation' => 'NewPass123',
        ])->assertOk();

        $this->assertSessionHashMatchesCurrentPassword($super);
    }
}
