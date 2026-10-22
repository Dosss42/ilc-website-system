<?php

namespace Tests\Feature\Security;

use App\Models\ActivityLog;
use App\Models\Enrollment;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

/**
 * Automatic logout after the Session Timeout (Admin › Settings › Security): every portal
 * page carries the idle timer with that many minutes; when it runs out the user is logged
 * out (and it's recorded) and lands on the right login form with a "You have been logged
 * out" notice in front of it. An expired form submit (419) lands on the same page instead of an error.
 */
class SessionTimeoutTest extends TestCase
{
    use RefreshDatabase;

    public function test_every_portal_page_carries_the_idle_timer_with_the_configured_minutes(): void
    {
        Setting::set('session_timeout', 15, 'integer');
        Setting::set('current_school_year', '2026-2027');

        $pages = [
            ['web', User::factory()->create(['role' => 'superadmin']), '/superadmin/dashboard', 'web'],
            ['web', User::factory()->admin()->create(), '/admin/dashboard', 'web'],
            ['web', User::factory()->create(['role' => 'teacher']), '/teacher/dashboard', 'web'],
            ['web', Enrollment::factory()->create()->user, '/student/portal', 'web'],
            ['finance', User::factory()->finance()->create(), '/finance/dashboard', 'finance'],
            ['cashier', User::factory()->cashier()->create(), '/cashier/dashboard', 'cashier'],
        ];
        foreach ($pages as [$guard, $user, $url, $portal]) {
            $html = $this->actingAs($user, $guard)->get($url)->assertOk()->getContent();
            $this->assertStringContainsString('/js/idle-logout.js', $html, $url);
            $this->assertStringContainsString('data-minutes="15"', $html, $url);
            $this->assertStringContainsString('session-expired?portal=' . $portal, $html, $url);
            $this->assertSame(1, substr_count($html, '/js/idle-logout.js'), "$url includes it once");
        }

        // Not on public pages / login forms
        auth()->logout();
        $this->get('/login')->assertDontSee('/js/idle-logout.js');
    }

    public function test_expire_logs_out_every_portal_and_records_it(): void
    {
        Setting::set('session_timeout', 15, 'integer');
        $cashier = User::factory()->cashier()->create(['name' => 'Cashier Ana']);

        $this->actingAs($cashier, 'cashier')->postJson('/session/expire')->assertOk()->assertJson(['ok' => true]);
        $this->assertGuest('cashier');
        $this->assertTrue(ActivityLog::where('event_type', 'logout')
            ->where('description', 'Cashier Ana was logged out automatically after 15 minutes of inactivity')->exists());

        $this->actingAs($cashier, 'cashier')->get('/cashier/dashboard')->assertOk();
        $this->getJson('/session/ping')->assertOk()->assertJson(['minutes' => 15]);
    }

    public function test_keep_alive_reports_a_logout_from_another_window(): void
    {
        $admin = User::factory()->admin()->create();
        $this->actingAs($admin, 'web')->getJson('/session/ping')->assertOk();

        // Another window/tab logs out the shared session…
        $this->postJson('/session/expire')->assertOk();
        // …so this window's next keep-alive says so, and its script goes to "You have been logged out"
        $this->getJson('/session/ping')->assertStatus(401)->assertJson(['session_expired' => true]);
    }

    public function test_after_logout_the_right_login_form_shows_the_notice_once(): void
    {
        Setting::set('session_timeout', 15, 'integer');

        foreach (['cashier' => 'cashier.login', 'finance' => 'finance.login', 'web' => 'login', 'evil.com' => 'login'] as $portal => $login) {
            $this->get('/session-expired?portal=' . $portal)->assertRedirect(route($login));
            $this->get(route($login))->assertOk()
                ->assertSee('id="ilc-session-notice"', false)
                ->assertSee('You have been logged out')->assertSee('15 minutes');
            // Shown once: reloading the login form doesn't bring it back
            $this->get(route($login))->assertOk()->assertDontSee('id="ilc-session-notice"', false);
        }

        $this->get('/session-expired?portal=web&reason=expired');
        $this->get(route('login'))->assertSee('Your session has expired');

        // A normal visit to the login form has no notice
        $this->get(route('cashier.login'))->assertDontSee('id="ilc-session-notice"', false);
    }

    public function test_an_expired_form_goes_to_the_logged_out_page_instead_of_an_error(): void
    {
        // CSRF is skipped in tests, so throw what the real check throws
        Route::middleware('web')->post('/cashier/_t419', fn () => throw new \Illuminate\Session\TokenMismatchException());
        Route::middleware('web')->post('/admin/_t419', fn () => throw new \Illuminate\Session\TokenMismatchException());
        Route::middleware('web')->post('/login/_t419', fn () => throw new \Illuminate\Session\TokenMismatchException());

        $this->post('/cashier/_t419')->assertRedirect(route('session.expired', ['portal' => 'cashier', 'reason' => 'expired']));
        $this->post('/admin/_t419')->assertRedirect(route('session.expired', ['portal' => 'web', 'reason' => 'expired']));
        $this->postJson('/admin/_t419')->assertStatus(419)->assertJson(['session_expired' => true]);
    }

