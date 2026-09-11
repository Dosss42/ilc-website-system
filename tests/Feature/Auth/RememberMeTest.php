<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class RememberMeTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Laravel's guard only *generates* a remember_token when the existing
     * one is empty — it won't rotate an already-set one on every login. So
     * these tests start from a real null token (what a genuinely
     * never-remembered-before account looks like) rather than relying on
     * whatever the factory happens to pre-seed.
     */
    public function test_checking_remember_me_on_main_login_sets_a_remember_token(): void
    {
        $student = User::factory()->student()->create([
            'email' => 'remember-student@example.com',
            'password' => Hash::make('password123'),
            'remember_token' => null,
        ]);

        $response = $this->post('/login', [
            'email' => 'remember-student@example.com',
            'password' => 'password123',
            'remember' => '1',
        ]);

        $response->assertRedirect();
        $this->assertAuthenticatedAs($student->fresh());
        $this->assertNotNull($student->fresh()->remember_token, 'remember_token should be set when "remember" is checked.');

        $cookies = $response->headers->getCookies();
        $hasRememberCookie = collect($cookies)->contains(fn ($c) => str_starts_with($c->getName(), 'remember_web_'));
        $this->assertTrue($hasRememberCookie, 'A remember_web_* cookie should be issued.');
    }

    public function test_not_checking_remember_me_leaves_no_remember_token(): void
    {
        $student = User::factory()->student()->create([
            'email' => 'no-remember@example.com',
            'password' => Hash::make('password123'),
            'remember_token' => null,
        ]);

        $this->post('/login', [
            'email' => 'no-remember@example.com',
            'password' => 'password123',
            // no 'remember' field at all — matches an unchecked checkbox
        ])->assertRedirect();

        $this->assertNull($student->fresh()->remember_token, 'remember_token should stay null when "remember" was not checked.');
    }

    public function test_checking_remember_me_on_cashier_login_sets_a_remember_token(): void
    {
        $cashier = User::factory()->cashier()->create([
            'email' => 'remember-cashier@example.com',
            'password' => Hash::make('password123'),
            'remember_token' => null,
        ]);

        $response = $this->post('/cashier/login', [
            'email' => 'remember-cashier@example.com',
            'password' => 'password123',
            'remember' => '1',
        ]);

        $response->assertRedirect(route('cashier.dashboard'));
        $this->assertNotNull($cashier->fresh()->remember_token);
    }

    public function test_checking_remember_me_on_finance_login_sets_a_remember_token(): void
    {
        $finance = User::factory()->finance()->create([
            'email' => 'remember-finance@example.com',
            'password' => Hash::make('password123'),
            'remember_token' => null,
        ]);

        $response = $this->post('/finance/login', [
            'email' => 'remember-finance@example.com',
            'password' => 'password123',
            'remember' => '1',
        ]);

        $response->assertRedirect(route('finance.dashboard'));
        $this->assertNotNull($finance->fresh()->remember_token);
    }
}
