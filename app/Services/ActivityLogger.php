<?php

namespace App\Services;

use App\Models\ActivityLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

class ActivityLogger
{
    public static function log(
        string $eventType,
        string $description,
        ?string $subjectType = null,
        ?string $subjectId   = null,
        array   $extra       = []
    ): void {
        try {
            $user = Auth::user();

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
