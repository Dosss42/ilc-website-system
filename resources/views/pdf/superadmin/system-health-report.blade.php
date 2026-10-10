<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>{{ $reportTitle }}</title>
@include('pdf.shared._styles')
<style>
    .sh-section-title { font-size: 11px; font-weight: bold; color: #1a3a6c; text-transform: uppercase; letter-spacing: 0.5px; margin: 16px 0 6px; padding-bottom: 4px; border-bottom: 1.5px solid #1a3a6c; }
    .sh-section-title:first-of-type { margin-top: 0; }
</style>
</head>
<body>
    @include('pdf.shared._letterhead')

    <div class="fin-stats">
        <div class="fin-stat-cell">
            <div class="fin-stat-value">{{ $dbSize }}</div>
            <div class="fin-stat-label">Database Size</div>
        </div>
        <div class="fin-stat-cell">
            <div class="fin-stat-value">{{ $dbPingMs }}ms</div>
            <div class="fin-stat-label">DB Response Time</div>
        </div>
        <div class="fin-stat-cell">
            <div class="fin-stat-value">{{ $diskUsedPct !== null ? $diskUsedPct . '%' : '—' }}</div>
            <div class="fin-stat-label">Disk Used</div>
        </div>
        <div class="fin-stat-cell">
            <div class="fin-stat-value">{{ count($backups) }}</div>
            <div class="fin-stat-label">Backups on File</div>
        </div>
    </div>

    <div class="sh-section-title">System Information</div>
    <table class="fin-table">
        <tbody>
            <tr><td>Database Name</td><td class="text-right">{{ $dbName }}</td></tr>
            <tr><td>Database Size</td><td class="text-right">{{ $dbSize }}</td></tr>
            <tr><td>Database Response Time</td><td class="text-right">{{ $dbPingMs }}ms</td></tr>
            <tr><td>Disk Free Space</td><td class="text-right">{{ $diskFree }}</td></tr>
            <tr><td>Disk Total Space</td><td class="text-right">{{ $diskTotal }}</td></tr>
            <tr><td>PHP Version</td><td class="text-right">{{ $phpVersion }}</td></tr>
            <tr><td>Laravel Version</td><td class="text-right">{{ $laravelVersion }}</td></tr>
        </tbody>
    </table>

    <div class="sh-section-title">Record Counts</div>
    <table class="fin-table">
        <thead>
            <tr>
                <th>Table</th>
                <th class="text-right">Records</th>
            </tr>
        </thead>
        <tbody>
            @foreach($recordCounts as $label => $count)
                <tr>
                    <td>{{ $label }}</td>
                    <td class="text-right">{{ number_format($count) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="sh-section-title">Backups</div>
    <table class="fin-table">
        <thead>
            <tr>
                <th>Backup File</th>
                <th>Created</th>
                <th class="text-right">Size</th>
            </tr>
        </thead>
        <tbody>
            @forelse($backups as $b)
                <tr>
                    <td>{{ $b['name'] }}</td>
                    <td>{{ $b['created'] }}</td>
                    <td class="text-right">{{ $b['size'] }}</td>
                </tr>
            @empty
                <tr><td colspan="3" class="text-center">No backups on file.</td></tr>
            @endforelse
        </tbody>
    </table>
    <table class="fin-table" style="margin-top:0;">
        <tbody>
            <tr><td>Last Backup</td><td class="text-right">{{ $lastBackup['created'] ?? 'Never' }}</td></tr>
            <tr><td>Local Retention</td><td class="text-right">{{ $backupRetention }} days</td></tr>
            <tr><td>Off-Site Copy Configured</td><td class="text-right">{{ $offsiteConfigured ? 'Yes' : 'No' }}</td></tr>
        </tbody>
    </table>

    @include('pdf.shared._bottom')
</body>
</html>
