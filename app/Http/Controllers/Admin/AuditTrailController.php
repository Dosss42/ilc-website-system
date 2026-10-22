<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Services\AuditLogRetention;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

/**
 * Admin › Audit Trail — what every role did, except the Super Admin (who sits above the
 * Admin and has the full log in System Logs). Cashier and Finance no longer have an audit
 * page of their own; their actions are still recorded and show up here. Read-only: only
 * the Super Admin sets how long entries are kept.
 */
class AuditTrailController extends Controller
{
    /** Roles an Admin can see entries for — every one but Super Admin */
    private function roles(): array
    {
        return array_diff_key(ActivityLog::ROLES, ['superadmin' => true]);
    }

    /** Fragment for the on-demand Audit Trail section (GET admin.section.audit). */
    public function section(Request $request)
    {
        AuditLogRetention::clearIfDue();

        $search = trim((string) $request->input('audit_search', ''));
        $type   = (string) $request->input('audit_type', '');
        $role   = (string) $request->input('audit_role', '');
        $from   = (string) $request->input('audit_from', '');
        $to     = (string) $request->input('audit_to', '');

        // Everything but Super Admin entries ("System" rows have no role and stay visible)
        $base = ActivityLog::where(fn ($q) => $q->whereNull('user_role')->orWhere('user_role', '!=', 'superadmin'));
        // Counted as all rows minus the Super Admin's: both use an index, while counting the
        // "null or not superadmin" condition reads the whole table (3.5 s at ~330,000 rows)
        $total = ActivityLog::count() - ActivityLog::where('user_role', 'superadmin')->count();
        $filtered = $search !== '' || isset(ActivityLog::EVENT_TYPES[$type]) || isset($this->roles()[$role])
            || $this->isDate($from) || $this->isDate($to);

        $query = (clone $base)
            ->when($search !== '', function ($q) use ($search) {
                $like = '%' . addcslashes($search, '%_\\') . '%';
                $q->where(fn ($w) => $w->where('description', 'like', $like)->orWhere('user_name', 'like', $like));
            })
            ->when(isset(ActivityLog::EVENT_TYPES[$type]), fn ($q) => $q->where('event_type', $type))
            ->when(isset($this->roles()[$role]), fn ($q) => $q->where('user_role', $role))
            // Date ranges, not DATE(created_at), so the created_at index is used
            ->when($this->isDate($from), fn ($q) => $q->where('created_at', '>=', $from . ' 00:00:00'))
            ->when($this->isDate($to), fn ($q) => $q->where('created_at', '<', Carbon::parse($to)->addDay()->toDateString() . ' 00:00:00'))
            ->orderByDesc('created_at')->orderByDesc('id');

        $logs = $query->paginate(20, ['*'], 'audit_page', null, $filtered ? null : $total)->withQueryString();

        return view('admin.sections.audit-content', [
            'logs'        => $logs,
            'auditSearch' => $search,
            'auditType'   => $type,
            'auditRole'   => $role,
            'auditFrom'   => $from,
            'auditTo'     => $to,
            'stats'       => [
                'total'  => $total,
                'today'  => (clone $base)->where('created_at', '>=', today())->count(),
                'failed' => (clone $base)->whereIn('event_type', ['failed_login', 'denied'])->where('created_at', '>=', now()->subDays(7))->count(),
            ],
            'retention'   => AuditLogRetention::label(),
            'roles'       => $this->roles(),
            'eventTypes'  => ActivityLog::EVENT_TYPES,
        ]);
    }

    private function isDate(?string $v): bool
    {
        return (bool) preg_match('/^\d{4}-\d{2}-\d{2}$/', (string) $v);
    }
}
