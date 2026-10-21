<?php

namespace App\Support;

/**
 * Groups a flat Schedule collection into the day/time grid every schedule
 * screen in this app builds — previously duplicated as an identical @php
 * block in teacherDashboard.blade.php (and similar ad-hoc JS versions in
 * the admin/student portals). Pulled out so the PDF versions of all three
 * schedules (pdf.schedule) render from the exact same shape and colors the
 * on-screen tables already use, instead of drifting into a different look.
 */
class ScheduleGridBuilder
{
    public const DAY_COLORS = [
        'Monday'    => ['bg' => '#1565c0', 'light' => '#e3f0ff'],
        'Tuesday'   => ['bg' => '#6a1b9a', 'light' => '#f3e5ff'],
        'Wednesday' => ['bg' => '#1b5e20', 'light' => '#e6f4ea'],
        'Thursday'  => ['bg' => '#e65100', 'light' => '#fff3e0'],
        'Friday'    => ['bg' => '#b71c1c', 'light' => '#fdecea'],
        'Saturday'  => ['bg' => '#37474f', 'light' => '#eceff1'],
    ];

    public const DAY_SHORT = [
        'Monday' => 'Mon', 'Tuesday' => 'Tue', 'Wednesday' => 'Wed',
        'Thursday' => 'Thu', 'Friday' => 'Fri', 'Saturday' => 'Sat',
    ];

    /**
     * The school's daily periods — same slots as the Admin/Super Admin
     * schedule grids (gradeTimeSlots / schedTimeSlots in those views).
     * 'break' rows span the whole week and never hold a class.
     */
    public const STANDARD_SLOTS = [
        ['start' => '07:00', 'end' => '08:00'],
        ['start' => '08:00', 'end' => '09:00'],
        ['start' => '09:00', 'end' => '09:20', 'break' => 'Health Break'],
        ['start' => '09:20', 'end' => '10:20'],
        ['start' => '10:20', 'end' => '11:20'],
        ['start' => '11:20', 'end' => '12:00', 'break' => 'Lunch Break'],
        ['start' => '12:00', 'end' => '13:00'],
        ['start' => '13:00', 'end' => '14:00'],
    ];

    /**
     * @param \Illuminate\Support\Collection $schedules Schedule rows, each
     *   with start_time/end_time/day_of_week and whatever relations
     *   (subject/section/teacher) the caller eager-loaded — the view only
     *   shows a relation if it's actually loaded, so callers control what
     *   each cell displays just by choosing what to eager-load.
     * @param bool $withStandardSlots Always include every STANDARD_SLOTS row
     *   (empty if nothing is scheduled there), so a timetable with few or no
     *   classes still shows the full school day. Classes at non-standard
     *   times still get their own row.
     */
    public static function build($schedules, bool $withStandardSlots = false): array
    {
        $usedDays = $schedules->pluck('day_of_week')->unique()->toArray();
        $showDays = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday'];
        if (in_array('Saturday', $usedDays, true)) {
            $showDays[] = 'Saturday';
        }

        $timeSlots = [];
        if ($withStandardSlots) {
            foreach (self::STANDARD_SLOTS as $slot) {
                $timeSlots[$slot['start'] . '-' . $slot['end']] = [
                    'start' => $slot['start'] . ':00',
                    'end'   => $slot['end'] . ':00',
                    'days'  => [],
                    'break' => $slot['break'] ?? null,
                ];
            }
        }
        foreach ($schedules as $sched) {
            $timeKey = substr($sched->start_time, 0, 5) . '-' . substr($sched->end_time, 0, 5);
            if (!isset($timeSlots[$timeKey])) {
                $timeSlots[$timeKey] = ['start' => $sched->start_time, 'end' => $sched->end_time, 'days' => [], 'break' => null];
            }
            // A class booked into a break slot turns it into a normal row so it still shows
            $timeSlots[$timeKey]['break'] = null;
            $timeSlots[$timeKey]['days'][$sched->day_of_week] = $sched;
        }
        ksort($timeSlots);

        return ['timeSlots' => $timeSlots, 'showDays' => $showDays];
    }
}
