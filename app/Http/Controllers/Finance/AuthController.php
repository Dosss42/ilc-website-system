<?php

namespace App\Http\Controllers\Finance;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    /**
     * Show finance login form
     */
    public function showLoginForm()
    {
        if (Auth::guard('finance')->check()) {
            return redirect()->route('finance.dashboard');
        }
        return view('finance.login');
    }

    /**
     * Handle finance login
     */
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
                    ->withInput($request->except('password'));
            }
        }

        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        // Rate limiting: block after 5 failed attempts for 15 minutes
        $lockKey   = 'finance_login_attempts_' . md5($request->ip() . $request->email);
        $unlockKey = 'finance_login_unlock_'   . md5($request->ip() . $request->email);
        $attempts  = cache()->get($lockKey, 0);

        if ($attempts >= 5) {
            $unlockAt = cache()->get($unlockKey);
            $minsLeft = ($unlockAt && $unlockAt > now())
                ? max(1, (int) ceil(now()->diffInSeconds($unlockAt) / 60))
                : 15;
            return back()->withErrors([
                'email' => "Too many failed login attempts. Please try again in {$minsLeft} minute(s).",
            ])->withInput($request->except('password'));
        }

        // Find user by email
        $user = User::where('email', $request->email)->first();

        // Check if user exists and has finance role
        if (!$user || $user->role !== 'finance') {
            $this->incrementLoginAttempts($lockKey, $unlockKey, $attempts);
            return back()->withErrors([
                'email' => 'These credentials do not match our finance records.',
            ])->withInput($request->except('password'));
        }

        // Check if account is active
        if (!$user->is_active) {
            return back()->withErrors([
                'email' => 'Your account has been deactivated. Please contact the administrator.',
            ])->withInput($request->except('password'));
        }

        // Attempt authentication using the finance guard
        if (Auth::guard('finance')->attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();
            cache()->forget($lockKey);
            cache()->forget($unlockKey);

            \Log::info('Finance user logged in', [
                'user_id' => $user->id,
                'email' => $user->email,
                'ip' => $request->ip(),
            ]);

            return redirect()->intended(route('finance.dashboard'));
        }

        $this->incrementLoginAttempts($lockKey, $unlockKey, $attempts);
        return back()->withErrors([
            'email' => 'The provided credentials are incorrect.',
        ])->withInput($request->except('password'));
    }

    /**
     * Logout finance user
     */
    public function logout(Request $request)
    {
        $user = Auth::guard('finance')->user();

        Auth::guard('finance')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        // Log the logout
        if ($user) {
            \Log::info('Finance user logged out', [
                'user_id' => $user->id,
                'email' => $user->email,
            ]);
        }

        return redirect()->route('finance.login');
    }

    private function incrementLoginAttempts(string $lockKey, string $unlockKey, int $current): void
    {
        $newAttempts = $current + 1;
        $ttl         = now()->addMinutes(15);
        cache()->put($lockKey, $newAttempts, $ttl);
        if ($newAttempts >= 5) {
            cache()->put($unlockKey, $ttl, $ttl);
        }
    }

    /**
     * Show change password form
     */
    public function showChangePasswordForm()
    {
        return view('finance.change-password');
    }

    /**
     * Change password
     */
    public function changePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'new_password' => 'required|string|min:8|confirmed',
        ]);

        $user = Auth::guard('finance')->user();

        // Verify current password
        if (!Hash::check($request->current_password, $user->password)) {
            return back()->withErrors([
                'current_password' => 'The current password is incorrect.',
            ]);
        }

        // Update password
        $user->update([
            'password' => Hash::make($request->new_password),
        ]);

        \Log::info('Finance user changed password', [
            'user_id' => $user->id,
            'email' => $user->email,
        ]);

        return back()->with('success', 'Password changed successfully!');
    }

    /**
     * Show profile form
     */
    public function showProfile()
    {
        return view('finance.profile', [
            'user' => Auth::guard('finance')->user(),
        ]);
    }

    /**
     * Update profile
     */
    public function updateProfile(Request $request)
    {
        $user = Auth::guard('finance')->user();

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
        ]);

        $user->update($validated);

        return back()->with('success', 'Profile updated successfully!');
    }

    /**
     * Create a new finance user (for superadmin only)
     */
    public function createFinanceUser(Request $request)
    {
        // Only superadmin or admin can create finance users
        if (!in_array(Auth::user()->role, ['superadmin', 'admin'])) {
            abort(403, 'Unauthorized');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8',
            'phone' => 'nullable|string|max:20',
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => 'finance',
            'is_active' => true,
            'email_verified_at' => now(),
        ]);

        \Log::info('Finance user created', [
            'created_by' => Auth::id(),
            'new_user_id' => $user->id,
            'email' => $user->email,
        ]);

        return back()->with('success', 'Finance user created successfully!');
    }

    /**
     * List all finance users (for superadmin/admin)
     */
    public function listFinanceUsers()
    {
        if (!in_array(Auth::user()->role, ['superadmin', 'admin'])) {
            abort(403, 'Unauthorized');
        }

        $financeUsers = User::where('role', 'finance')
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('finance.users', compact('financeUsers'));
    }

    /**
     * Toggle finance user active status
     */
    public function toggleUserStatus(User $user)
    {
        if (!in_array(Auth::user()->role, ['superadmin', 'admin'])) {
            abort(403, 'Unauthorized');
        }

        if ($user->role !== 'finance') {
            return back()->with('error', 'User is not a finance staff member.');
        }

        $user->update([
            'is_active' => !$user->is_active,
        ]);

        $status = $user->is_active ? 'activated' : 'deactivated';

        \Log::info("Finance user {$status}", [
            'admin_id' => Auth::id(),
            'user_id' => $user->id,
            'email' => $user->email,
        ]);

        return back()->with('success', "Finance user {$status} successfully!");
    }
}
