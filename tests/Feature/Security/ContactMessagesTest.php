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

    public function test_superadmin_has_the_same_inbox_and_actions(): void
    {
        ContactMessage::create(['name' => 'Maria', 'email' => 'm@example.com', 'subject' => 'Enrollment', 'message' => 'Hi', 'status' => 'unread']);
        $msg = ContactMessage::create(['name' => 'Pedro', 'email' => 'p@example.com', 'subject' => 'Fees', 'message' => 'Hi', 'status' => 'read']);
        $sa = User::factory()->create(['role' => 'superadmin']);

        $data = null;
        View::composer('superadmin_dashboard', function ($view) use (&$data) { $data = $view->getData(); });
        $html = $this->actingAs($sa, 'web')->get('/superadmin/dashboard')->assertOk()->getContent();
        $this->assertSame([2, 1, 1, 0], [$data['totalMessagesCount'], $data['unreadMessagesCount'], $data['readMessagesCount'], $data['repliedMessagesCount']]);

        // Same page, pop-up and script as Admin; sidebar link and top-bar envelope with the unread count
        foreach (['id="section-messages"', 'id="msgViewModal"', '/js/contact-messages.js?v=', 'id="nav-messages"', 'id="topbarMsgBadge"', "showSection('messages')"] as $needle) {
            $this->assertStringContainsString($needle, $html, $needle);
        }
        $this->assertMatchesRegularExpression('#id="topbarMsgBadge"[^>]*>1</span>#', $html, 'envelope shows 1 unread');

        $this->actingAs($sa, 'web')->patchJson("/admin/messages/{$msg->id}/status", ['status' => 'replied'])->assertOk();
        $this->assertSame('replied', $msg->fresh()->status);
        $this->actingAs($sa, 'web')->deleteJson("/admin/messages/{$msg->id}")->assertOk();
        $this->assertSame(1, ContactMessage::count());
    }

    public function test_student_portal_has_no_placeholder_search_or_bell(): void
    {
        $student = User::factory()->create(['role' => 'student']);
        $html = $this->actingAs($student, 'web')->get('/student/dashboard')->getContent();
        $this->assertStringNotContainsString('<input type="text" placeholder="Search...">', $html);
        $this->assertStringNotContainsString('<i class="bi bi-bell"></i><span class="notif-dot">2</span>', $html);
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
