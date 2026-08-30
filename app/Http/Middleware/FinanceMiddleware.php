<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class FinanceMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $guard = Auth::guard('finance');

        if (!$guard->check()) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['error' => 'Unauthorized'], 401);
            }
            return redirect()->route('finance.login');
        }

        $user = $guard->user();

        if (!in_array($user->role, ['finance', 'admin', 'superadmin'])) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['error' => 'Forbidden - Finance access only'], 403);
            }
            return redirect()->route('finance.login');
        }

        if (!$user->is_active) {
            $guard->logout();
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['error' => 'Account deactivated'], 403);
            }
            return redirect()->route('finance.login')
                ->with('error', 'Your account has been deactivated. Please contact the administrator.');
        }

        return $next($request);
    }
}
