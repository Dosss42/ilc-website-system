<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CashierMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        $guard = Auth::guard('cashier');

        if (!$guard->check()) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['error' => 'Unauthorized'], 401);
            }
            return redirect()->route('cashier.login');
        }

        $user = $guard->user();

        if (!in_array($user->role, ['cashier', 'admin', 'superadmin'])) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['error' => 'Cashier access only'], 403);
            }
            return redirect()->route('cashier.login')
                ->with('error', 'You do not have cashier access.');
        }

        if (!$user->is_active) {
            $guard->logout();
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['error' => 'Account deactivated'], 403);
            }
            return redirect()->route('cashier.login')
                ->with('error', 'Your account has been deactivated. Please contact the administrator.');
        }

        return $next($request);
    }
}
