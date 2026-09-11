<?php

namespace Tests\Feature\Auth;

use App\Mail\ForgotPasswordMail;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Password;
use Tests\TestCase;

class ForgotPasswordTest extends TestCase
{
    use RefreshDatabase;

    public function test_requesting_a_reset_link_sends_the_branded_email(): void
    {
        Mail::fake();
        $user = User::factory()->student()->create(['email' => 'student@example.com']);

        $response = $this->post('/forgot-password', ['email' => 'student@example.com']);

        $response->assertRedirect();
        $response->assertSessionHas('status');
        Mail::assertSent(ForgotPasswordMail::class, fn ($mail) => $mail->hasTo('student@example.com'));
        $this->assertDatabaseHas('password_reset_tokens', ['email' => 'student@example.com']);
    }

    public function test_requesting_a_reset_link_for_an_unknown_email_does_not_reveal_that(): void
    {
        Mail::fake();

        $response = $this->post('/forgot-password', ['email' => 'nobody@example.com']);

        // Same generic response either way — doesn't leak which emails exist.
        $response->assertRedirect();
        $response->assertSessionHas('status');
        Mail::assertNothingSent();
    }

    public function test_resetting_with_a_valid_token_changes_the_password_and_redirects_to_the_right_login(): void
    {
        $student = User::factory()->student()->create(['email' => 'reset-me@example.com']);
        $token = Password::broker()->createToken($student);

        $response = $this->post('/reset-password', [
            'token' => $token,
            'email' => 'reset-me@example.com',
            'password' => 'NewSecurePass123',
            'password_confirmation' => 'NewSecurePass123',
        ]);

        $response->assertRedirect(route('login'));

        $student->refresh();
        $this->assertTrue(Hash::check('NewSecurePass123', $student->password));
    }

    public function test_resetting_a_finance_users_password_redirects_to_the_finance_login(): void
    {
        $finance = User::factory()->finance()->create(['email' => 'finance-reset@example.com']);
        $token = Password::broker()->createToken($finance);

        $this->post('/reset-password', [
            'token' => $token,
            'email' => 'finance-reset@example.com',
            'password' => 'NewSecurePass123',
            'password_confirmation' => 'NewSecurePass123',
        ])->assertRedirect(route('finance.login'));
    }

    public function test_resetting_a_cashier_users_password_redirects_to_the_cashier_login(): void
    {
        $cashier = User::factory()->cashier()->create(['email' => 'cashier-reset@example.com']);
        $token = Password::broker()->createToken($cashier);

        $this->post('/reset-password', [
            'token' => $token,
            'email' => 'cashier-reset@example.com',
            'password' => 'NewSecurePass123',
            'password_confirmation' => 'NewSecurePass123',
        ])->assertRedirect(route('cashier.login'));
    }

    public function test_resetting_with_an_invalid_token_fails(): void
    {
        $student = User::factory()->student()->create(['email' => 'bad-token@example.com']);
        $originalHash = $student->password;

        $this->post('/reset-password', [
            'token' => 'totally-invalid-token',
            'email' => 'bad-token@example.com',
            'password' => 'NewSecurePass123',
            'password_confirmation' => 'NewSecurePass123',
        ])->assertSessionHasErrors();

        $student->refresh();
        $this->assertEquals($originalHash, $student->password);
    }
}
