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
     * @param \Illuminate\Support\Collection $schedules Schedule rows, each
     *   with start_time/end_time/day_of_week and whatever relations
     *   (subject/section/teacher) the caller eager-loaded — the view only
     *   shows a relation if it's actually loaded, so callers control what
     *   each cell displays just by choosing what to eager-load.
     */
    public static function build($schedules): array
    {
        $usedDays = $schedules->pluck('day_of_week')->unique()->toArray();
        $showDays = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday'];
        if (in_array('Saturday', $usedDays, true)) {
            $showDays[] = 'Saturday';
        }

        $timeSlots = [];
        foreach ($schedules as $sched) {
            $timeKey = substr($sched->start_time, 0, 5) . '-' . substr($sched->end_time, 0, 5);
            if (!isset($timeSlots[$timeKey])) {
                $timeSlots[$timeKey] = ['start' => $sched->start_time, 'end' => $sched->end_time, 'days' => []];
            }
            $timeSlots[$timeKey]['days'][$sched->day_of_week] = $sched;
        }
        ksort($timeSlots);

        return ['timeSlots' => $timeSlots, 'showDays' => $showDays];
    }
}
