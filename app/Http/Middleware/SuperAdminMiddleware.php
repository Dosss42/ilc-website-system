<?php
// ════════════════════════════════════════════════════════
// FILE: app/Http/Middleware/SuperAdminMiddleware.php
// ════════════════════════════════════════════════════════
namespace App\Http\Middleware;
 
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
 
class SuperAdminMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        if (!Auth::check()) {
            return redirect()->route('login')
                ->withErrors(['email' => 'Please login to access this page.']);
        }
 
        if (Auth::user()->role !== 'superadmin') {
            abort(403, 'Unauthorized. Super Admin access required.');
        }
 
        if (!Auth::user()->is_active) {
            Auth::logout();
            return redirect()->route('login')
                ->withErrors(['email' => 'Your account has been deactivated.']);
        }
 
        return $next($request);
    }
}