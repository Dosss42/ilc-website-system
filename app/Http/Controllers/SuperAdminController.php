<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\Announcement;
use App\Models\News;
use App\Models\User;
use App\Services\ActivityLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class SuperAdminController extends Controller
{
    /**
     * Display the superadmin dashboard with user statistics and user list.
     */
    private const ALL_ROLES = ['superadmin', 'admin', 'finance', 'teacher', 'student'];

    public function dashboard(Request $request)
    {
        $sort       = $request->input('sort', 'newest');
        $roleFilter = $request->input('role', 'all');
        $section    = $request->input('section', 'dashboard');

        $usersQuery = User::query();
        if ($roleFilter !== 'all' && in_array($roleFilter, self::ALL_ROLES)) {
            $usersQuery->where('role', $roleFilter);
        }

        match ($sort) {
            'oldest'    => $usersQuery->orderBy('created_at'),
            'name_asc'  => $usersQuery->orderBy('name'),
            'name_desc' => $usersQuery->orderByDesc('name'),
            default     => $usersQuery->orderByDesc('created_at'),
        };

        $userManagementUsers = (clone $usersQuery)->paginate(15)->withQueryString();
        $dashboardUsers      = (clone $usersQuery)->paginate(10)->withQueryString();
        $users = $userManagementUsers;

        $allUsers = User::get();
        $stats = [
            'total_users'    => $allUsers->count(),
            'superadmins'    => $allUsers->where('role', 'superadmin')->count(),
            'admins'         => $allUsers->where('role', 'admin')->count(),
            'finance'        => $allUsers->where('role', 'finance')->count(),
            'total_teachers' => $allUsers->where('role', 'teacher')->count(),
            'total_students' => $allUsers->where('role', 'student')->count(),
            'active_users'   => $allUsers->where('is_active', true)->count(),
            'inactive_users' => $allUsers->where('is_active', false)->count(),
        ];

        $logs    = $this->getSystemLogs([
            'type'   => $request->input('log_type'),
            'search' => $request->input('log_search'),
            'date'   => $request->input('log_date'),
        ]);
        $backups = $this->listBackups();

        $announcements = Announcement::with('teacher')->latest()->paginate(15, ['*'], 'ann_page');
        $newsArticles  = News::with('author')->latest()->paginate(15, ['*'], 'news_page');

        return view('superadmin_dashboard', compact(
            'users', 'dashboardUsers', 'userManagementUsers',
            'stats', 'sort', 'roleFilter', 'logs', 'section', 'backups',
            'announcements', 'newsArticles'
        ));
    }

    /**
     * Get system logs from the activity_logs table.
     */
    private function getSystemLogs(array $filters = [])
    {
        $query = ActivityLog::orderByDesc('created_at');

        if (!empty($filters['type'])) {
            $query->where('event_type', $filters['type']);
        }
        if (!empty($filters['search'])) {
            $query->where(function ($q) use ($filters) {
                $q->where('description', 'like', '%' . $filters['search'] . '%')
                  ->orWhere('user_name', 'like', '%' . $filters['search'] . '%');
            });
        }
        if (!empty($filters['date'])) {
            $query->whereDate('created_at', $filters['date']);
        }

        return $query->limit(200)->get();
    }

    /**
     * Store a newly created user.
     */
    public function storeUser(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'role' => 'required|in:superadmin,admin,finance,teacher,student',
            'is_active' => 'boolean',
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => $validated['role'],
            'is_active' => $validated['is_active'] ?? true,
            'email_verified_at' => now(),
        ]);

        ActivityLogger::log('create', 'New user created: ' . $user->name . ' (' . $user->role . ')', 'User', $user->id);

        return response()->json(['success' => true, 'message' => 'User created successfully.', 'user' => $user], 201);
    }

    /**
     * Update the specified user.
     */
    public function updateUser(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'role' => 'required|in:superadmin,admin,finance,teacher,student',
            'is_active' => 'boolean',
        ]);

        $user->update([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'role' => $validated['role'],
            'is_active' => $validated['is_active'] ?? $user->is_active,
        ]);

        ActivityLogger::log('update', 'User updated: ' . $user->name . ' (' . $user->role . ')', 'User', $user->id);

        return response()->json(['success' => true, 'message' => 'User updated successfully.', 'user' => $user]);
    }

    /**
     * Remove the specified user.
     */
    public function deleteUser(User $user)
    {
        if ($user->id === auth()->id()) {
            return response()->json(['success' => false, 'message' => 'You cannot delete your own account.'], 403);
        }

        ActivityLogger::log('delete', 'User deleted: ' . $user->name . ' (' . $user->role . ')', 'User', $user->id);
        $user->delete();

        return response()->json(['success' => true, 'message' => 'User deleted successfully.']);
    }

    /**
     * Toggle user active status.
     */
    public function toggleUserStatus(User $user)
    {
        if ($user->id === auth()->id()) {
            return response()->json(['success' => false, 'message' => 'You cannot deactivate your own account.'], 403);
        }

        $user->update(['is_active' => !$user->is_active]);

        $action = $user->is_active ? 'activated' : 'deactivated';
        ActivityLogger::log('update', 'User ' . $action . ': ' . $user->name, 'User', $user->id);

        return response()->json(['success' => true, 'message' => 'User status updated successfully.', 'is_active' => $user->is_active]);
    }

    /**
     * Reset user password.
     */
    public function resetPassword(Request $request, User $user)
    {
        $validated = $request->validate([
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user->update(['password' => Hash::make($validated['password'])]);

        ActivityLogger::log('update', 'Password reset for user: ' . $user->name, 'User', $user->id);

        return response()->json(['success' => true, 'message' => 'Password reset successfully.']);
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'password'         => 'required|string|min:8|confirmed',
        ]);

        $user = Auth::user();

        if (!Hash::check($request->current_password, $user->password)) {
            return back()->withErrors(['current_password' => 'The current password is incorrect.'])
                         ->with('settings_tab', 'account');
        }

        $user->update(['password' => Hash::make($request->password)]);

        return back()->with('password_success', 'Password updated successfully.')
                     ->with('settings_tab', 'account');
    }

    // ── Announcements ────────────────────────────────

    public function storeAnnouncement(Request $request)
    {
        $request->validate([
            'title'    => 'required|string|max:255',
            'content'  => 'required|string',
            'category' => 'required|in:academic,reminder,activity,general,enrollment',
            'audience' => 'required|in:all,parents,teachers',
            'image'    => 'nullable|image|mimes:jpg,jpeg,png,webp|max:3072',
        ]);

        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('announcements', 'public');
        }

        Announcement::create([
            'teacher_id' => Auth::id(),
            'title'      => $request->title,
            'content'    => $request->content,
            'category'   => $request->category,
            'audience'   => $request->audience,
            'is_active'  => true,
            'image'      => $imagePath,
        ]);

        return back()->with('sa_success', 'Announcement posted successfully!')->with('sa_section', 'announcements');
    }

    public function toggleAnnouncement(Announcement $announcement)
    {
        $announcement->update(['is_active' => !$announcement->is_active]);
        return back()->with('sa_success', 'Announcement updated.')->with('sa_section', 'announcements');
    }

    public function destroyAnnouncement(Announcement $announcement)
    {
        $announcement->delete();
        return back()->with('sa_success', 'Announcement deleted.')->with('sa_section', 'announcements');
    }

    // ── News ─────────────────────────────────────────

    public function storeNews(Request $request)
    {
        $request->validate([
            'title'    => 'required|string|max:255',
            'body'     => 'required|string',
            'category' => 'required|in:academic,events,activity,achievement,general',
            'image'    => 'nullable|image|mimes:jpg,jpeg,png,webp|max:3072',
        ]);

        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('news', 'public');
        }

        News::create([
            'posted_by' => Auth::id(),
            'title'     => $request->title,
            'body'      => $request->body,
            'category'  => $request->category,
            'image'     => $imagePath,
            'is_active' => true,
        ]);

        return back()->with('sa_success', 'News article published!')->with('sa_section', 'news');
    }

    public function toggleNews(News $news)
    {
        $news->update(['is_active' => !$news->is_active]);
        return back()->with('sa_success', 'News article updated.')->with('sa_section', 'news');
    }

    public function destroyNews(News $news)
    {
        if ($news->image) Storage::disk('public')->delete($news->image);
        $news->delete();
        return back()->with('sa_success', 'News article deleted.')->with('sa_section', 'news');
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

        return back()->with('photo_success', 'Profile photo updated successfully.')
                     ->with('settings_tab', 'account');
    }

    // ──────────────────────────────────────────
    // BACKUP & RESTORE
    // ──────────────────────────────────────────

    private function backupDir(): string
    {
        $dir = storage_path('app/backups');
        if (!File::exists($dir)) {
            File::makeDirectory($dir, 0755, true);
        }
        return $dir;
    }

    private function listBackups(): array
    {
        $dir   = $this->backupDir();
        $files = File::files($dir);
        $backups = [];
        foreach ($files as $file) {
            if ($file->getExtension() === 'sql') {
                $backups[] = [
                    'name'     => $file->getFilename(),
                    'size'     => $this->formatBytes($file->getSize()),
                    'size_raw' => $file->getSize(),
                    'created'  => date('M d, Y h:i A', $file->getMTime()),
                    'ts'       => $file->getMTime(),
                ];
            }
        }
        usort($backups, fn($a, $b) => $b['ts'] - $a['ts']);
        return $backups;
    }

    public function createBackup(Request $request)
    {
        try {
            $db       = config('database.connections.' . config('database.default'));
            $host     = $db['host']     ?? '127.0.0.1';
            $port     = $db['port']     ?? 3306;
            $user     = $db['username'] ?? 'root';
            $pass     = $db['password'] ?? '';
            $name     = $db['database'] ?? '';
            $filename = 'backup_' . now()->format('Y-m-d_His') . '.sql';
            $path     = $this->backupDir() . DIRECTORY_SEPARATOR . $filename;

            $passPart = $pass ? '-p' . escapeshellarg($pass) : '';
            $cmd = sprintf(
                'mysqldump --host=%s --port=%s -u%s %s %s > %s 2>&1',
                escapeshellarg($host),
                escapeshellarg((string) $port),
                escapeshellarg($user),
                $passPart,
                escapeshellarg($name),
                escapeshellarg($path)
            );

            exec($cmd, $output, $code);

            if ($code !== 0 || !File::exists($path) || File::size($path) < 100) {
                File::delete($path);
                return response()->json(['success' => false, 'message' => 'Backup failed. Check that mysqldump is available on the server.']);
            }

            ActivityLogger::log('create', 'Database backup created: ' . $filename, 'Backup');
            return response()->json(['success' => true, 'message' => 'Backup created: ' . $filename, 'filename' => $filename]);
        } catch (\Throwable $e) {
            return response()->json(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
        }
    }

    public function downloadBackup(string $file)
    {
        $path = $this->backupDir() . DIRECTORY_SEPARATOR . basename($file);
        if (!File::exists($path)) {
            abort(404, 'Backup file not found.');
        }
        ActivityLogger::log('update', 'Backup downloaded: ' . basename($file), 'Backup');
        return response()->download($path);
    }

    public function deleteBackup(string $file)
    {
        $path = $this->backupDir() . DIRECTORY_SEPARATOR . basename($file);
        if (!File::exists($path)) {
            return response()->json(['success' => false, 'message' => 'File not found.']);
        }
        File::delete($path);
        ActivityLogger::log('delete', 'Backup deleted: ' . basename($file), 'Backup');
        return response()->json(['success' => true, 'message' => 'Backup deleted.']);
    }

    public function restoreBackup(Request $request, string $file)
    {
        try {
            $path = $this->backupDir() . DIRECTORY_SEPARATOR . basename($file);
            if (!File::exists($path)) {
                return response()->json(['success' => false, 'message' => 'Backup file not found.']);
            }

            $db   = config('database.connections.' . config('database.default'));
            $host = $db['host']     ?? '127.0.0.1';
            $port = $db['port']     ?? 3306;
            $user = $db['username'] ?? 'root';
            $pass = $db['password'] ?? '';
            $name = $db['database'] ?? '';

            $passPart = $pass ? '-p' . escapeshellarg($pass) : '';
            $cmd = sprintf(
                'mysql --host=%s --port=%s -u%s %s %s < %s 2>&1',
                escapeshellarg($host),
                escapeshellarg((string) $port),
                escapeshellarg($user),
                $passPart,
                escapeshellarg($name),
                escapeshellarg($path)
            );

            exec($cmd, $output, $code);

            if ($code !== 0) {
                return response()->json(['success' => false, 'message' => 'Restore failed: ' . implode(' ', $output)]);
            }

            ActivityLogger::log('update', 'Database restored from backup: ' . basename($file), 'Backup');
            return response()->json(['success' => true, 'message' => 'Database restored from ' . basename($file)]);
        } catch (\Throwable $e) {
            return response()->json(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
        }
    }

    public function enrollmentsData(Request $request)
    {
        $status = $request->get('status', 'all');
        $query  = \App\Models\Enrollment::select(
            'id','user_id','reference_number','status',
            'student_data','payment_status','payment_amount','total_fee',
            'created_at','updated_at'
        );
        if ($status !== 'all') {
            $query->where('status', $status);
        }
        $enrollments = $query->latest()->take(200)->get()->map(function ($e) {
            $data = is_string($e->student_data)
                ? json_decode($e->student_data, true)
                : (array) $e->student_data;
            return [
                'id'               => $e->id,
                'reference_number' => $e->reference_number,
                'status'           => $e->status,
                'payment_status'   => $e->payment_status,
                'payment_amount'   => $e->payment_amount,
                'total_fee'        => $e->total_fee,
                'created_at'       => $e->created_at?->format('M d, Y'),
                'first_name'       => $data['first_name']  ?? '',
                'last_name'        => $data['last_name']   ?? '',
                'grade_level'      => $data['grade_level'] ?? '',
            ];
        });

        $counts = \App\Models\Enrollment::selectRaw('status, count(*) as cnt')
            ->groupBy('status')->pluck('cnt', 'status');

        return response()->json(['enrollments' => $enrollments, 'counts' => $counts]);
    }

    private function formatBytes(int $bytes): string
    {
        if ($bytes >= 1048576) return round($bytes / 1048576, 2) . ' MB';
        if ($bytes >= 1024)    return round($bytes / 1024, 1)    . ' KB';
        return $bytes . ' B';
    }
}
