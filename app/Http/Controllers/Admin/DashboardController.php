<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\ActivityLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class DashboardController extends Controller
{
    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'password'         => ['required', 'string', 'confirmed', \Illuminate\Validation\Rules\Password::defaults()],
        ]);

        $user = Auth::user();

        if (!Hash::check($request->current_password, $user->password)) {
            ActivityLogger::log('denied', "{$user->name} entered the wrong current password while trying to change their own password", 'User', $user->id);
            return back()->withErrors(['current_password' => 'The current password is incorrect.'])
                         ->with('settings_tab', 'account');
        }

        $user->update(['password' => Hash::make($request->password)]);

        ActivityLogger::log('password_change', "{$user->name} changed their own password", 'User', $user->id);

        return back()->with('password_success', 'Password updated successfully.')
                     ->with('settings_tab', 'account');
    }

    public function updateProfile(Request $request)
    {
        // Own error bag so this can't collide with any other form's "name" field on the dashboard
        $validated = $request->validateWithBag('profile', [
            'name' => 'required|string|min:2|max:255',
        ]);

        $user    = Auth::user();
        $oldName = $user->name;
        $user->update(['name' => $validated['name']]);

        if ($oldName !== $user->name) {
            ActivityLogger::log('update', "{$oldName} changed their name to {$user->name}", 'User', $user->id);
        }

        return back()->with('profile_success', 'Name updated successfully.')
                     ->with('settings_tab', 'account');
    }

    public function updatePhoto(Request $request)
    {
        $request->validate([
            'photo' => 'required|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        $user = Auth::user();

        if ($user->profile_photo) {
            Storage::disk('public')->delete($user->profile_photo);
        }

        $path = $request->file('photo')->store('admin-photos', 'public');
        $user->update(['profile_photo' => $path]);

        ActivityLogger::log('update', "{$user->name} updated their profile photo", 'User', $user->id);

        return back()->with('photo_success', 'Profile photo updated successfully.')
                     ->with('settings_tab', 'account');
    }
}
