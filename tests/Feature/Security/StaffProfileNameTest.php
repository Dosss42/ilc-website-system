<?php

namespace Tests\Feature\Security;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Finance and Cashier staff can change their own display name from their
 * portal's Settings page. Cashier had no way to do this at all (read-only
 * field, no route); Finance's form also had a Phone field that was silently
 * discarded on save because users has no phone column.
 */
class StaffProfileNameTest extends TestCase
{
    use RefreshDatabase;

    public function test_finance_user_can_change_their_name(): void
    {
        $user = User::factory()->finance()->create(['name' => 'Old Finance Name']);

        $this->actingAs($user, 'finance')
            ->from('/finance/settings')
            ->put('/finance/profile', ['name' => 'New Finance Name'])
            ->assertRedirect('/finance/settings')
            ->assertSessionHas('success', 'Profile updated successfully!');

        $this->assertDatabaseHas('users', ['id' => $user->id, 'name' => 'New Finance Name']);
        $this->assertDatabaseHas('activity_logs', ['description' => 'Old Finance Name changed their name to New Finance Name']);

        $this->actingAs($user->fresh(), 'finance')->get('/finance/settings')
            ->assertOk()
            ->assertSee('value="New Finance Name"', false)
            ->assertDontSee('name="phone"', false);
    }

    public function test_cashier_user_can_change_their_name(): void
    {
        $user = User::factory()->cashier()->create(['name' => 'Old Cashier Name']);

        $this->actingAs($user, 'cashier')
            ->from('/cashier/dashboard')
            ->put('/cashier/profile', ['name' => 'New Cashier Name'])
            ->assertRedirect('/cashier/dashboard')
            ->assertSessionHas('profile_success', 'Profile updated successfully!');

        $this->assertDatabaseHas('users', ['id' => $user->id, 'name' => 'New Cashier Name']);
        $this->assertDatabaseHas('activity_logs', ['description' => 'Old Cashier Name changed their name to New Cashier Name']);

        // The success banner and the new name both render on the next page load
        $this->actingAs($user->fresh(), 'cashier')
            ->withSession(['profile_success' => 'Profile updated successfully!'])
            ->get('/cashier/dashboard')
            ->assertOk()
            ->assertSee('Profile updated successfully!')
            ->assertSee('value="New Cashier Name"', false);
    }

    public function test_an_empty_or_too_long_name_is_rejected_and_shown(): void
    {
        $finance = User::factory()->finance()->create(['name' => 'Keep Finance']);
        $cashier = User::factory()->cashier()->create(['name' => 'Keep Cashier']);

        $this->actingAs($finance, 'finance')->from('/finance/settings')
            ->put('/finance/profile', ['name' => ''])
            ->assertRedirect('/finance/settings')
            ->assertSessionHasErrors('name');
        $this->actingAs($cashier, 'cashier')->from('/cashier/dashboard')
            ->put('/cashier/profile', ['name' => str_repeat('x', 300)])
            ->assertRedirect('/cashier/dashboard')
            ->assertSessionHasErrors('name');

        $this->assertDatabaseHas('users', ['id' => $finance->id, 'name' => 'Keep Finance']);
        $this->assertDatabaseHas('users', ['id' => $cashier->id, 'name' => 'Keep Cashier']);

        // The validation message is displayed next to the field after the redirect
        $this->actingAs($finance, 'finance')->from('/finance/settings')
            ->followingRedirects()->put('/finance/profile', ['name' => ''])
            ->assertSee('The name field is required.');
        $this->actingAs($cashier, 'cashier')->from('/cashier/dashboard')
            ->followingRedirects()->put('/cashier/profile', ['name' => ''])
            ->assertSee('The name field is required.');
    }

    public function test_admin_and_superadmin_can_change_their_name(): void
    {
        foreach ([
            ['admin',      '/admin/dashboard?section=settings',      '/admin/settings/profile'],
            ['superadmin', '/superadmin/dashboard?section=settings', '/superadmin/settings/profile'],
        ] as [$role, $page, $save]) {
            $user = User::factory()->{$role}()->create(['name' => "Old {$role} Name"]);

            $this->actingAs($user, 'web')
                ->from($page)
                ->put($save, ['name' => "New {$role} Name"])
                ->assertRedirect($page)
                ->assertSessionHas('profile_success', 'Name updated successfully.')
                ->assertSessionHas('settings_tab', 'account');

            $this->assertDatabaseHas('users', ['id' => $user->id, 'name' => "New {$role} Name"]);
            $this->assertDatabaseHas('activity_logs', ['description' => "Old {$role} Name changed their name to New {$role} Name"]);

            // After the redirect the page shows the success message and the new name in the field
            $this->actingAs($user->fresh(), 'web')
                ->withSession(['profile_success' => 'Name updated successfully.', 'settings_tab' => 'account'])
                ->get($page)
                ->assertOk()
                ->assertSee('Name updated successfully.')
                ->assertSee('value="New ' . $role . ' Name"', false);
        }
    }

    public function test_admin_and_superadmin_invalid_names_are_rejected_and_shown(): void
    {
        foreach ([
            ['admin',      '/admin/dashboard?section=settings',      '/admin/settings/profile'],
            ['superadmin', '/superadmin/dashboard?section=settings', '/superadmin/settings/profile'],
        ] as [$role, $page, $save]) {
            $user = User::factory()->{$role}()->create(['name' => "Keep {$role}"]);

            foreach (['', 'x', str_repeat('x', 300)] as $bad) {
                $this->actingAs($user, 'web')->from($page)
                    ->put($save, ['name' => $bad])
                    ->assertRedirect($page)
                    ->assertSessionHasErrorsIn('profile', 'name');
            }
            $this->assertDatabaseHas('users', ['id' => $user->id, 'name' => "Keep {$role}"]);

            $this->actingAs($user, 'web')->from($page)
                ->followingRedirects()->put($save, ['name' => ''])
                ->assertSee('The name field is required.');
        }
    }

    public function test_admin_cannot_use_the_superadmin_name_endpoint(): void
    {
        $admin = User::factory()->admin()->create(['name' => 'Plain Admin']);

        $this->actingAs($admin, 'web')
            ->put('/superadmin/settings/profile', ['name' => 'Escalated'])
            ->assertStatus(403);

        $this->assertDatabaseHas('users', ['id' => $admin->id, 'name' => 'Plain Admin']);
    }

    public function test_staff_cannot_rename_through_another_portal(): void
    {
        $cashier = User::factory()->cashier()->create(['name' => 'Cashier Only']);

        // A cashier session is not a finance session, so the Finance endpoint must refuse it
        $this->actingAs($cashier, 'cashier')
            ->put('/finance/profile', ['name' => 'Hijacked'])
            ->assertRedirect();

        $this->assertDatabaseHas('users', ['id' => $cashier->id, 'name' => 'Cashier Only']);
    }
}
