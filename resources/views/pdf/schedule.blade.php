<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>{{ $reportTitle }}</title>
@include('pdf.shared._styles')
<style>
    {{-- Day-grid timetable — same color-per-day scheme the on-screen
         teacher dashboard already defines ($dayColors in
         teacherDashboard.blade.php, now shared via
         App\Support\ScheduleGridBuilder) so the PDF matches what every
         portal already shows on screen instead of inventing a new look. --}}
    table.sched-grid { width: 100%; border-collapse: collapse; margin-bottom: 14px; table-layout: fixed; }
    table.sched-grid th.sched-time-col,
    table.sched-grid td.sched-time-col { width: 13%; }
    table.sched-grid th { color: #fff; font-size: 9px; text-transform: uppercase; padding: 7px 6px; text-align: center; border: 1px solid rgba(255,255,255,0.35); }
    table.sched-grid th.sched-time-col { background: #1a3a6c; }
    table.sched-grid td { border: 1px solid #ddd; padding: 5px 6px; font-size: 8.5px; vertical-align: middle; text-align: center; }
    table.sched-grid td.sched-time-col { background: #f7f9fc; font-weight: bold; color: #333; white-space: nowrap; }
    table.sched-grid td.sched-empty { color: #ccc; }
    .sched-subject { font-weight: bold; color: #1a3a6c; font-size: 9px; }
    .sched-code { display: inline-block; background: #f0f4ff; color: #1a3a6c; border-radius: 3px; padding: 0 4px; font-size: 7.5px; font-weight: bold; margin-top: 1px; }
    .sched-meta { font-size: 7.5px; color: #555; margin-top: 2px; }
</style>
</head>
<body>
    @include('pdf.shared._letterhead')

    <table class="sched-grid">
        <thead>
            <tr>
                <th class="sched-time-col">Time</th>
                @foreach($showDays as $day)
                    <th style="background: {{ \App\Support\ScheduleGridBuilder::DAY_COLORS[$day]['bg'] ?? '#1a3a6c' }};">
                        {{ \App\Support\ScheduleGridBuilder::DAY_SHORT[$day] ?? $day }}
                    </th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            @forelse($timeSlots as $slot)
                @if(!empty($slot['break']))
                <tr>
                    <td class="sched-time-col">
                        {{ \Carbon\Carbon::parse($slot['start'])->format('g:i A') }}<br>–<br>{{ \Carbon\Carbon::parse($slot['end'])->format('g:i A') }}
                    </td>
                    <td colspan="{{ count($showDays) }}" style="text-align:center;background:#fdf8ec;color:#8a6d1f;font-weight:bold;letter-spacing:1px;">
                        {{ $slot['break'] }}
                    </td>
                </tr>
                @continue
                @endif
                <tr>
                    <td class="sched-time-col">
                        {{ \Carbon\Carbon::parse($slot['start'])->format('g:i A') }}<br>–<br>{{ \Carbon\Carbon::parse($slot['end'])->format('g:i A') }}
                    </td>
                    @foreach($showDays as $day)
                        @php $cell = $slot['days'][$day] ?? null; @endphp
                        <td style="{{ $cell ? 'background:' . (\App\Support\ScheduleGridBuilder::DAY_COLORS[$day]['light'] ?? '#f7f9fc') . ';' : '' }}">
                            @if($cell)
                                <div class="sched-subject">{{ $cell->subject->name ?? 'All Subjects' }}</div>
                                @if($cell->subject && $cell->subject->code)
                                    <div><span class="sched-code">{{ $cell->subject->code }}</span></div>
                                @endif
                                <div class="sched-meta">
                                    @if($cell->relationLoaded('section') && $cell->section){{ $cell->section->name }}@endif
                                    @if($cell->relationLoaded('teacher') && $cell->teacher)
                                        @if($cell->relationLoaded('section') && $cell->section) &middot; @endif
                                        {{ $cell->teacher->name }}
                                    @endif
                                    @if($cell->room)
                                        @if(($cell->relationLoaded('section') && $cell->section) || ($cell->relationLoaded('teacher') && $cell->teacher)) &middot; @endif
                                        {{ $cell->room }}
                                    @endif
                                </div>
                            @else
                                <span class="sched-empty">—</span>
                            @endif
                        </td>
                    @endforeach
                </tr>
            @empty
                <tr>
                    <td class="sched-time-col">—</td>
                    @foreach($showDays as $day)
                        <td class="sched-empty">—</td>
                    @endforeach
                </tr>
            @endforelse
        </tbody>
    </table>

    @if(!empty($unscheduledSubjects) && count($unscheduledSubjects))
        <div style="margin-top:4px;font-size:9px;font-weight:bold;color:#555;">Not Yet Scheduled</div>
        <table style="width:100%;border-collapse:collapse;margin-top:4px;">
            @foreach($unscheduledSubjects as $sub)
                <tr>
                    <td style="border:1px solid #ddd;padding:4px 6px;font-size:8.5px;width:70%;">{{ $sub->name }}</td>
                    <td style="border:1px solid #ddd;padding:4px 6px;font-size:8.5px;color:#888;">{{ $sub->code ?? '—' }}</td>
                </tr>
            @endforeach
        </table>
    @endif

    @include('pdf.shared._bottom')
</body>
</html>
