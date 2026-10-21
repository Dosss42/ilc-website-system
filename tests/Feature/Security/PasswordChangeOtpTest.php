<?php

namespace Tests\Feature\Security;

use App\Mail\PasswordChangeOtpMail;
use App\Models\OtpVerification;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

/**
 * Student and Teacher portals change a password through an emailed 6-digit
 * OTP (App\Support\Traits\ChangesPasswordWithOtp). Regression tests for:
 *  - Send OTP answered a rejected new password with a redirect, which the
 *    page's fetch() showed as a vague "Network error";
 *  - the attempts countdown was off by one ("-1 attempt(s) remaining");
 *  - an email-send failure crashed with a 500 and left a dead OTP behind.
 */
class PasswordChangeOtpTest extends TestCase
{
    use RefreshDatabase;

    public static function portals(): array
    {
        return [
            'student' => ['student', '/student/settings/password', 'account'],
            'teacher' => ['teacher', '/teacher/settings/password', 'password'],
        ];
    }

    private function makeUser(string $role): User
    {
        return User::factory()->{$role}()->create(['password' => 'OldPass123', 'is_active' => true]);
    }

    #[DataProvider('portals')]
    public function test_send_otp_explains_why_a_password_is_rejected(string $role, string $base, string $tab): void
    {
        Mail::fake();
        $user = $this->makeUser($role);

        // Fails the Security policy (no uppercase) — must come back as readable JSON, not a redirect
        $this->actingAs($user)->postJson("$base/send-otp", [
            'current_password' => 'OldPass123', 'password' => 'newpassword1', 'password_confirmation' => 'newpassword1',
        ])->assertStatus(422)->assertJson(['success' => false, 'message' => 'The password must contain at least one uppercase letter.']);

        // Same request the way the page sends it (JSON body, Accept header) — must not redirect either
        $this->actingAs($user)->withHeaders(['Accept' => 'application/json'])->post("$base/send-otp", [
            'current_password' => 'OldPass123', 'password' => 'Short1', 'password_confirmation' => 'Short1',
        ])->assertStatus(422)->assertJsonPath('success', false)->assertJsonPath('message', 'The password field must be at least 8 characters.');

        $this->actingAs($user)->postJson("$base/send-otp", [
            'current_password' => 'WrongPass1', 'password' => 'NewPass123', 'password_confirmation' => 'NewPass123',
        ])->assertStatus(422)->assertJson(['message' => 'Current password is incorrect.']);

        Mail::assertNothingSent();
        $this->assertDatabaseMissing('otp_verifications', ['email' => $user->email]);
    }

    #[DataProvider('portals')]
    public function test_full_flow_changes_the_password(string $role, string $base, string $tab): void
    {
        Mail::fake();
        $user = $this->makeUser($role);

        $res = $this->actingAs($user)->postJson("$base/send-otp", [
            'current_password' => 'OldPass123', 'password' => 'NewPass123', 'password_confirmation' => 'NewPass123',
        ])->assertOk()->assertJson(['success' => true, 'email' => $user->email]);

        $code = null;
        Mail::assertSent(PasswordChangeOtpMail::class, function ($mail) use ($user, &$code) {
            $code = $mail->otp;
            return $mail->hasTo($user->email);
        });
        $this->assertMatchesRegularExpression('/^\d{6}$/', $code);

        $this->actingAs($user)->from('/x')->put($base, ['otp_token' => $res->json('token'), 'otp_code' => $code])
            ->assertRedirect('/x')
            ->assertSessionHas('password_success', 'Password changed successfully!')
            ->assertSessionHas('settings_tab', $tab);

        $this->assertTrue(Hash::check('NewPass123', $user->fresh()->password));
        $this->assertDatabaseMissing('otp_verifications', ['email' => $user->email]);
        $this->assertDatabaseHas('activity_logs', ['description' => "{$user->name} changed their own password"]);
    }

    #[DataProvider('portals')]
    public function test_wrong_codes_count_down_correctly_then_lock(string $role, string $base, string $tab): void
    {
        Mail::fake();
        $user = $this->makeUser($role);
        $token = $this->actingAs($user)->postJson("$base/send-otp", [
            'current_password' => 'OldPass123', 'password' => 'NewPass123', 'password_confirmation' => 'NewPass123',
        ])->json('token');
        $real = null;
        Mail::assertSent(PasswordChangeOtpMail::class, function ($m) use (&$real) { $real = $m->otp; return true; });
        $wrong = $real === '000000' ? '111111' : '000000';

        $expected = [
            ['Incorrect OTP. 2 attempt(s) remaining.', true],
            ['Incorrect OTP. 1 attempt(s) remaining.', true],
            ['Incorrect OTP. No attempts left — please request a new OTP.', false],
        ];
        foreach ($expected as [$message, $keepsOtpStep]) {
            $r = $this->actingAs($user)->from('/x')->put($base, ['otp_token' => $token, 'otp_code' => $wrong])
                ->assertSessionHasErrors(['otp_code' => $message]);
            $keepsOtpStep ? $r->assertSessionHas('otp_token', $token) : $r->assertSessionMissing('otp_token');
        }

        // Code is dead now — even the right one is refused, and the password is unchanged
        $this->actingAs($user)->from('/x')->put($base, ['otp_token' => $token, 'otp_code' => $real])
            ->assertSessionHasErrors(['otp_code' => 'OTP expired or invalid. Please request a new one.']);
        $this->assertTrue(Hash::check('OldPass123', $user->fresh()->password));
    }

