<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class UserController extends Controller
{
    // ══════════════════════════════════════════
    // DASHBOARD — list all users with pagination and sorting
    // ══════════════════════════════════════════
    public function index(Request $request)
    {
        $sort = $request->get('sort', 'newest'); // default: newest first
        $roleFilter = $request->get('role', 'all'); // filter by role

        $query = User::query();

        // Apply role filter
        if ($roleFilter !== 'all') {
            $query->where('role', $roleFilter);
        }

        // Apply sorting
        switch ($sort) {
            case 'name_asc':
                $query->orderBy('name', 'asc');
                break;
            case 'name_desc':
                $query->orderBy('name', 'desc');
                break;
            case 'oldest':
                $query->orderBy('created_at', 'asc');
                break;
            case 'newest':
            default:
                $query->orderBy('created_at', 'desc');
                break;
        }

        $users = $query->paginate(15)->withQueryString();
        return view('superadmin_dashboard', compact('users', 'sort', 'roleFilter'));
    }

    // ══════════════════════════════════════════
    // CREATE USER (Admin or Teacher only)
    // Super Admin accounts are seeded — not created via form
    // ══════════════════════════════════════════
    public function store(Request $request)
    {
        $request->validate([
            'name'  => 'required|string|max:100',
            'email' => 'required|email|unique:users,email|max:255',
            'role'  => 'required|in:admin,teacher',
        ], [
            'email.unique' => 'This email is already registered.',
            'role.in'      => 'Role must be admin or teacher only.',
        ]);

        // Generate temporary password
        $tempPassword = Str::upper(Str::random(4))
                      . rand(100, 999)
                      . Str::lower(Str::random(3))
                      . '!';

        $user = User::create([
            'name'              => strip_tags(trim($request->name)),
            'email'             => strtolower(trim($request->email)),
            'password'          => Hash::make($tempPassword),
            'role'              => $request->role,
            'is_active'         => true,
            'email_verified_at' => now(),
        ]);

        // Send credentials by email
        $roleName = ucfirst($request->role);
        Mail::raw(
            "Hello {$user->name},\n\n" .
            "Your {$roleName} account has been created at IEMELIF Learning Center.\n\n" .
            "LOGIN CREDENTIALS:\n" .
            "Email:    {$user->email}\n" .
            "Password: {$tempPassword}\n\n" .
            "Please log in and change your password immediately.\n" .
            "Login at: " . url('/login') . "\n\n" .
            "For security, do not share these credentials.\n\n" .
            "— ILC System Administrator",
            fn($m) => $m->to($user->email)
                        ->subject("ILC System — Your {$roleName} Account Credentials")
        );

        return back()->with('success',
            "Account created for {$user->name}. Login credentials sent to {$user->email}."
        );
    }

    // ══════════════════════════════════════════
    // TOGGLE ACTIVE / INACTIVE
    // ══════════════════════════════════════════
    public function toggleActive(User $user)
    {
        if ($user->id === auth()->id()) {
            return back()->withErrors(['error' => 'You cannot deactivate your own account.']);
        }

        $user->update(['is_active' => ! $user->is_active]);

        $status = $user->is_active ? 'activated' : 'deactivated';
        return back()->with('success', "Account {$status} successfully.");
    }

    // ══════════════════════════════════════════
    // UPDATE USER
    // ══════════════════════════════════════════
    public function update(Request $request, User $user)
    {
        $request->validate([
            'name'  => 'required|string|max:100',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'role'  => 'required|in:admin,teacher,student',
        ]);

        $user->update([
            'name'  => strip_tags(trim($request->name)),
            'email' => strtolower(trim($request->email)),
            'role'  => $request->role,
        ]);

        return back()->with('success', 'User updated successfully.');
    }

    // ══════════════════════════════════════════
    // DELETE USER
    // ══════════════════════════════════════════
    public function destroy(User $user)
    {
        if ($user->id === auth()->id()) {
            return back()->withErrors(['error' => 'You cannot delete your own account.']);
        }

        if ($user->role === 'superadmin') {
            return back()->withErrors(['error' => 'Cannot delete Super Admin accounts.']);
        }

        $user->delete();
        return back()->with('success', 'User deleted successfully.');
    }

    // ══════════════════════════════════════════
    // RESET PASSWORD — generates new temp password
    // ══════════════════════════════════════════
    public function resetPassword(User $user)
    {
        $tempPassword = Str::upper(Str::random(4))
                      . rand(100, 999)
                      . Str::lower(Str::random(3))
                      . '!';

        $user->update(['password' => Hash::make($tempPassword)]);

        Mail::raw(
            "Hello {$user->name},\n\n" .
            "Your password has been reset by the administrator.\n\n" .
            "NEW PASSWORD: {$tempPassword}\n\n" .
            "Please log in and change your password immediately.\n" .
            "Login at: " . url('/login') . "\n\n" .
            "— ILC System Administrator",
            fn($m) => $m->to($user->email)
                        ->subject('ILC System — Password Reset')
        );

        return back()->with('success', "Password reset. New credentials sent to {$user->email}.");
    }
}
