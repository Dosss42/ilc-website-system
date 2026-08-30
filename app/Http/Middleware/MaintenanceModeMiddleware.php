<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\Setting;

class MaintenanceModeMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        if (!Setting::get('maintenance_mode', false)) {
            return $next($request);
        }

        // Admins and super admins bypass maintenance mode
        $user = $request->user();
        if ($user && in_array($user->role, ['admin', 'superadmin', 'registrar'])) {
            return $next($request);
        }

        return response()->view('maintenance', [], 503);
    }
}
