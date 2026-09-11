<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules;

class ForgotPasswordController extends Controller
{
    /**
     * Single shared "forgot password" entry point for every portal
     * (main login, Finance, Cashier) — accounts all live in the same
     * `users` table, so one email-lookup flow covers all of them; the
     * user is routed back to the correct login page for their role
     * once the password is reset.
     */
    public function showLinkRequestForm()
    {
        return view('auth.forgot-password');
    }

    public function sendResetLinkEmail(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        // Rate limit by IP so this can't be used to spam an inbox or
        // brute-force-enumerate registered emails.
        $key = 'forgot_password_' . md5($request->ip());
        if (cache()->get($key, 0) >= 5) {
            return back()
                ->withErrors(['email' => 'Too many requests. Please try again in a few minutes.'])
                ->withInput($request->only('email'));
        }
        cache()->put($key, cache()->get($key, 0) + 1, now()->addMinutes(15));

        Password::sendResetLink($request->only('email'));

        // Always show the same generic message regardless of whether the
        // email is actually registered, so this can't be used to check
        // which addresses exist in the system.
        return back()->with('status', 'If an account exists with that email, a password reset link has been sent.');
    }

    public function showResetForm(Request $request, string $token)
    {
        return view('auth.reset-password', [
            'token' => $token,
            'email' => $request->query('email', ''),
        ]);
    }

    public function reset(Request $request)
    {
        $request->validate([
            'token'    => 'required',
            'email'    => 'required|email',
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function (User $user, string $password) {
                $user->forceFill([
                    'password' => Hash::make($password),
                ])->setRememberToken(Str::random(60));
                $user->save();

                event(new PasswordReset($user));
            }
        );

        if ($status === Password::PASSWORD_RESET) {
            $user = User::where('email', $request->email)->first();
            $loginRoute = match ($user?->role) {
                'finance' => 'finance.login',
                'cashier' => 'cashier.login',
                default   => 'login',
            };

            return redirect()->route($loginRoute)
                ->with('success', 'Your password has been reset. Please log in with your new password.');
        }

        return back()
            ->withErrors(['email' => __($status)])
            ->withInput($request->only('email'));
    }
}
