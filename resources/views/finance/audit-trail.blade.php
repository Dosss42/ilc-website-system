@extends('finance.layout')

@section('title', 'Audit Trail')

@section('styles')
<style>
    .status-badge { display:inline-flex;align-items:center;gap:6px;padding:5px 12px;border-radius:20px;font-size:11px;font-weight:600;white-space:nowrap; }
    .status-badge.success { background:#e8f5e9;color:#2e7d32; }
    .status-badge.danger  { background:#ffebee;color:#c62828; }
    .status-badge.active  { background:#e8f0fb;color:var(--blue); }
    .status-badge.primary { background:#eff6ff;color:#2471a3; }
</style>
@endsection

@section('content')
<div class="section-header">
    <div>
        <h1><i class="bi bi-journal-check" style="color:var(--gold);"></i> Audit Trail</h1>
        <p>A record of payments and actions you've performed — for your own reference and accountability.</p>
    </div>
</div>

<div class="content-card">
    <div class="card-header">
        <div class="card-title">My Activity</div>
    </div>
    <div style="overflow-x:auto;">
        <table class="dash-table">
            <thead>
                <tr>
                    <th>Action</th>
                    <th>Description</th>
                    <th>Date &amp; Time</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $badgeClass = [
                        'login' => 'success', 'logout' => 'active',
                        'payment_approved' => 'success', 'payment_rejected' => 'danger',
                        'payment_processed' => 'success', 'walkin_payment' => 'success',
                        'password_change' => 'primary',
                    ];
                    $badgeLabel = [
                        'login' => 'Login', 'logout' => 'Logout',
                        'payment_approved' => 'Payment Approved', 'payment_rejected' => 'Payment Rejected',
                        'payment_processed' => 'Payment Processed', 'walkin_payment' => 'Walk-in Payment',
                        'password_change' => 'Password Change',
                    ];
                @endphp
                @forelse($logs as $log)
                <tr>
                    <td>
                        <span class="status-badge {{ $badgeClass[$log->event_type] ?? 'active' }}">
                            {{ $badgeLabel[$log->event_type] ?? ucfirst(str_replace('_', ' ', $log->event_type)) }}
                        </span>
                    </td>
                    <td style="max-width:480px;word-break:break-word;">{{ $log->description }}</td>
                    <td style="font-size:12px;color:#64748b;white-space:nowrap;">
                        {{ $log->created_at?->format('M d, Y h:i A') }}
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="3" style="text-align:center;padding:48px;color:#94a3b8;">
                        <i class="bi bi-journal-x" style="font-size:40px;display:block;margin-bottom:12px;"></i>
                        No activity recorded yet.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div style="padding:16px 24px;">
        {{ $logs->links() }}
    </div>
</div>
@endsection
