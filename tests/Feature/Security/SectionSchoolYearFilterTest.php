<?php

namespace Tests\Feature\Security;

use App\Models\Enrollment;
use App\Models\Section;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Section Management > School Year filter (?section_sy=). Lets the admin view and
 * create next year's sections (e.g. for promoted students) separately from this
 * year's, with stat cards counting the whole filtered year.
 */
class SectionSchoolYearFilterTest extends TestCase
{
    use RefreshDatabase;

    private function section(string $name, string $sy, bool $active = true): Section
    {
        return Section::create([
            'name' => $name, 'grade_level' => 'grade2', 'max_students' => 30,
            'school_year' => $sy, 'is_active' => $active,
        ]);
    }

    public function test_defaults_to_the_current_school_year_and_can_switch_years(): void
    {
        // Configured explicitly, as in Admin > Settings > Academic (otherwise the
        // current year falls back to the newest active section's year)
        $current = '2026-2027';
        $next    = '2027-2028';
        Setting::set('current_school_year', $current);

        $admin = User::factory()->admin()->create();
        $this->section('ThisYear A', $current);
        $this->section('ThisYear B', $current, false);
        $nextSec = $this->section('NextYear A', $next);
        $nextSec->students()->attach(Enrollment::factory()->create(['grade_level' => 'grade2', 'school_year' => $next])->user_id);

        // Default: current year only
        $view = $this->actingAs($admin, 'web')->get('/admin/dashboard')->assertOk()->viewData('sections');
        $this->assertEqualsCanonicalizing(['ThisYear A', 'ThisYear B'], collect($view->items())->pluck('name')->all());
        $this->assertSame(['total' => 2, 'active' => 1, 'enrolled' => 0],
            $this->get('/admin/dashboard')->viewData('sectionStats'));

        // Next year's view: its own sections and stats, pagination keeps the filter
        $res = $this->get('/admin/dashboard?section=sections&section_sy=' . $next)->assertOk();
        $this->assertSame(['NextYear A'], collect($res->viewData('sections')->items())->pluck('name')->all());
        $this->assertSame(['total' => 1, 'active' => 1, 'enrolled' => 1], $res->viewData('sectionStats'));
        $this->assertStringContainsString('section_sy=' . $next, $res->viewData('sections')->url(2));
        $res->assertSee('Sections — S.Y. ' . $next);

        // All years
        $this->assertSame(3, $this->get('/admin/dashboard?section_sy=all')->viewData('sections')->total());

        // A year with no sections shows the per-year empty state
        $this->get('/admin/dashboard?section_sy=2030-2031')->assertOk()
            ->assertSee('No sections for S.Y. 2030-2031 yet');

        // Garbage falls back to the current year
        $this->assertSame($current, $this->get('/admin/dashboard?section_sy=not-a-year')->viewData('sectionSyFilter'));
    }
}
