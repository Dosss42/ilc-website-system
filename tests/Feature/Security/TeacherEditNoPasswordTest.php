<?php

namespace Tests\Feature\Security;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

/**
 * Edit Teacher (Admin and Super Admin, PUT /admin/teachers/{id}) changes name, email and status only.
 * A teacher's password is theirs: Settings (OTP) or "Forgot password?"; the Super Admin can reset it in User Management.
 */
class TeacherEditNoPasswordTest extends TestCase
{
    use RefreshDatabase;

    public function test_edit_teacher_never_changes_the_password(): void
    {
        $teacher = User::factory()->teacher()->create(['name' => 'Ana Reyes', 'email' => 'ana.reyes@gmail.com', 'password' => Hash::make('Original#123')]);

        foreach ([User::factory()->admin()->create(), User::factory()->superadmin()->create()] as $editor) {
            $this->actingAs($editor, 'web')->putJson("/admin/teachers/{$teacher->id}", [
                'name' => 'Ana R. Santos', 'email' => 'ana.reyes@gmail.com', 'is_active' => true, 'password' => 'Hacked#9999',
            ])->assertOk();
            $this->assertTrue(Hash::check('Original#123', $teacher->fresh()->password), $editor->role . ' cannot set a teacher password here');
        }
        $this->assertSame('Ana R. Santos', $teacher->fresh()->name, 'the rest of the edit still saves');
    }

    /** Like "Sample Teacher" (teacher@ilc.edu.ph): the saved email's domain doesn't exist on the internet. */
    public function test_renaming_works_even_if_the_saved_email_domain_does_not_exist(): void
    {
        $teacher = User::factory()->teacher()->create(['name' => 'Sample Teacher', 'email' => 'teacher@ilc.edu.ph']);
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin, 'web')->putJson("/admin/teachers/{$teacher->id}", ['name' => 'Sample Teacher Renamed', 'email' => 'teacher@ilc.edu.ph', 'is_active' => true])->assertOk();
        $this->assertSame('Sample Teacher Renamed', $teacher->fresh()->name, 'unchanged email is not domain-checked');

        // A NEW email still has to have a real domain
        $this->actingAs($admin, 'web')->putJson("/admin/teachers/{$teacher->id}", ['name' => 'Sample Teacher Renamed', 'email' => 'someone@no-such-domain-ilc.invalid', 'is_active' => true])
            ->assertStatus(422)->assertJsonPath('errors.email.0', 'That email address is not valid, or its domain does not exist (check the spelling after the @).');
        $this->assertSame('teacher@ilc.edu.ph', $teacher->fresh()->email);

        // Same rule in the teacher's own Settings
        $this->actingAs($teacher->fresh(), 'web')->from('/teacher/dashboard')->put('/teacher/settings/profile', ['name' => 'Sample T.', 'email' => 'teacher@ilc.edu.ph'])
            ->assertSessionHasNoErrors();
        $this->assertSame('Sample T.', $teacher->fresh()->name);
    }

    public function test_edit_teacher_window_has_no_password_field(): void
    {
        $html = $this->actingAs(User::factory()->admin()->create(), 'web')->get('/admin/dashboard')->assertOk()->getContent();
        $this->assertStringNotContainsString('id="teacher-password"', $html);
        $sa = $this->actingAs(User::factory()->superadmin()->create(), 'web')->get('/superadmin/dashboard')->assertOk()->getContent();
        $this->assertStringNotContainsString('id="teacher-password"', $sa);
    }
}
