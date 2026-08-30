<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class FixAuthController extends Controller
{
    /**
     * Test direct login bypassing all checks
     */
    public function directLogin(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $user = User::where('email', $credentials['email'])->first();
        
        if (!$user) {
            return back()->withErrors(['email' => 'User not found']);
        }

        if (!Hash::check($credentials['password'], $user->password)) {
            return back()->withErrors(['email' => 'Invalid password']);
        }

        // Force login without Auth::attempt
        Auth::login($user, $request->boolean('remember'));
        $request->session()->regenerate();
        
        echo "<h1>Direct Login Test</h1>";
        echo "<p>Logged in user: {$user->name}</p>";
        echo "<p>User role: {$user->role}</p>";
        echo "<p>User ID: {$user->id}</p>";
        
        // Test redirect
        $redirectUrl = $this->getRedirectUrl($user);
        echo "<p>Will redirect to: {$redirectUrl}</p>";
        
        return redirect($redirectUrl);
    }
    
    private function getRedirectUrl($user)
    {
        return match($user->role) {
            'superadmin' => route('superadmin.dashboard'),
            'admin' => route('admin.dashboard'),
            'teacher' => route('teacher.dashboard'),
            default => route('student.portal'),
        };
    }
}
