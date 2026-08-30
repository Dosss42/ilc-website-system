<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Enrollment;
use App\Models\StudentDocument;
use App\Models\StudentProfile;
use App\Models\Subject;
use App\Models\Section;
use App\Models\GuidanceRecord;
use App\Models\Announcement;
use App\Models\Promotion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class DashboardController extends Controller
{
    // ══════════════════════════════════════════
    // ADMIN DASHBOARD
    // ══════════════════════════════════════════
    public function index()
    {
        // School year range: configured present year → +10 years forward
        $configured = \App\Models\Setting::where('key', 'current_school_year')->value('value') ?? '2026-2027';
        $baseYear = (int) substr($configured, 0, 4);
        $schoolYears = [];
        for ($y = $baseYear; $y <= $baseYear + 10; $y++) {
            $schoolYears[] = $y . '-' . ($y + 1);
        }

        // ── Summary Counts ──
        $totalStudents = User::where('role', 'student')->count();
        $totalTeachers = User::where('role', 'teacher')->count();
        $studentCount = $totalStudents;
        $enrollmentCount = Enrollment::count();
        $financeCount = Enrollment::where('payment_status', '!=', 'paid')->count();

        // ── Dashboard Counts ──
        $paidCount = Enrollment::where('payment_status', 'paid')->count();
        $unpaidCount = Enrollment::where('payment_status', '!=', 'paid')->count();

        // ── Get Students Data ──
        $recentStudents = User::where('role', 'student')
            ->with(['latestEnrollment', 'enrollments'])
            ->orderByDesc('created_at')
            ->paginate(10, ['*'], 'page', request('page'));

        $students = User::where('role', 'student')
            ->with(['latestEnrollment', 'enrollments'])
            ->when(request('student_search'), fn($q) => 
                $q->where('name', 'like', '%' . request('student_search') . '%')
                  ->orWhere('email', 'like', '%' . request('student_search') . '%')
            )
            ->when(request('student_grade'), fn($q) => 
                $q->whereHas('latestEnrollment', fn($sq) => $sq->where('grade_level', request('student_grade')))
            )
            ->orderByDesc('created_at')
            ->paginate(15, ['*'], 'page', request('page'));

        // ── Get Teachers Data ──
        $teachers = User::where('role', 'teacher')->with('sections')->paginate(15);

        // ── Get Enrollment Data ──
        $enrollments = Enrollment::with('studentProfile')
            ->when(request('statusFilter'), fn($q) => 
                $q->where('status', request('statusFilter'))
            )
            ->orderByDesc('created_at')
            ->paginate(15);

        // ── Get Sections Data ──
        $sections = Section::with(['enrollments', 'subjects', 'schedules'])
            ->get();

        // ── Get Subjects Data ──
        $subjects = Subject::paginate(15);

        // ── Get Student Documents ──
        $studentDocuments = StudentDocument::with('user')
            ->orderByDesc('created_at')
            ->take(20)
            ->get();

        // ── Get Finance/Payment Data ──
        $allStudentsPayment = User::where('role', 'student')
            ->with(['enrollments', 'latestEnrollment.paymentInstallments'])
            ->paginate(15);

        $financePayments = \App\Models\PaymentTransaction::with(['enrollment:id,reference_number,grade_level,payment_status,total_fee,remaining_balance,payment_type,payment_option', 'user:id,name,email', 'installment', 'processedBy:id,name'])
            ->orderByDesc('created_at')
            ->paginate(15);

        // ── Get Installment Data ──
        $installmentEnrollments = Enrollment::with('paymentInstallments')
            ->where('payment_status', '!=', 'paid')
            ->paginate(15);

        // ── Get Guidance Records ──
        $guidanceRecords = GuidanceRecord::with('student')
            ->orderByDesc('created_at')
            ->paginate(15);

        // ── Get Announcements ──
        $announcements = Announcement::orderByDesc('created_at')->take(20)->get();

        // ── Get News ──
        $news = \App\Models\News::orderByDesc('created_at')->take(20)->get();

        // ── Assessment & Promotion ──
        // Assessed: Grade 1-6, new or returning students who completed a year at ILC.
        // NOT assessed: Nursery/Kinder (auto-advance), Transferees (first year at ILC).
        $assessStudents = User::where('role', 'student')
            ->with(['latestEnrollment', 'promotions' => fn($q) => $q->orderByDesc('id')])
            ->whereHas('latestEnrollment', fn($q) =>
                $q->whereIn('status', ['enrolled', 'completed'])
            )
            ->orderBy('name')
            ->get()
            ->filter(function ($s) {
                $enr   = $s->latestEnrollment;
                if (!$enr) return false;
                $gl    = $enr->student_data['grade_level'] ?? ($enr->grade_level ?? '');
                $stype = $enr->student_data['student_type'] ?? '';
                // Must be Grade 1-6 and NOT a transferee
                return in_array($gl, ['grade1','grade2','grade3','grade4','grade5','grade6'])
                    && $stype !== 'transferee';
            })
            ->values();

        // ── Fee Breakdowns ──
        $feeBreakdowns = [];

        // ── Filter/Sort Variables ──
        $sort = request('sort', 'newest');
        $statusFilter = request('statusFilter', 'all');
        $gradeFilter = request('gradeFilter', 'all');
        $studentSearch = request('student_search', '');
        $studentGradeFilter = request('student_grade', '');
        $studentStatusFilter = request('student_status', '');
        $studentPaymentFilter = request('student_payment', '');
        $studentSchoolYearFilter = request('student_schoolyear', '');

        // Check which grades have sections (for schedule filter)
        $gradesWithStudents = Section::whereNotNull('grade_level')
            ->distinct()
            ->pluck('grade_level')
            ->toArray();

        // Load all schedules for master list view
        $allSchedules = \App\Models\Schedule::with(['section', 'subject', 'teacher'])
            ->orderByRaw("FIELD(day_of_week,'Monday','Tuesday','Wednesday','Thursday','Friday','Saturday','Sunday')")
            ->orderBy('start_time')
            ->get();

        return view('adminDashboard', compact(
            'totalStudents',
            'totalTeachers',
            'studentCount',
            'enrollmentCount',
            'financeCount',
            'paidCount',
            'unpaidCount',
            'recentStudents',
            'students',
            'teachers',
            'enrollments',
            'sections',
            'subjects',
            'studentDocuments',
            'allStudentsPayment',
            'financePayments',
            'installmentEnrollments',
            'guidanceRecords',
            'announcements',
            'news',
            'feeBreakdowns',
            'schoolYears',
            'sort',
            'statusFilter',
            'gradeFilter',
            'studentSearch',
            'studentGradeFilter',
            'studentStatusFilter',
            'studentPaymentFilter',
            'studentSchoolYearFilter',
            'gradesWithStudents',
            'allSchedules',
            'assessStudents'
        ));
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
}
