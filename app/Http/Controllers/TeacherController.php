<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Mail\TeacherAccountCreated;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class TeacherController extends Controller
{
    public function index()
    {
        $teachers = User::where('role', 'teacher')->where('is_active', true)->orderBy('name')->get();
        return response()->json(['teachers' => $teachers]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'      => 'required|string|min:2|max:255',
            'email'     => 'required|email:rfc,dns|unique:users,email|max:255',
            'is_active' => 'boolean',
        ]);

        // Auto-generate password (same format as student accounts)
        $plainPassword = $this->generateTeacherPassword();

        $teacher = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => bcrypt($plainPassword),
            'role' => 'teacher',
            'is_active' => $validated['is_active'] ?? true,
            'email_verified_at' => now(),
        ]);

        // Send welcome email (synchronous to avoid queue worker dependency)
        try {
            Mail::to($teacher->email)->send(new TeacherAccountCreated($teacher, $plainPassword));
        } catch (\Exception $e) {
            Log::warning('Failed to send teacher welcome email: ' . $e->getMessage());
        }

        return response()->json([
            'success' => true,
            'message' => 'Teacher account created. Login credentials have been sent to ' . $teacher->email,
            'data' => $teacher,
        ], 201);
    }

    /**
     * Generate a readable teacher password
     * Format: YEAR + 4-6 random digits (e.g., 2026-5847)
     */
    private function generateTeacherPassword(): string
    {
        $year = date('Y');
        $randomDigits = str_pad(mt_rand(1000, 999999), 4, '0', STR_PAD_LEFT);
        return $year . '-' . $randomDigits;
    }

    public function show(User $teacher)
    {
        return response()->json($teacher);
    }

    public function update(Request $request, User $teacher)
    {
        $validated = $request->validate([
            'name'      => 'required|string|min:2|max:255',
            'email'     => 'required|email:rfc,dns|unique:users,email,' . $teacher->id . '|max:255',
            'password'  => 'nullable|string|min:8',
            'is_active' => 'boolean',
        ]);

        $teacher->name = $validated['name'];
        $teacher->email = $validated['email'];
        if (!empty($validated['password'])) {
            $teacher->password = bcrypt($validated['password']);
        }
        $teacher->is_active = $validated['is_active'] ?? true;
        $teacher->save();

        return response()->json($teacher);
    }

    public function destroy(User $teacher)
    {
        $teacher->delete();
        return response()->json(['message' => 'Teacher deleted successfully.']);
    }
}
