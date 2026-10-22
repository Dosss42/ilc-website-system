<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>{{ $reportTitle }}</title>
@include('pdf.shared._styles')
<style>
    table.fin-table td.ip { font-size: 8.5px; }
    table.fin-table td.when { white-space: nowrap; }
</style>
</head>
<body>
    @include('pdf.shared._letterhead')

    <div class="fin-stats" style="margin-bottom:10px;">
        <div class="fin-stat-cell">
            <div class="fin-stat-value">{{ number_format($logs->count()) }}</div>
            <div class="fin-stat-label">Total Entries</div>
        </div>
    </div>

    @if(($matching ?? 0) > $logs->count())
        <div style="border:1px solid #f5c26b; background:#fff8ec; color:#7a4a00; padding:7px 10px; font-size:9.5px; margin-bottom:10px;">
            Showing the newest {{ number_format($logs->count()) }} of {{ number_format($matching) }} matching logs (the most one PDF can hold).
            Use the date filters to export the older ones.
        </div>
    @endif

    {{-- Blocks of 100 rows, not one big table: Dompdf's memory for a single table grows with
         every row and ran out at about 1,500 logs. Fixed column widths keep the blocks aligned. --}}
    @forelse($logs->chunk(100) as $block)
    <table class="fin-table" style="table-layout:fixed;{{ $loop->last ? '' : 'margin-bottom:0;' }}">
        <thead>
            <tr>
                <th style="width:14%;">Date &amp; Time</th>
                <th style="width:14%;">Event Type</th>
                <th style="width:36%;">Description</th>
                <th style="width:14%;">User</th>
                <th style="width:10%;">Role</th>
                <th style="width:12%;">IP Address</th>
            </tr>
        </thead>
        <tbody>
            @foreach($block as $log)
                <tr>
                    <td class="when">{{ $log->created_at?->format('M d, Y') ?? '—' }}<br>{{ $log->created_at?->format('h:i A') }}</td>
                    <td>{{ \App\Models\ActivityLog::eventLabel($log->event_type) }}</td>
                    <td>{{ $log->description }}</td>
                    <td>{{ $log->user_name ?? 'System' }}</td>
                    <td>{{ $log->user_role ? (\App\Models\ActivityLog::ROLES[$log->user_role] ?? ucfirst($log->user_role)) : '—' }}</td>
                    <td class="ip">{{ $log->ip_address ?? '—' }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
    @empty
    <table class="fin-table">
        <tbody><tr><td class="text-center">No activity recorded.</td></tr></tbody>
    </table>
    @endforelse

    @include('pdf.shared._bottom')
</body>
</html>
