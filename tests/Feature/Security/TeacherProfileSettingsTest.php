<?php

namespace Tests\Feature\Security;

use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

/**
 * Teacher Settings → Account Information: the teacher edits their own name and email.
 * Changing the email (the login) needs the current password; the top bar has no placeholder
 * bell/envelope, and the user chip shows the uploaded profile photo.
 */
class TeacherProfileSettingsTest extends TestCase
{
    use RefreshDatabase;

    private User $teacher;

    protected function setUp(): void
    {
        parent::setUp();
        $this->teacher = User::factory()->teacher()->create(['name' => 'Ana Reyes', 'email' => 'ana.reyes@gmail.com', 'password' => Hash::make('Secret#123')]);
    }

    private function save(array $data)
    {
        return $this->actingAs($this->teacher, 'web')->from('/teacher/dashboard')->put('/teacher/settings/profile', $data);
    }

    public function test_name_change_needs_no_password_and_is_logged(): void
    {
        $this->save(['name' => 'Ana R. Santos', 'email' => 'ana.reyes@gmail.com'])
            ->assertRedirect('/teacher/dashboard')->assertSessionHas('profile_success', 'Name updated successfully.');
        $this->assertSame('Ana R. Santos', $this->teacher->fresh()->name);
        $this->assertTrue(ActivityLog::where('description', 'Ana Reyes changed their name to Ana R. Santos')->exists());
    }

    public function test_email_change_needs_the_current_password(): void
    {
        $this->save(['name' => 'Ana Reyes', 'email' => 'ana.santos@gmail.com'])->assertSessionHasErrors('profile_password', null, 'profile');
        $this->save(['name' => 'Ana Reyes', 'email' => 'ana.santos@gmail.com', 'profile_password' => 'wrong'])->assertSessionHasErrors('profile_password', null, 'profile');
        $this->assertSame('ana.reyes@gmail.com', $this->teacher->fresh()->email, 'unchanged without the right password');

        $this->save(['name' => 'Ana Reyes', 'email' => 'ana.santos@gmail.com', 'profile_password' => 'Secret#123'])
            ->assertSessionHasNoErrors()->assertSessionHas('profile_success', 'Email updated. Use your new email the next time you log in.');
        $this->assertSame('ana.santos@gmail.com', $this->teacher->fresh()->email);
        $this->assertTrue(ActivityLog::where('description', 'Ana Reyes changed their email from ana.reyes@gmail.com to ana.santos@gmail.com')->exists());
    }

    public function test_email_must_be_valid_and_not_taken(): void
    {
        User::factory()->create(['email' => 'taken@gmail.com']);
        $this->save(['name' => 'Ana Reyes', 'email' => 'taken@gmail.com', 'profile_password' => 'Secret#123'])->assertSessionHasErrors('email', null, 'profile');
        $this->save(['name' => 'Ana Reyes', 'email' => 'not-an-email', 'profile_password' => 'Secret#123'])->assertSessionHasErrors('email', null, 'profile');
        $this->save(['name' => 'A', 'email' => 'ana.reyes@gmail.com'])->assertSessionHasErrors('name', null, 'profile');
        $this->assertSame(['Ana Reyes', 'ana.reyes@gmail.com'], [$this->teacher->fresh()->name, $this->teacher->fresh()->email]);
    }

    public function test_only_teachers_can_use_it(): void
    {
        $student = User::factory()->create(['role' => 'student', 'email' => 'kid@gmail.com']);
        $this->actingAs($student, 'web')->put('/teacher/settings/profile', ['name' => 'Hacked', 'email' => 'kid@gmail.com']);
        $this->assertNotSame('Hacked', $student->fresh()->name);
    }

    public function test_dashboard_has_the_form_no_placeholder_icons_and_the_photo_in_the_chip(): void
    {
        $this->teacher->update(['profile_photo' => 'teacher-photos/ana.jpg']);
        $html = $this->actingAs($this->teacher, 'web')->get('/teacher/dashboard')->assertOk()->getContent();

        $this->assertStringContainsString('id="teacher-profile-form"', $html);
        $this->assertStringNotContainsString('class="bi bi-bell"', $html, 'placeholder bell hidden');
        $this->assertStringNotContainsString('<span class="notif-dot">2</span>', $html, 'placeholder "2" hidden');
        $this->assertMatchesRegularExpression('#<div class="user-avatar">\s*<img src="[^"]*storage/teacher-photos/ana\.jpg"#', $html, 'photo in the top-bar chip');
    }
}