    #[DataProvider('portals')]
    public function test_email_failure_returns_a_clear_message_and_cleans_up(string $role, string $base, string $tab): void
    {
        $user = $this->makeUser($role);
        Mail::shouldReceive('to')->andThrow(new \RuntimeException('SMTP down'));

        $this->actingAs($user)->postJson("$base/send-otp", [
            'current_password' => 'OldPass123', 'password' => 'NewPass123', 'password_confirmation' => 'NewPass123',
        ])->assertStatus(503)->assertJson([
            'success' => false,
            'message' => "Couldn't send the OTP email right now. Please try again in a few minutes.",
        ]);

        $this->assertDatabaseMissing('otp_verifications', ['email' => $user->email]);
        $this->assertTrue(Hash::check('OldPass123', $user->fresh()->password));
    }

    public function test_otp_expiry_is_not_reset_when_the_row_is_updated(): void
    {
        // MariaDB had silently added ON UPDATE CURRENT_TIMESTAMP to expires_at,
        // so recording one wrong attempt expired the code on the spot.
        $expires = now()->addMinutes(10)->startOfSecond();
        $otp = OtpVerification::create([
            'email' => 'x@example.com', 'code' => Hash::make('123456'), 'token' => 'tok-1',
            'attempts' => 0, 'verified' => false, 'expires_at' => $expires,
        ]);
        $otp->increment('attempts');
        $otp->update(['verified' => true]);

        $this->assertEquals($expires->toDateTimeString(), $otp->fresh()->expires_at->toDateTimeString());
        $this->assertFalse($otp->fresh()->isExpired());
    }

    public function test_enrollment_email_code_survives_a_typo_and_counts_down(): void
    {
        $email = 'applicant@gmail.com';
        OtpVerification::create([
            'email' => $email, 'code' => bcrypt('654321'), 'token' => 'enr-token',
            'attempts' => 0, 'verified' => false, 'expires_at' => now()->addMinutes(3),
        ]);

        $this->postJson('/enrollment/verify-otp', ['email' => $email, 'code' => '000000'])
            ->assertStatus(422)->assertJson(['message' => 'Incorrect code. 2 attempts remaining.']);
        $this->postJson('/enrollment/verify-otp', ['email' => $email, 'code' => '000000'])
            ->assertStatus(422)->assertJson(['message' => 'Incorrect code. 1 attempt remaining.']);

        // Right code after two typos still works (it used to say "expired")
        $this->postJson('/enrollment/verify-otp', ['email' => $email, 'code' => '654321'])
            ->assertOk()->assertJson(['success' => true, 'token' => 'enr-token']);
    }

    public function test_enrollment_resend_is_allowed_after_60_seconds(): void
    {
        $email = 'applicant2@gmail.com';
        $this->postJson('/enrollment/send-otp', ['email' => $email])->assertOk();
        $this->postJson('/enrollment/send-otp', ['email' => $email])->assertStatus(429);

        $this->travel(61)->seconds();
        $this->postJson('/enrollment/send-otp', ['email' => $email])->assertOk();
    }

    #[DataProvider('portals')]
    public function test_an_expired_code_is_refused(string $role, string $base, string $tab): void
    {
        Mail::fake();
        $user = $this->makeUser($role);
        $token = $this->actingAs($user)->postJson("$base/send-otp", [
            'current_password' => 'OldPass123', 'password' => 'NewPass123', 'password_confirmation' => 'NewPass123',
        ])->json('token');
        $code = null;
        Mail::assertSent(PasswordChangeOtpMail::class, function ($m) use (&$code) { $code = $m->otp; return true; });

        OtpVerification::where('token', $token)->update(['expires_at' => now()->subMinute()]);

        $this->actingAs($user)->from('/x')->put($base, ['otp_token' => $token, 'otp_code' => $code])
            ->assertSessionHasErrors(['otp_code' => 'OTP expired or invalid. Please request a new one.']);
        $this->assertTrue(Hash::check('OldPass123', $user->fresh()->password));
    }
}
