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
            <div class="fin-stat-value">{{ $stats['total'] }}</div>
            <div class="fin-stat-label">Total Users</div>
        </div>
        <div class="fin-stat-cell">
            <div class="fin-stat-value">{{ $stats['active'] }}</div>
            <div class="fin-stat-label">Active</div>
        </div>
        <div class="fin-stat-cell">
            <div class="fin-stat-value">{{ $stats['inactive'] }}</div>
            <div class="fin-stat-label">Inactive</div>
        </div>
    </div>

    <table class="fin-table">
        <thead>
            <tr>
                <th>Name</th>
                <th>Email</th>
                <th class="text-center">Role</th>
                <th class="text-center">Status</th>
                <th>Joined</th>
            </tr>
        </thead>
        <tbody>
            @forelse($users as $u)
                <tr>
                    <td>{{ $u->name }}</td>
                    <td>{{ $u->email }}</td>
                    <td class="text-center">{{ ucfirst($u->role) }}</td>
                    <td class="text-center">{{ $u->is_active ? 'Active' : 'Inactive' }}</td>
                    <td>{{ $u->created_at?->format('M d, Y') ?? '—' }}</td>
                </tr>
            @empty
                <tr><td colspan="5" class="text-center">No users found.</td></tr>
            @endforelse
        </tbody>
        <tfoot>
            <tr>
                <td colspan="4">Total Users</td>
                <td>{{ $stats['total'] }}</td>
            </tr>
        </tfoot>
    </table>

    @include('pdf.shared._bottom')
</body>
</html>
