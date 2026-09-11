<?php

namespace App\Services;

use App\Models\ActivityLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

class ActivityLogger
{
    /**
     * Resolve the currently authenticated user regardless of which guard
     * they signed in on. Finance and Cashier staff authenticate on their
     * own dedicated guards, not the default 'web' guard, so Auth::user()
     * alone silently returns null for them — logging their actions with
     * a blank user_id/user_name/user_role, or being skipped entirely.
     */
    public static function resolveUser()
    {
        return Auth::user()
            ?? Auth::guard('finance')->user()
            ?? Auth::guard('cashier')->user();
    }

    public static function log(
        string $eventType,
        string $description,
        ?string $subjectType = null,
        ?string $subjectId   = null,
        array   $extra       = []
    ): void {
        try {
            $user = self::resolveUser();

            ActivityLog::create([
                'user_id'      => $user?->id,
                'user_name'    => $user?->name,
                'user_role'    => $user?->role,
                'event_type'   => $eventType,
                'description'  => $description,
                'subject_type' => $subjectType,
                'subject_id'   => $subjectId ? (string) $subjectId : null,
                'ip_address'   => Request::ip(),
                'extra'        => !empty($extra) ? json_encode($extra) : null,
            ]);
        } catch (\Throwable $e) {
            // Never let logging break the main request
        }
    }
}
