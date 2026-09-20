<?php

namespace App\Http\Controllers;

use App\Models\Enrollment;
use App\Models\Guardian;
use App\Models\PreviousSchool;
use App\Models\StudentAddress;
use App\Models\StudentProfile;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use App\Services\ActivityLogger;

class AuthController extends Controller
{
    // ══════════════════════════════════════════
    // SHOW LOGIN PAGE
    // ══════════════════════════════════════════
    public function showLogin()
    {
        return view('login_register');
    }

    // ══════════════════════════════════════════
    // SHOW REGISTER PAGE (opens register panel)
    // ══════════════════════════════════════════
    public function showRegister()
    {
        return view('login_register');
    }

    // ══════════════════════════════════════════
    // LOGIN — redirects by role
    // ══════════════════════════════════════════
    public function login(Request $request)
    {
        // Validate reCAPTCHA if enabled
        if (\App\Services\RecaptchaService::isEnabled()) {
            $recaptchaToken = $request->input('g-recaptcha-response');
            $recaptchaResult = \App\Services\RecaptchaService::verify($recaptchaToken);
            if (!$recaptchaResult['success']) {
                $errorMessage = $recaptchaResult['error'] ?? 'Please verify that you are not a robot.';
                return back()
                    ->withErrors(['email' => $errorMessage])
                    ->withInput($request->only('email'));
            }
        }

        $credentials = $request->validate([
            'email'    => 'required|email',
            'password' => 'required',
        ]);

        // Account lockout: block after 5 failed attempts for 15 minutes.
        // Keyed by email only (not IP) — an IP-inclusive key lets an
        // attacker rotate source IP for a fresh 5-attempt budget against
        // the same target account every time.
        $lockKey    = 'login_attempts_' . md5($request->email);
        $unlockKey  = 'login_unlock_'   . md5($request->email);
        $attempts   = cache()->get($lockKey, 0);

        if ($attempts >= 5) {
            $unlockAt   = cache()->get($unlockKey);
            $minsLeft   = ($unlockAt && $unlockAt > now())
                ? max(1, (int) ceil(now()->diffInSeconds($unlockAt) / 60))
                : 15;
            return back()
                ->withErrors(['email' => "Too many failed login attempts. Please try again in {$minsLeft} minute(s)."])
                ->withInput($request->only('email'));
        }

        if (! Auth::attempt($credentials, $request->boolean('remember'))) {
            $newAttempts = $attempts + 1;
            $ttl         = now()->addMinutes(15);
            cache()->put($lockKey, $newAttempts, $ttl);

            if ($newAttempts >= 5) {
                cache()->put($unlockKey, $ttl, $ttl);
                $msg = 'Too many failed login attempts. Please try again in 15 minutes.';
            } else {
                $remaining = 5 - $newAttempts;
                $msg = "Invalid email or password. {$remaining} attempt(s) remaining.";
            }

            // Log the failed login attempt
            ActivityLogger::log('failed_login', 'Failed login attempt for ' . $request->email, null, null, [
                'email'    => $request->email,
                'attempts' => $newAttempts,
            ]);

            return back()
                ->withErrors(['email' => $msg])
                ->withInput($request->only('email'));
        }

        // Clear failed attempts on successful login
        cache()->forget($lockKey);
        cache()->forget($unlockKey);

        $request->session()->regenerate();
        $user = Auth::user();
        
        // Debug logging
        \Log::info('Login attempt', [
            'user_id' => $user->id,
            'user_name' => $user->name,
            'user_role' => $user->role,
            'user_email' => $user->email,
            'is_active' => $user->is_active,
            'email_verified_at' => $user->email_verified_at,
        ]);

        if (! $user->is_active) {
            \Log::info('User deactivated', ['user_id' => $user->id]);
            Auth::logout();
            return back()->withErrors(['email' => 'Your account is deactivated. Contact the administrator.']);
        }

        // Finance/cashier accounts use dedicated guards (Auth::guard('finance')
        // / Auth::guard('cashier')) with their own sessions and dashboards —
        // Auth::attempt() above only checked the default 'web' guard, which is
        // blind to role, so a correct finance/cashier password would otherwise
        // succeed here too and leave a stray 'web' session that finance./
        // cashier.dashboard's middleware doesn't recognize. Send them to the
        // right login page instead of leaving that dead end.
        if (in_array($user->role, ['finance', 'cashier'])) {
            $roleLabel = ucfirst($user->role);
            Auth::logout();
            return redirect()->route($user->role . '.login')
                ->withErrors(['email' => "Please use the {$roleLabel} login page for this account."]);
        }

        // Block unverified emails (except for admin roles)
        if (! $user->email_verified_at && $user->role === 'student') {
            \Log::info('Student email not verified', ['user_id' => $user->id]);
            Auth::logout();
            return back()->withErrors(['email' => 'Please verify your email first. Check your inbox.'])
                         ->withInput($request->only('email'));
        }

        ActivityLogger::log('login', $user->name . ' logged in', 'User', $user->id, ['role' => $user->role]);

        $redirect = self::redirectByRole($user);
        return $redirect;
    }

    // ══════════════════════════════════════════
    // REGISTER — creates basic account only
    // ══════════════════════════════════════════
    // ══════════════════════════════════════════
    // LOGOUT
    // ══════════════════════════════════════════
    public function logout(Request $request)
    {
        $user = Auth::user();
        if ($user) {
            ActivityLogger::log('logout', $user->name . ' logged out', 'User', $user->id, ['role' => $user->role]);
        }
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login');
    }

    // ══════════════════════════════════════════
    // HELPER — redirect by role
    // ══════════════════════════════════════════
    public static function redirectByRole(User $user)
    {
        return match($user->role) {
            'superadmin' => redirect()->route('superadmin.dashboard'),
            'admin'      => redirect()->route('admin.dashboard'),
            'finance'    => redirect()->route('finance.dashboard'),
            'teacher'    => redirect()->route('teacher.dashboard'),
            'student'    => redirect()->route('student.portal'),
            default      => redirect()->route('home'),
        };
    }
}