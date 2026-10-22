<?php

namespace Tests\Feature\Security;

use App\Models\Enrollment;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Pagination\LengthAwarePaginator;
use Tests\TestCase;

/**
 * Pagers: at most 10 numbered buttons everywhere (partials/pagination.blade.php),
 * and Assessment & Promotion's pager actually changes page (its links used
 * ?page=N while the controller read ?assess_page=N, so every click showed page 1).
 */
class PaginationTest extends TestCase
{
    use RefreshDatabase;

    private function buttons(int $current, int $last): array
    {
        $html = (string) (new LengthAwarePaginator(range(1, 15), $last * 15, 15, $current, ['path' => '/x']))->links();
        preg_match_all('/class="page-link"[^>]*>([^<]+)</', $html, $m);
        return array_values(array_filter(array_map('trim', $m[1]), fn ($t) => !in_array($t, ['Previous', 'Next'], true)));
    }

    public function test_never_more_than_ten_page_numbers_and_always_first_last_and_current(): void
    {
        foreach ([1, 7, 10, 11, 17, 44] as $last) {
            for ($cur = 1; $cur <= $last; $cur++) {
                $nums = array_values(array_filter($this->buttons($cur, $last), 'is_numeric'));
                $this->assertLessThanOrEqual(10, count($nums), "last=$last cur=$cur");
                $this->assertContains((string) $cur, $nums);
                $this->assertContains('1', $nums);
                $this->assertContains((string) $last, $nums);
            }
        }
        $this->assertSame(['1', '2', '3', '4', '5', '6', '7', '8', '9', '…', '17'], $this->buttons(1, 17));
        $this->assertSame(['1', '…', '6', '7', '8', '9', '10', '11', '12', '13', '…', '17'], $this->buttons(9, 17));
        $this->assertSame(['1', '…', '9', '10', '11', '12', '13', '14', '15', '16', '17'], $this->buttons(17, 17));
        $this->assertSame(['1', '2', '3'], $this->buttons(2, 3));
    }

    public function test_assessment_pager_links_open_that_page(): void
    {
        Setting::set('current_school_year', '2026-2027');
        $admin = User::factory()->admin()->create();
        Enrollment::factory()->count(20)->create(['grade_level' => 'grade1']); // 2 pages of 15

        $page1 = $this->actingAs($admin, 'web')->get('/admin/dashboard?section=assessment')->assertOk();
        $paginator = $page1->viewData('assessStudents');
        $this->assertStringContainsString('assess_page=2', $paginator->url(2));

        $page2 = $this->actingAs($admin, 'web')->get('/admin/dashboard?section=assessment&assess_page=2')->assertOk()->viewData('assessStudents');
        $this->assertSame(2, $page2->currentPage());
        $this->assertCount(5, $page2->items());
        $this->assertEmpty(array_intersect(collect($paginator->items())->pluck('id')->all(), collect($page2->items())->pluck('id')->all()));
    }
}
