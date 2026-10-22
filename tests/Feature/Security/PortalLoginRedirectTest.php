<?php

namespace Tests\Feature\Security;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Logging in to the Cashier or Finance portal must land in that portal. Laravel remembers the
 * last protected page a guest tried to open ("intended" URL) in one session key shared by all
 * portals, so opening an Admin page while logged out and then signing in at /cashier/login
 * used to send the cashier to the Admin dashboard.
 */
class PortalLoginRedirectTest extends TestCase
{
    use RefreshDatabase;

    public function test_cashier_login_lands_in_the_cashier_portal_even_after_visiting_admin(): void
    {
        $cashier = User::factory()->cashier()->create(['password' => bcrypt('secret123')]);

        $this->get('/admin/dashboard')->assertRedirect(); // guest → login, remembers /admin/dashboard

        $this->post('/cashier/login', ['email' => $cashier->email, 'password' => 'secret123'])
            ->assertRedirect(route('cashier.dashboard'));
        $this->assertNull(session('url.intended'), 'the Admin page is forgotten, not kept for later');
    }

    public function test_finance_login_lands_in_the_finance_portal_even_after_visiting_admin(): void
    {
        $finance = User::factory()->finance()->create(['password' => bcrypt('secret123')]);

        $this->get('/admin/dashboard')->assertRedirect();

        $this->post('/finance/login', ['email' => $finance->email, 'password' => 'secret123'])
            ->assertRedirect(route('finance.dashboard'));
    }

    public function test_a_page_inside_the_same_portal_is_still_returned_to(): void
    {
        $finance = User::factory()->finance()->create(['password' => bcrypt('secret123')]);

        $this->withSession(['url.intended' => url('/finance/payments')])
            ->post('/finance/login', ['email' => $finance->email, 'password' => 'secret123'])
            ->assertRedirect(url('/finance/payments'));
    }
}
