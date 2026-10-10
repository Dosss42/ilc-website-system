<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>{{ $reportTitle }}</title>
@include('pdf.shared._styles')
</head>
<body>
    @include('pdf.shared._letterhead')

    <div class="fin-stats">
        <div class="fin-stat-cell">
            <div class="fin-stat-value">{{ $approvedDocs }}</div>
            <div class="fin-stat-label">Approved</div>
        </div>
        <div class="fin-stat-cell">
            <div class="fin-stat-value">{{ $pendingDocs }}</div>
            <div class="fin-stat-label">Pending Review</div>
        </div>
        <div class="fin-stat-cell">
            <div class="fin-stat-value">{{ $rejectedDocs }}</div>
            <div class="fin-stat-label">Rejected</div>
        </div>
        <div class="fin-stat-cell">
            <div class="fin-stat-value">{{ $totalDocs }}</div>
            <div class="fin-stat-label">Total Documents</div>
        </div>
    </div>

    <table class="fin-table">
        <thead>
            <tr>
                <th>Student</th>
                <th>Document Type</th>
                <th class="text-center">Status</th>
                <th>Submitted</th>
            </tr>
        </thead>
        <tbody>
            @forelse($documents as $doc)
                <tr>
                    <td>{{ $doc->user->name ?? '—' }}</td>
                    <td>{{ ucwords(str_replace('_', ' ', $doc->document_type ?? '')) }}</td>
                    <td class="text-center">{{ ucfirst($doc->status ?? '—') }}</td>
                    <td>{{ $doc->created_at ? $doc->created_at->format('M j, Y') : '—' }}</td>
                </tr>
            @empty
                <tr><td colspan="4" class="text-center">No document records found.</td></tr>
            @endforelse
        </tbody>
    </table>

    @include('pdf.shared._bottom')
</body>
</html>