    public function test_coming_back_after_the_limit_is_logged_out_even_if_the_page_reloads_itself(): void
    {
        Setting::set('session_timeout', 15, 'integer');
        $admin = User::factory()->admin()->create(['name' => 'Admin Bea']);
        $key = \App\Http\Middleware\EnforceIdleTimeout::KEY;

        // Away 16 minutes, then the portal's own "reload when the tab is visible again"
        $this->actingAs($admin, 'web')->withSession([$key => time() - 16 * 60])
            ->get('/admin/dashboard', ['Sec-Fetch-Mode' => 'navigate'])
            ->assertRedirect(route('session.expired', ['portal' => 'web']));
        $this->assertGuest('web');
        $this->assertTrue(ActivityLog::where('description', 'Admin Bea was logged out automatically after 15 minutes of inactivity')->exists());

        // A background request after the limit gets JSON the page script understands
        $cashier = User::factory()->cashier()->create();
        $this->actingAs($cashier, 'cashier')->withSession([$key => time() - 16 * 60])
            ->getJson('/cashier/students')->assertStatus(401)->assertJson(['session_expired' => true]);
        $this->assertGuest('cashier');
    }

    public function test_only_real_user_activity_keeps_the_session_alive(): void
    {
        Setting::set('session_timeout', 15, 'integer');
        $key = \App\Http\Middleware\EnforceIdleTimeout::KEY;
        $cashier = User::factory()->cashier()->create();
        $tenMinAgo = time() - 10 * 60;

        // Background polling (fetch) does NOT count as activity
        $this->actingAs($cashier, 'cashier')->withSession([$key => $tenMinAgo])
            ->get('/cashier/students', ['Sec-Fetch-Mode' => 'cors', 'Accept' => 'application/json'])->assertOk();
        $this->assertSame($tenMinAgo, session($key));

        // The in-page activity ping does
        $this->get('/session/ping', ['Sec-Fetch-Mode' => 'cors'])->assertOk();
        $this->assertGreaterThanOrEqual(time() - 2, session($key));

        // Opening a page does
        $this->withSession([$key => $tenMinAgo])->get('/cashier/dashboard', ['Sec-Fetch-Mode' => 'navigate'])->assertOk();
        $this->assertGreaterThanOrEqual(time() - 2, session($key));

        // Within the limit nothing happens
        $this->assertAuthenticatedAs($cashier, 'cashier');
    }

    public function test_short_timeouts_are_used_as_set_and_open_pages_learn_a_new_value(): void
    {
        $admin = User::factory()->admin()->create(['name' => 'Admin Bea']);
        $key = \App\Http\Middleware\EnforceIdleTimeout::KEY;

        // 2 minutes is used as 2 (it used to become 5 without saying so); below 1 becomes 1
        Setting::set('session_timeout', 2, 'integer');
        $this->assertSame(2, \App\Support\SecurityPolicy::sessionMinutes());
        Setting::set('session_timeout', 0, 'integer');
        $this->assertSame(1, \App\Support\SecurityPolicy::sessionMinutes());
        Setting::set('session_timeout', 2, 'integer');

        // The page carries 2, and the in-page ping reports the current value
        $this->actingAs($admin, 'web')->get('/admin/dashboard')->assertSee('data-minutes="2"', false);
        $this->getJson('/session/ping')->assertOk()->assertJson(['minutes' => 2]);
        Setting::set('session_timeout', 3, 'integer'); // changed in Settings while the page is open
        $this->getJson('/session/ping')->assertOk()->assertJson(['minutes' => 3]);
        Setting::set('session_timeout', 2, 'integer');

        // The server enforces 2 minutes too: 1.5 min idle is fine, 2.5 min is logged out
        $this->withSession([$key => time() - 90])->get('/admin/dashboard', ['Sec-Fetch-Mode' => 'navigate'])->assertOk();
        $this->withSession([$key => time() - 150])->get('/admin/dashboard', ['Sec-Fetch-Mode' => 'navigate'])
            ->assertRedirect(route('session.expired', ['portal' => 'web']));
        $this->assertTrue(ActivityLog::where('description', 'Admin Bea was logged out automatically after 2 minutes of inactivity')->exists());

        // "1 minute", not "1 minutes"
        $this->assertSame('1 minute', \App\Support\SecurityPolicy::sessionMinutesLabel(1));
        $this->get('/session-expired?portal=web');
        $this->get(route('login'))->assertSee('2 minutes');

        // The Settings field allows 1–1440
        $this->actingAs(User::factory()->admin()->create(), 'web')->get('/admin/dashboard')
            ->assertSee('id="set-session_timeout" class="form-fld" min="1" max="1440"', false);
    }

    public function test_a_login_form_left_open_too_long_just_reloads(): void
    {
        $this->app['router']->post('/login', fn () => throw new \Illuminate\Session\TokenMismatchException())->middleware('web');
        $this->from('/login')->post('/login', ['email' => 'a@b.com', 'password' => 'x'])
            ->assertRedirect('/login')->assertSessionHasErrors(['email' => 'The page was open for too long. Please try again.']);
    }
}
