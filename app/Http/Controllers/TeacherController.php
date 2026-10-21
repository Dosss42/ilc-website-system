<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Mail\TeacherAccountCreated;
use App\Services\ActivityLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;

class TeacherController extends Controller
{
    public function index(Request $request)
    {
        $query = User::where('role', 'teacher')->where('is_active', true)->orderByDesc('created_at');

        // Pagination is opt-in (?paginated=1) — other callers (dropdown
        // population elsewhere, the dedicated Teacher Management page) need
        // the full list, so the default response stays unpaginated.
        if ($request->boolean('paginated')) {
            $page = $query->paginate((int) $request->input('per_page', 15));
            return response()->json([
                'teachers'     => $page->items(),
                'current_page' => $page->currentPage(),
                'last_page'    => $page->lastPage(),
                'per_page'     => $page->perPage(),
                'total'        => $page->total(),
                'from'         => $page->firstItem(),
                'to'           => $page->lastItem(),
            ]);
        }

        return response()->json(['teachers' => $query->get()]);
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

        ActivityLogger::log('create', "Created teacher account for {$teacher->name} ({$teacher->email})", 'User', $teacher->id);

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
            'password'  => ['nullable', 'string', \Illuminate\Validation\Rules\Password::defaults()],
            'is_active' => 'boolean',
        ]);

        $teacher->name = $validated['name'];
        $teacher->email = $validated['email'];
        $passwordChanged = !empty($validated['password']);
        if ($passwordChanged) {
            $teacher->password = bcrypt($validated['password']);
        }
        $teacher->is_active = $validated['is_active'] ?? true;
        $teacher->save();

        ActivityLogger::log('update', "Updated teacher {$teacher->name} ({$teacher->email})" . ($passwordChanged ? ' — password reset' : ''), 'User', $teacher->id);

        return response()->json($teacher);
    }

    /**
     * Archive a teacher — soft delete, not permanent. Password-confirmed for
     * Admin, same gate as EnrollmentController::deleteStudent() — Super Admin
     * uses its own saConfirm() dialog instead (no password field anywhere
     * else in that dashboard either, including its own generic user-delete),
     * so the check is skipped for that role to match.
     */
    public function destroy(Request $request, User $teacher)
    {
        if (Auth::user()->role !== 'superadmin' && !Hash::check((string) $request->input('password'), Auth::user()->password)) {
            ActivityLogger::log('denied', "Wrong password entered while trying to archive teacher {$teacher->name}", 'User', $teacher->id);
            return response()->json(['success' => false, 'message' => 'Incorrect password. Please try again.'], 422);
        }

        $name = $teacher->name;
        $email = $teacher->email;

        // User uses SoftDeletes, so delete() below is an UPDATE, not a real
        // row removal — the nullOnDelete() foreign keys on these three
        // tables never fire on a soft delete, leaving them pointing at a
        // now-deleted account. Null them out explicitly first.
        \App\Models\Section::where('teacher_id', $teacher->id)->update(['teacher_id' => null]);
        \App\Models\Schedule::where('teacher_id', $teacher->id)->update(['teacher_id' => null]);
        \App\Models\Subject::where('teacher_id', $teacher->id)->update(['teacher_id' => null]);

        $teacher->delete();
        ActivityLogger::log('delete', "Archived teacher {$name} ({$email})", 'User', $teacher->id);
        return response()->json(['success' => true, 'message' => 'Teacher archived successfully. You can restore them from the Archives tab.']);
    }

    /**
     * Restore an archived teacher.
     */
    public function restore($id)
    {
        $teacher = User::withTrashed()->findOrFail($id);
        $teacher->restore();
        ActivityLogger::log('update', "Restored teacher {$teacher->name} ({$teacher->email}) from archive", 'User', $teacher->id);
        return response()->json(['success' => true, 'message' => 'Teacher restored successfully.']);
    }

    /**
     * Permanently delete an archived teacher — removes the account and
     * profile photo. Cannot be undone. teacher_assignments rows cascade-delete
     * at the DB level (ON DELETE CASCADE); sections/schedules/subjects.teacher_id
     * null themselves via their own FK — no manual cleanup needed for either.
     */
    public function forceDelete(Request $request, $id)
    {
        $teacher = User::withTrashed()->find($id);
        if (Auth::user()->role !== 'superadmin' && !Hash::check((string) $request->input('password'), Auth::user()->password)) {
            ActivityLogger::log('denied', "Wrong password entered while trying to PERMANENTLY delete teacher " . ($teacher->name ?? "#{$id}"), 'User', $id);
            return response()->json(['success' => false, 'message' => 'Incorrect password. Please try again.'], 422);
        }

        $teacher = User::withTrashed()->findOrFail($id);
        $name = $teacher->name;
        $email = $teacher->email;

        if ($teacher->profile_photo && Storage::disk('public')->exists($teacher->profile_photo)) {
            Storage::disk('public')->delete($teacher->profile_photo);
        }

        $teacher->forceDelete();
        ActivityLogger::log('delete', "PERMANENTLY deleted teacher {$name} ({$email})", 'User', $id);
        return response()->json(['success' => true, 'message' => 'Teacher permanently deleted.']);
    }
}
