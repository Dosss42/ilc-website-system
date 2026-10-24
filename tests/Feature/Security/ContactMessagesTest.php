<?php

namespace Tests\Feature\Security;

use App\Models\ContactMessage;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\View;
use Tests\TestCase;

/**
 * The website's Contact form feeds the Admin "Contact Messages" inbox:
 * sent → unread (counted in the badges) → read / replied → deleted. Only staff can change them.
 */
class ContactMessagesTest extends TestCase
{
    use RefreshDatabase;

    public function test_contact_form_message_reaches_the_admin_inbox(): void
    {
        $this->post('/contact/send', ['name' => 'Maria Santos', 'email' => 'maria@example.com', 'phone' => '09170000000',
            'subject' => 'Enrollment', 'message' => 'When does enrollment start?'])->assertSessionHas('success');

        $msg = ContactMessage::sole();
        $this->assertSame(['Maria Santos', 'unread'], [$msg->name, $msg->status]);

        $data = null;
        View::composer('adminDashboard', function ($view) use (&$data) { $data = $view->getData(); });
        $admin = User::factory()->admin()->create();
        $this->actingAs($admin, 'web')->get('/admin/dashboard')->assertOk()->assertSee('Enrollment');
        $this->assertSame([1, 1, 0, 0], [$data['totalMessagesCount'], $data['unreadMessagesCount'], $data['readMessagesCount'], $data['repliedMessagesCount']]);
    }

    public function test_admin_marks_and_deletes_messages(): void
    {
        $msg = ContactMessage::create(['name' => 'Pedro', 'email' => 'p@example.com', 'subject' => 'Fees', 'message' => 'Hi', 'status' => 'unread']);
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin, 'web')->patchJson("/admin/messages/{$msg->id}/status", ['status' => 'replied'])->assertOk()->assertJson(['success' => true]);
        $this->assertSame('replied', $msg->fresh()->status);
        $this->actingAs($admin, 'web')->patchJson("/admin/messages/{$msg->id}/status", ['status' => 'archived'])->assertStatus(422);

        $this->actingAs($admin, 'web')->deleteJson("/admin/messages/{$msg->id}")->assertOk();
        $this->assertSame(0, ContactMessage::count());
    }

    public function test_non_staff_cannot_change_messages(): void
    {
        $msg = ContactMessage::create(['name' => 'Pedro', 'email' => 'p@example.com', 'subject' => 'Fees', 'message' => 'Hi', 'status' => 'unread']);
        $teacher = User::factory()->teacher()->create();

        $this->actingAs($teacher, 'web')->patchJson("/admin/messages/{$msg->id}/status", ['status' => 'read']);
        $this->actingAs($teacher, 'web')->deleteJson("/admin/messages/{$msg->id}");
        $this->assertSame('unread', $msg->fresh()->status);
    }
}
