<?php

namespace Tests\Feature\Security;

use App\Models\Announcement;
use App\Models\News;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Admin / Super Admin can star news and announcements as Highlights; the home page shows the
 * newest 5 public, active ones in its Highlights column next to the fixed cards.
 */
class HighlightsTest extends TestCase
{
    use RefreshDatabase;

    public function test_posting_with_the_checkbox_marks_it_as_a_highlight(): void
    {
        $admin = User::factory()->admin()->create();
        $this->actingAs($admin, 'web');

        $this->post('/admin/news', ['title' => 'Starred', 'body' => 'x', 'category' => 'general', 'is_highlight' => '1'])->assertRedirect();
        $this->post('/admin/news', ['title' => 'Plain', 'body' => 'x', 'category' => 'general'])->assertRedirect();
        $this->post('/admin/announcements', ['title' => 'Starred ann', 'content' => 'x', 'category' => 'general', 'audience' => 'all', 'is_highlight' => '1'])->assertRedirect();

        $this->assertTrue(News::where('title', 'Starred')->value('is_highlight'));
        $this->assertFalse(News::where('title', 'Plain')->value('is_highlight'));
        $this->assertTrue(Announcement::where('title', 'Starred ann')->value('is_highlight'));
    }

    public function test_admin_and_superadmin_can_toggle_and_others_cannot(): void
    {
        $news = News::create(['title' => 'N', 'body' => 'x', 'category' => 'general', 'is_active' => true]);
        $ann  = Announcement::create(['title' => 'A', 'content' => 'x', 'category' => 'general', 'audience' => 'all', 'is_active' => true]);

        $this->actingAs(User::factory()->admin()->create(), 'web')->post("/admin/news/{$news->id}/highlight")->assertRedirect();
        $this->assertTrue($news->fresh()->is_highlight);
        $this->post("/admin/announcements/{$ann->id}/highlight")->assertRedirect();
        $this->assertTrue($ann->fresh()->is_highlight);

        $this->actingAs(User::factory()->superadmin()->create(), 'web')->post("/superadmin/news/{$news->id}/highlight")->assertRedirect();
        $this->assertFalse($news->fresh()->is_highlight, 'second toggle removes it');
        $this->post("/superadmin/announcements/{$ann->id}/highlight")->assertRedirect();
        $this->assertFalse($ann->fresh()->is_highlight);

        $this->actingAs(User::factory()->teacher()->create(), 'web')->post("/admin/news/{$news->id}/highlight");
        $this->assertFalse($news->fresh()->is_highlight, 'a teacher cannot star posts');
    }

    public function test_home_page_shows_only_public_active_highlights_newest_five(): void
    {
        $mk = fn (string $title, array $extra, string $at) => tap(News::create($extra + ['title' => $title, 'body' => "Body of $title", 'category' => 'general', 'is_active' => true]), fn ($n) => $n->forceFill(['created_at' => $at])->save());
        $mk('Oldest highlight', ['is_highlight' => true], '2025-06-01');
        $mk('Old highlight', ['is_highlight' => true], '2026-01-01');
        $mk('Mid highlight', ['is_highlight' => true], '2026-05-01');
        $mk('New highlight', ['is_highlight' => true], '2026-10-01');
        $mk('Hidden highlight', ['is_highlight' => true, 'is_active' => false], '2026-10-02');
        $mk('Not starred', [], '2026-10-03');
        $ann = fn (string $title, string $audience, string $at) => tap(Announcement::create(['title' => $title, 'content' => 'x', 'category' => 'general', 'audience' => $audience, 'is_active' => true, 'is_highlight' => true]), fn ($a) => $a->forceFill(['created_at' => $at])->save());
        $ann('Teachers only star', 'teachers', '2026-10-09');
        $ann('Public star', 'all', '2026-10-05');
        $ann('Public star 2', 'all', '2026-10-06');

        $titles = $this->get('/')->assertOk()->viewData('highlightPosts')->pluck('title')->all();

        $this->assertSame(['Public star 2', 'Public star', 'New highlight', 'Mid highlight', 'Old highlight'], $titles,
            'newest 5 across news and announcements; hidden, unstarred, non-public and the 6th are left out');
    }
}
