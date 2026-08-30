<?php

namespace App\Http\Middleware;
 
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;


// ════════════════════════════════════════════════════════
// FILE: app/Http/Middleware/TeacherMiddleware.php
// ════════════════════════════════════════════════════════
class TeacherMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        if (!Auth::check()) {
            return redirect()->route('login')
                ->withErrors(['email' => 'Please login to access this page.']);
        }
 
        if (!in_array(Auth::user()->role, ['superadmin', 'admin', 'teacher'])) {
            abort(403, 'Unauthorized. Teacher access required.');
        }
 
        if (!Auth::user()->is_active) {
            Auth::logout();
            return redirect()->route('login')
                ->withErrors(['email' => 'Your account has been deactivated.']);
        }
 
        return $next($request);
    }
}
 