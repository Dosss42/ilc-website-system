<?php

namespace App\Support\Traits;

use App\Mail\PasswordChangeOtpMail;
use App\Models\OtpVerification;
use App\Services\ActivityLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

/**
 * Email-OTP "change my password" flow shared by the Student and Teacher
 * portals (both previously had their own identical copy, so a bug in one
 * was always a bug in both). The using controller supplies the Settings tab
 * to reopen after the redirect via passwordSettingsTab().
 *
 * Step 1 (sendPasswordOtp, called with fetch): checks the current password
 * and the new one against Settings > Security, emails a 6-digit code, and
 * keeps the new password's hash in the session until the code is confirmed.
 * Step 2 (updatePassword, normal form post): checks the code and applies it.
 */
trait ChangesPasswordWithOtp
{
    abstract protected function passwordSettingsTab(): string;

    public function sendPasswordOtp(Request $request)
    {
        // Always answer in JSON — the page reads this with fetch(), and a default
        // validation redirect showed up there as a vague "Network error".
        $validator = Validator::make($request->all(), [
            'current_password' => 'required',
            'password'         => ['required', 'string', 'confirmed', \Illuminate\Validation\Rules\Password::defaults()],
        ]);
        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => $validator->errors()->first()], 422);
        }

        $user = Auth::user();

        if (!Hash::check($request->current_password, $user->password)) {
            return response()->json(['success' => false, 'message' => 'Current password is incorrect.'], 422);
        }

        // Invalidate any previous pending OTPs for this user
        OtpVerification::where('email', $user->email)->delete();

        $code  = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        $token = Str::random(40);

        $otp = OtpVerification::create([
            'email'      => $user->email,
            'code'       => Hash::make($code),
            'token'      => $token,
            'attempts'   => 0,
            'verified'   => false,
            'expires_at' => now()->addMinutes(10),
        ]);

        // New password's hash waits in the session until the code is confirmed
        $hashKey = 'pwd_change_hash_' . $token;
        session([$hashKey => Hash::make($request->password)]);

        try {
            Mail::to($user->email)->send(new PasswordChangeOtpMail($code, $user->name));
        } catch (\Throwable $e) {
            Log::error('Password-change OTP email failed', ['user_id' => $user->id, 'error' => $e->getMessage()]);
            $otp->delete();
            session()->forget($hashKey);
            return response()->json([
                'success' => false,
                'message' => "Couldn't send the OTP email right now. Please try again in a few minutes.",
            ], 503);
        }

        return response()->json(['success' => true, 'token' => $token, 'email' => $user->email]);
    }

    public function updatePassword(Request $request)
    {
        $tab = $this->passwordSettingsTab();

        $request->validate([
            'otp_token' => 'required|string',
            'otp_code'  => 'required|string|size:6',
        ]);

        $user = Auth::user();
        $otp  = OtpVerification::where('token', $request->otp_token)
                    ->where('email', $user->email)
                    ->first();

        if (!$otp || $otp->isExpired()) {
            return back()->withErrors(['otp_code' => 'OTP expired or invalid. Please request a new one.'])
                         ->with('settings_tab', $tab);
        }

        if ($otp->hasExceededAttempts()) {
            $otp->delete();
            return back()->withErrors(['otp_code' => 'Too many incorrect attempts. Please request a new OTP.'])
                         ->with('settings_tab', $tab);
        }

        if (!Hash::check($request->otp_code, $otp->code)) {
            $otp->increment('attempts');
            $remaining = max(0, 3 - $otp->attempts); // OtpVerification::hasExceededAttempts() allows 3

            if ($remaining === 0) {
                $otp->delete();
                session()->forget('pwd_change_hash_' . $request->otp_token);
                return back()->withErrors(['otp_code' => 'Incorrect OTP. No attempts left — please request a new OTP.'])
                             ->with('settings_tab', $tab);
            }

            return back()->withErrors(['otp_code' => "Incorrect OTP. {$remaining} attempt(s) remaining."])
                         ->with('otp_token', $request->otp_token)
                         ->with('settings_tab', $tab);
        }

        $hashKey = 'pwd_change_hash_' . $request->otp_token;
        $newHash = session($hashKey);

        if (!$newHash) {
            return back()->withErrors(['otp_code' => 'Session expired. Please start over.'])
                         ->with('settings_tab', $tab);
        }

        $user->update(['password' => $newHash]);
        $otp->delete();
        session()->forget($hashKey);
        \App\Support\SessionPasswordHash::refresh(); // stay signed in on this device

        ActivityLogger::log('password_change', "{$user->name} changed their own password", 'User', $user->id);

        return back()->with('password_success', 'Password changed successfully!')
                     ->with('settings_tab', $tab);
    }
}
