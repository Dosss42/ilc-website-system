<?php

namespace Tests\Feature\Security;

use App\Support\ScheduleGridBuilder;
use Tests\TestCase;

/**
 * Schedule PDFs show the Health Break / Lunch Break rows that fall inside the day being shown:
 * a morning-only timetable (Nursery, Kinder) gets the Health Break but no Lunch Break; a full day gets both.
 */
class ScheduleBreaksTest extends TestCase
{
    private function slot(string $start, string $end, string $day = 'Monday'): object
    {
        return (object) ['start_time' => "$start:00", 'end_time' => "$end:00", 'day_of_week' => $day];
    }

    private function rows(array $schedules): array
    {
        return collect(ScheduleGridBuilder::build(collect($schedules))['timeSlots'])
            ->map(fn($s) => substr($s['start'], 0, 5) . ($s['break'] ? ' ' . $s['break'] : ''))->values()->all();
    }

    public function test_morning_only_timetable_shows_the_health_break_but_no_lunch(): void
    {
        $this->assertSame(['07:00', '08:00', '09:00 Health Break', '09:20'],
            $this->rows([$this->slot('07:00', '08:00'), $this->slot('08:00', '09:00'), $this->slot('09:20', '10:20')]));
    }

    public function test_full_day_shows_both_breaks_in_order(): void
    {
        $this->assertSame(['07:00', '09:00 Health Break', '09:20', '11:20 Lunch Break', '12:00', '15:00'],
            $this->rows([$this->slot('07:00', '08:00'), $this->slot('09:20', '10:20'), $this->slot('12:00', '13:00'), $this->slot('15:00', '16:00')]));
    }

    public function test_no_break_rows_for_an_empty_timetable_or_a_class_held_in_a_break(): void
    {
        $this->assertSame([], $this->rows([]));
        $this->assertSame(['07:00', '09:00', '09:20'],
            $this->rows([$this->slot('07:00', '08:00'), $this->slot('09:00', '09:20'), $this->slot('09:20', '10:20')]), 'a class at 9:00 keeps its own row');
    }
}
