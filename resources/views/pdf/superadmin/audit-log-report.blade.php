<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>{{ $reportTitle }}</title>
@include('pdf.shared._styles')
<style>
    table.fin-table td.ip { font-family: 'Courier New', monospace; font-size: 8.5px; }
</style>
</head>
<body>
    @include('pdf.shared._letterhead')

    <div class="fin-stats" style="margin-bottom:10px;">
        <div class="fin-stat-cell">
            <div class="fin-stat-value">{{ $logs->count() }}</div>
            <div class="fin-stat-label">Total Entries</div>
        </div>
    </div>

    <table class="fin-table">
        <thead>
            <tr>
                <th>Date &amp; Time</th>
                <th>Event Type</th>
                <th>Description</th>
                <th>User</th>
                <th>Role</th>
                <th>IP Address</th>
            </tr>
        </thead>
        <tbody>
            @forelse($logs as $log)
                <tr>
                    <td>{{ $log->created_at?->format('M d, Y h:i A') ?? '—' }}</td>
                    <td>{{ ucfirst(str_replace('_', ' ', $log->event_type)) }}</td>
                    <td>{{ $log->description }}</td>
                    <td>{{ $log->user_name ?? 'System' }}</td>
                    <td>{{ $log->user_role ? ucfirst($log->user_role) : '—' }}</td>
                    <td class="ip">{{ $log->ip_address ?? '—' }}</td>
                </tr>
            @empty
                <tr><td colspan="6" class="text-center">No activity recorded.</td></tr>
            @endforelse
        </tbody>
    </table>

    @include('pdf.shared._bottom')
</body>
</html>
