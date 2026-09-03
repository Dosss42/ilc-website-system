<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\EnrollmentController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\StudentPortalController;
use App\Http\Controllers\SuperAdminController;
use App\Http\Controllers\SubjectController;
use App\Http\Controllers\SectionController;
use App\Http\Controllers\ScheduleController;
use App\Http\Controllers\TeacherController;
use App\Http\Controllers\SummerClassController;
use App\Http\Controllers\GuidanceController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\FeeSettingController;
use App\Http\Controllers\TeacherAssignmentController;

// ─────────────────────────────────────────
// PUBLIC ROUTES
// ─────────────────────────────────────────
Route::get('/', function () {
    $enrollmentOpen = \App\Models\Setting::get('enrollment_open', true);

    // Visitor counter — increment once per session (unique visit)
    if (!session()->has('visited')) {
        session(['visited' => true]);
        $current = (int) \App\Models\Setting::get('visitor_count', 0);
        \App\Models\Setting::set('visitor_count', $current + 1);
    }
    $visitorCount = (int) \App\Models\Setting::get('visitor_count', 0);

    $latestNews          = \App\Models\News::where('is_active', true)->latest()->limit(3)->get();
    $latestAnnouncements = \App\Models\Announcement::where('is_active', true)->where('audience', 'all')->latest()->limit(3)->get();

    return view('home', compact('enrollmentOpen', 'visitorCount', 'latestNews', 'latestAnnouncements'));
})->name('home');
Route::get('/about',         fn() => view('about',         ['visitorCount' => (int) \App\Models\Setting::get('visitor_count', 0)]))->name('about');
Route::get('/aims',          fn() => view('aims'))->name('aims');
Route::get('/academics',     fn() => view('academics',     ['visitorCount' => (int) \App\Models\Setting::get('visitor_count', 0)]))->name('academics');
Route::get('/admission', function () {
    $enrollmentOpen = \App\Models\Setting::get('enrollment_open', true);
    $visitorCount   = (int) \App\Models\Setting::get('visitor_count', 0);
    return view('admission', compact('enrollmentOpen', 'visitorCount'));
})->name('admission');
Route::get('/news', function () {
    $category     = request('category');
    $newsQuery    = \App\Models\News::where('is_active', true);
    if ($category) $newsQuery->where('category', $category);
    $featuredNews = \App\Models\News::where('is_active', true)->latest()->first();
    $news         = $newsQuery->when($featuredNews, fn($q) => $q->where('id', '!=', $featuredNews->id))->latest()->paginate(9)->withQueryString();
    $recentNews   = \App\Models\News::where('is_active', true)->latest()->limit(5)->get();
    $categories   = \App\Models\News::where('is_active', true)->selectRaw('category, count(*) as total')->groupBy('category')->get();
    $sidebarAnns  = \App\Models\Announcement::where('is_active', true)->where('audience', 'all')->latest()->limit(4)->get();
    $visitorCount = (int) \App\Models\Setting::get('visitor_count', 0);
    return view('news', compact('news', 'featuredNews', 'recentNews', 'categories', 'sidebarAnns', 'visitorCount', 'category'));
})->name('news');

Route::get('/news/{news}', function (\App\Models\News $news) {
    abort_if(!$news->is_active, 404);
    $relatedNews  = \App\Models\News::where('is_active', true)->where('id', '!=', $news->id)->where('category', $news->category)->latest()->limit(3)->get();
    $sidebarAnns  = \App\Models\Announcement::where('is_active', true)->where('audience', 'all')->latest()->limit(4)->get();
    $visitorCount = (int) \App\Models\Setting::get('visitor_count', 0);
    return view('news-show', compact('news', 'relatedNews', 'sidebarAnns', 'visitorCount'));
})->name('news.show');

Route::get('/announcements', function () {
    $category             = request('category');
    $annQuery             = \App\Models\Announcement::where('is_active', true)->where('audience', 'all');
    if ($category) $annQuery->where('category', $category);
    $featuredAnnouncement = \App\Models\Announcement::where('is_active', true)->where('audience', 'all')->latest()->first();
    $announcements        = $annQuery->when($featuredAnnouncement, fn($q) => $q->where('id', '!=', $featuredAnnouncement->id))->latest()->paginate(12)->withQueryString();
    $recentAnns           = \App\Models\Announcement::where('is_active', true)->where('audience', 'all')->latest()->limit(5)->get();
    $annCategories        = \App\Models\Announcement::where('is_active', true)->where('audience', 'all')->selectRaw('category, count(*) as total')->groupBy('category')->get();
    $scheduleAnns         = \App\Models\Announcement::where('is_active', true)->where('audience', 'all')->where('category', 'activity')->latest()->limit(5)->get();
    $reminderAnns         = \App\Models\Announcement::where('is_active', true)->where('audience', 'all')->where('category', 'reminder')->latest()->limit(4)->get();
    $visitorCount         = (int) \App\Models\Setting::get('visitor_count', 0);
    return view('announcements', compact('announcements', 'featuredAnnouncement', 'recentAnns', 'annCategories', 'scheduleAnns', 'reminderAnns', 'visitorCount', 'category'));
})->name('announcements');

Route::get('/announcements/{announcement}', function (\App\Models\Announcement $announcement) {
    abort_if(!$announcement->is_active || $announcement->audience !== 'all', 404);
    $relatedAnns  = \App\Models\Announcement::where('is_active', true)->where('audience', 'all')->where('id', '!=', $announcement->id)->latest()->limit(3)->get();
    $visitorCount = (int) \App\Models\Setting::get('visitor_count', 0);
    return view('announcements-show', compact('announcement', 'relatedAnns', 'visitorCount'));
})->name('announcements.show');
Route::get('/contact',       fn() => view('contact',       ['visitorCount' => (int) \App\Models\Setting::get('visitor_count', 0)]))->name('contact');
Route::post('/contact/send', [\App\Http\Controllers\ContactController::class, 'send'])->name('contact.send');
Route::get('/search',        fn() => view('search'))->name('search');
Route::get('/terms',         fn() => view('terms_and_conditions'))->name('terms');
Route::get('/privacy',       fn() => view('privacy_policy'))->name('privacy');


Route::get('/enrollment/process', function () {
    if (!\App\Models\Setting::get('enrollment_open', true)) {
        return redirect()->route('admission')->with('enrollment_closed', true);
    }
    return view('enrollment_process');
})->name('enrollment.process');

Route::get('/enrollment/application', function () {
    if (!\App\Models\Setting::get('enrollment_open', true)) {
        return redirect()->route('admission')->with('enrollment_closed', true);
    }
    return view('enrollment-application');
})->name('enrollment.form');

Route::post('/enrollment/submit',     [EnrollmentController::class, 'store'])->name('enrollment.submit');
Route::post('/enrollment/send-otp',   [EnrollmentController::class, 'sendOtp'])->name('enrollment.send-otp');
Route::post('/enrollment/verify-otp', [EnrollmentController::class, 'verifyOtp'])->name('enrollment.verify-otp');

// REST API Routes for React Components
Route::post('/api/enrollment/submit', [EnrollmentController::class, 'submitApi'])->name('api.enrollment.submit');

// ─────────────────────────────────────────
// AUTH ROUTES
// login_register.blade.php handles BOTH
// login and register in one page (two panels)
// ─────────────────────────────────────────

// GET /login  → shows login_register.blade.php (login panel active)
Route::get('/login',    fn() => view('login_register'))->name('login');

// Registration is enrollment-based only — redirect direct access to admission page
Route::get('/register',  fn() => redirect()->route('admission'))->name('register');

// POST /login → rate limiting handled in AuthController (redirects back with error instead of 429 page)
Route::post('/login',    [AuthController::class, 'login'])->name('login.submit');

// POST /register → blocked; accounts are created through enrollment only
Route::post('/register', fn() => redirect()->route('admission'))->name('register.submit');

// POST /logout
Route::post('/logout',   [AuthController::class, 'logout'])->name('logout');

// ─────────────────────────────────────────
// EMAIL VERIFICATION ROUTES
// ─────────────────────────────────────────

// 1. Show "please check your email" page (after register)
Route::get('/email/verify', function () {
    return view('auth.verify-email');
})->middleware('auth')->name('verification.notice');

// 2. Handle the verification link clicked from email
Route::get('/email/verify/{id}/{hash}', function (EmailVerificationRequest $request) {
    $request->fulfill(); // sets email_verified_at
    return redirect()->route('profile.complete')
           ->with('success', '✅ Email verified! Please complete your profile to proceed.');
})->middleware(['auth', 'signed'])->name('verification.verify');

// 3. Resend verification email (button in verify-email.blade.php)
Route::post('/email/verification-notification', function (Request $request) {
    $request->user()->sendEmailVerificationNotification();
    return back()->with('success', 'Verification link resent! Check your inbox.');
})->middleware(['auth', 'throttle:6,1'])->name('verification.send');

// ─────────────────────────────────────────
// SUPERADMIN DASHBOARD
// ─────────────────────────────────────────
Route::middleware(['auth', 'superadmin'])->prefix('superadmin')->name('superadmin.')->group(function () {
    Route::get('/dashboard', [SuperAdminController::class, 'dashboard'])->name('dashboard');

    // USER MANAGEMENT
    Route::prefix('users')->name('users.')->group(function () {
        Route::post('/', [SuperAdminController::class, 'storeUser'])->name('store');
        Route::put('/{user}', [SuperAdminController::class, 'updateUser'])->name('update');
        Route::delete('/{user}', [SuperAdminController::class, 'deleteUser'])->name('destroy');
        Route::post('/{user}/toggle-status', [SuperAdminController::class, 'toggleUserStatus'])->name('toggle-status');
        Route::post('/{user}/reset-password', [SuperAdminController::class, 'resetPassword'])->name('reset-password');
    });

    // Backup & Restore
    Route::post('/backup/create',          [SuperAdminController::class, 'createBackup'])->name('backup.create');
    Route::get('/backup/download/{file}',  [SuperAdminController::class, 'downloadBackup'])->name('backup.download');
    Route::delete('/backup/{file}',        [SuperAdminController::class, 'deleteBackup'])->name('backup.destroy');
    Route::post('/backup/restore/{file}',  [SuperAdminController::class, 'restoreBackup'])->name('backup.restore');

    // Settings
    Route::put('/settings/password', [SuperAdminController::class, 'updatePassword'])->name('settings.password');
    Route::post('/settings/photo',   [SuperAdminController::class, 'updatePhoto'])->name('settings.photo');

    // Announcements
    Route::post('/announcements',                        [SuperAdminController::class, 'storeAnnouncement'])->name('announcements.store');
    Route::post('/announcements/{announcement}/toggle',  [SuperAdminController::class, 'toggleAnnouncement'])->name('announcements.toggle');
    Route::delete('/announcements/{announcement}',       [SuperAdminController::class, 'destroyAnnouncement'])->name('announcements.destroy');

    // News
    Route::post('/news',                [SuperAdminController::class, 'storeNews'])->name('news.store');
    Route::post('/news/{news}/toggle',  [SuperAdminController::class, 'toggleNews'])->name('news.toggle');
    Route::delete('/news/{news}',       [SuperAdminController::class, 'destroyNews'])->name('news.destroy');

    // Academic management data (proxies to admin JSON endpoints)
    Route::get('/enrollments-data', [SuperAdminController::class, 'enrollmentsData'])->name('enrollments-data');
});

// ─────────────────────────────────────────
// ADMIN / REGISTRAR DASHBOARD
// ─────────────────────────────────────────
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [EnrollmentController::class, 'adminIndex'])->name('dashboard');
    
    // ENROLLMENT MANAGEMENT
    Route::prefix('enrollments')->name('enrollments.')->group(function () {
        Route::get('/', [EnrollmentController::class, 'adminIndex'])->name('index');
        Route::get('/{enrollment}', [EnrollmentController::class, 'adminShow'])->name('show');
        Route::post('/{enrollment}/approve', [EnrollmentController::class, 'approve'])->name('approve');
        Route::post('/{enrollment}/decline', [EnrollmentController::class, 'decline'])->name('decline');
        Route::post('/{enrollment}/payment', [EnrollmentController::class, 'updatePayment'])->name('payment');
        Route::post('/{enrollment}/change-section', [EnrollmentController::class, 'changeSection'])->name('changeSection');
        Route::post('/walk-in', [EnrollmentController::class, 'walkInEnrollment'])->name('walk-in');
    });
    
    // SUBJECT MANAGEMENT
    Route::prefix('subjects')->name('subjects.')->group(function () {
        Route::get('/', [SubjectController::class, 'index'])->name('index');
        Route::post('/', [SubjectController::class, 'store'])->name('store');
        Route::get('/{subject}', [SubjectController::class, 'show'])->name('show');
        Route::put('/{subject}', [SubjectController::class, 'update'])->name('update');
        Route::delete('/{subject}', [SubjectController::class, 'destroy'])->name('destroy');
    });
    
    // SECTION MANAGEMENT
    Route::prefix('sections')->name('sections.')->group(function () {
        Route::get('/', [SectionController::class, 'index'])->name('index');
        Route::post('/', [SectionController::class, 'store'])->name('store');
        Route::post('/auto-assign', [SectionController::class, 'autoAssign'])->name('autoAssign');
        Route::get('/{section}', [SectionController::class, 'show'])->name('show');
        Route::put('/{section}', [SectionController::class, 'update'])->name('update');
        Route::delete('/{section}', [SectionController::class, 'destroy'])->name('destroy');
        Route::post('/{section}/assign-teacher', [SectionController::class, 'assignTeacher'])->name('assignTeacher');
        Route::post('/{section}/add-student', [SectionController::class, 'addStudent'])->name('addStudent');
        Route::post('/{section}/remove-student', [SectionController::class, 'removeStudent'])->name('removeStudent');
        Route::post('/{section}/transfer-student', [SectionController::class, 'transferStudent'])->name('transferStudent');
        Route::get('/{section}/subjects', [SectionController::class, 'getSubjects'])->name('getSubjects');
        Route::post('/{section}/subjects', [SectionController::class, 'assignSubjects'])->name('subjects');
    });
    
    // STUDENT MANAGEMENT
    Route::prefix('students')->name('students.')->group(function () {
        Route::get('/', [EnrollmentController::class, 'indexStudents'])->name('index');
        Route::post('/{id}/restore', [EnrollmentController::class, 'restoreStudent'])->name('restore');
        Route::delete('/{id}/force', [EnrollmentController::class, 'forceDeleteStudent'])->name('force-delete');
        Route::get('/{user}/sf10', [EnrollmentController::class, 'downloadSF10'])->name('sf10');
        Route::get('/{user}', [EnrollmentController::class, 'showStudent'])->name('show');
        Route::put('/{user}', [EnrollmentController::class, 'updateStudent'])->name('update');
        Route::delete('/{user}', [EnrollmentController::class, 'deleteStudent'])->name('destroy');
    });

    // GRADE OVERSIGHT
    Route::get('/grades/pending',              [EnrollmentController::class, 'pendingGrades'])->name('admin.grades.pending');
    Route::post('/grades/approve',             [EnrollmentController::class, 'approveGrades'])->name('admin.grades.approve');
    Route::post('/grades/reject',              [EnrollmentController::class, 'rejectGrades'])->name('admin.grades.reject');

    // CONTACT MESSAGES INBOX
    Route::patch('/messages/{message}/status', [\App\Http\Controllers\ContactController::class, 'updateStatus'])->name('messages.status');
    Route::delete('/messages/{message}',       [\App\Http\Controllers\ContactController::class, 'destroy'])->name('messages.destroy');

    // DOCUMENT MANAGEMENT
    Route::post('/documents/{document}/approve', [EnrollmentController::class, 'approveDocument'])->name('documents.approve');
    Route::post('/documents/{document}/reject', [EnrollmentController::class, 'rejectDocument'])->name('documents.reject');
    Route::delete('/documents/{document}', [EnrollmentController::class, 'deleteDocument'])->name('documents.destroy');

    // ASSESSMENT & PROMOTION (per-student)
    Route::get('/assessment/candidates',                  [EnrollmentController::class, 'assessmentCandidates'])->name('assessment.candidates');
    Route::post('/assessment/{user}/promote',             [EnrollmentController::class, 'assessPromotion'])->name('assessment.promote');
    Route::get('/assessment/sections-for-grade',          [EnrollmentController::class, 'sectionsForGrade'])->name('assessment.sections');

    // STUDENT MANAGEMENT — status change + assess data endpoints
    Route::post('/enrollment/{enrollment}/change-status', [EnrollmentController::class, 'changeEnrollmentStatus'])->name('enrollment.change-status');
    Route::get('/student/{user}/grades-for-assess',       [EnrollmentController::class, 'getStudentGradesForAssess'])->name('student.grades-for-assess');
    Route::get('/student/{user}/documents-for-assess',    [EnrollmentController::class, 'getStudentDocumentsForAssess'])->name('student.documents-for-assess');
    Route::get('/student/{user}/guidance-for-assess',      [EnrollmentController::class, 'getStudentGuidanceForAssess'])->name('student.guidance-for-assess');
    Route::get('/student/{user}/summer-for-assess',        [EnrollmentController::class, 'getStudentSummerForAssess'])->name('student.summer-for-assess');

    // MASS PROMOTION (kept for legacy, hidden from UI)
    Route::post('/students/mass-promote', [EnrollmentController::class, 'massPromote'])->name('students.mass-promote');
    Route::get('/students/promotion-history', [EnrollmentController::class, 'promotionHistory'])->name('students.promotion-history');

    // SCHEDULE MANAGEMENT
    Route::prefix('schedules')->name('schedules.')->group(function () {
        Route::get('/', [ScheduleController::class, 'index'])->name('index');
        Route::post('/', [ScheduleController::class, 'store'])->name('store');
        Route::post('/copy-term', [ScheduleController::class, 'copyTerm'])->name('copy-term');
        Route::get('/{schedule}', [ScheduleController::class, 'show'])->name('show');
        Route::put('/{schedule}', [ScheduleController::class, 'update'])->name('update');
        Route::delete('/{schedule}', [ScheduleController::class, 'destroy'])->name('destroy');
        Route::get('/section/{section}', [ScheduleController::class, 'getSectionSchedules'])->name('getSectionSchedules');
        Route::get('/teacher/{teacher}', [ScheduleController::class, 'getTeacherSchedules'])->name('getTeacherSchedules');
    });

    // TEACHER MANAGEMENT
    Route::prefix('teachers')->name('teachers.')->group(function () {
        Route::get('/', [TeacherController::class, 'index'])->name('index');
        Route::post('/', [TeacherController::class, 'store'])->name('store');
        Route::get('/{teacher}', [TeacherController::class, 'show'])->name('show');
        Route::put('/{teacher}', [TeacherController::class, 'update'])->name('update');
        Route::delete('/{teacher}', [TeacherController::class, 'destroy'])->name('destroy');
    });

    // TEACHER ASSIGNMENT MANAGEMENT
    Route::prefix('teacher-assignments')->name('teacher-assignments.')->group(function () {
        Route::get('/', [TeacherAssignmentController::class, 'getAssignments'])->name('index');
        Route::post('/', [TeacherAssignmentController::class, 'store'])->name('store');
        Route::put('/{id}', [TeacherAssignmentController::class, 'update'])->name('update');
        Route::delete('/{id}', [TeacherAssignmentController::class, 'destroy'])->name('destroy');
    });

    // SUMMER CLASS MANAGEMENT
    Route::prefix('summer-classes')->name('summer-classes.')->group(function () {
        Route::get('/', [SummerClassController::class, 'index'])->name('index');
        Route::post('/', [SummerClassController::class, 'store'])->name('store');
        Route::get('/eligible-students', [SummerClassController::class, 'getEligibleStudents'])->name('eligible');
        Route::get('/{summerClass}', [SummerClassController::class, 'show'])->name('show');
        Route::put('/{summerClass}', [SummerClassController::class, 'update'])->name('update');
        Route::delete('/{summerClass}', [SummerClassController::class, 'destroy'])->name('destroy');
        Route::post('/{summerClass}/enroll', [SummerClassController::class, 'enrollStudent'])->name('enroll');
        Route::delete('/{summerClass}/remove/{studentId}', [SummerClassController::class, 'removeStudent'])->name('remove');
        Route::put('/{summerClass}/grade/{studentId}', [SummerClassController::class, 'updateGrade'])->name('grade');
    });

    // GUIDANCE RECORDS
    Route::prefix('guidance')->name('guidance.')->group(function () {
        Route::get('/', [GuidanceController::class, 'index'])->name('index');
        Route::post('/', [GuidanceController::class, 'store'])->name('store');
        Route::get('/{guidance}', [GuidanceController::class, 'show'])->name('show');
        Route::put('/{guidance}', [GuidanceController::class, 'update'])->name('update');
        Route::delete('/{guidance}', [GuidanceController::class, 'destroy'])->name('destroy');
    });

    // REPORTS
    Route::prefix('reports')->name('reports.')->group(function () {
        Route::get('/academic', [ReportController::class, 'academicReport'])->name('academic');
        Route::get('/financial', [ReportController::class, 'financialReport'])->name('financial');
        Route::get('/enrollment', [ReportController::class, 'enrollmentReport'])->name('enrollment');
        Route::get('/guidance', [ReportController::class, 'guidanceReport'])->name('guidance');
        Route::get('/dashboard-summary', [ReportController::class, 'dashboardSummary'])->name('dashboard-summary');
    });

    // SETTINGS
    Route::prefix('settings')->name('settings.')->group(function () {
        Route::get('/', [SettingController::class, 'index'])->name('index');
        Route::get('/{group}', [SettingController::class, 'getGroup'])->name('group');
        Route::put('/', [SettingController::class, 'update'])->name('update');
        Route::post('/seed', [SettingController::class, 'seedDefaults'])->name('seed');
        Route::post('/toggle-enrollment', [SettingController::class, 'toggleEnrollment'])->name('toggle-enrollment');
        Route::post('/toggle-maintenance', [SettingController::class, 'toggleMaintenance'])->name('toggle-maintenance');
    });

    // FEE SETTINGS (dedicated endpoint)
    Route::get('/fee-settings', [FeeSettingController::class, 'index'])->name('fee-settings.index');
    Route::put('/fee-settings', [FeeSettingController::class, 'update'])->name('fee-settings.update');

    // ANNOUNCEMENTS (admin can post/manage)
    Route::post('/announcements',                       [\App\Http\Controllers\SuperAdminController::class, 'storeAnnouncement'])->name('announcements.store');
    Route::post('/announcements/{announcement}/toggle', [\App\Http\Controllers\SuperAdminController::class, 'toggleAnnouncement'])->name('announcements.toggle');
    Route::delete('/announcements/{announcement}',      [\App\Http\Controllers\SuperAdminController::class, 'destroyAnnouncement'])->name('announcements.destroy');

    // NEWS (admin can post/manage)
    Route::post('/news',               [\App\Http\Controllers\SuperAdminController::class, 'storeNews'])->name('news.store');
    Route::post('/news/{news}/toggle', [\App\Http\Controllers\SuperAdminController::class, 'toggleNews'])->name('news.toggle');
    Route::delete('/news/{news}',      [\App\Http\Controllers\SuperAdminController::class, 'destroyNews'])->name('news.destroy');

    // SCHEDULE CLEANUP — list all schedules with delete buttons
    Route::get('/schedule-cleanup', function () {
        $schedules = \App\Models\Schedule::with(['section', 'subject', 'teacher'])
            ->orderBy('id', 'desc')
            ->get();
        return view('schedule-cleanup', compact('schedules'));
    })->name('schedule-cleanup');

    // Settings — account
    Route::put('/settings/password', [\App\Http\Controllers\Admin\DashboardController::class, 'updatePassword'])->name('settings.password');
    Route::post('/settings/photo',   [\App\Http\Controllers\Admin\DashboardController::class, 'updatePhoto'])->name('settings.photo');
});

// ─────────────────────────────────────────
// PAYMENT MANAGEMENT (Auth only - accessible from admin dashboard)
// ─────────────────────────────────────────
Route::middleware(['auth'])->prefix('admin/payments')->name('admin.payments.')->group(function () {
    Route::get('/{payment}', [\App\Http\Controllers\Finance\DashboardController::class, 'paymentDetails'])->name('details');
    Route::post('/{payment}/approve', [\App\Http\Controllers\Finance\DashboardController::class, 'approvePayment'])->name('approve');
    Route::post('/{payment}/reject', [\App\Http\Controllers\Finance\DashboardController::class, 'rejectPayment'])->name('reject');
    Route::delete('/{payment}', [\App\Http\Controllers\Finance\DashboardController::class, 'deletePayment'])->name('destroy');
});

// Secure document viewing route
Route::middleware(['auth'])->get('/documents/{document}/view', [\App\Http\Controllers\Finance\DashboardController::class, 'viewDocument'])->name('documents.view');

// ─────────────────────────────────────────
// PAYMENT APPROVAL ROUTES (Auth only - for admin dashboard)
// ─────────────────────────────────────────
Route::middleware(['auth'])->prefix('payments')->name('payments.')->group(function () {
    Route::get('/test', function () { return response()->json(['ok' => true]); })->name('test');
    Route::post('/{id}/approve', [\App\Http\Controllers\Finance\DashboardController::class, 'approvePayment'])->name('approve');
    Route::post('/{id}/reject', [\App\Http\Controllers\Finance\DashboardController::class, 'rejectPayment'])->name('reject');
});

// ─────────────────────────────────────────
// ADMIN PAYMENT PROCESSING (Auth only - for walk-in payments)
// ─────────────────────────────────────────
Route::middleware(['auth'])->prefix('admin/enrollments')->name('admin.enrollments.')->group(function () {
    Route::post('/{enrollment}/payment', [\App\Http\Controllers\Finance\DashboardController::class, 'processAdminPayment'])->name('payment');
});

// ─────────────────────────────────────────
// FINANCE PORTAL - AUTHENTICATION
// ─────────────────────────────────────────
Route::prefix('finance')->name('finance.')->group(function () {
    // Public auth routes
    Route::get('/login', [\App\Http\Controllers\Finance\AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [\App\Http\Controllers\Finance\AuthController::class, 'login'])->name('login.submit');
    Route::post('/logout', [\App\Http\Controllers\Finance\AuthController::class, 'logout'])->name('logout');
});

// ─────────────────────────────────────────
// FINANCE PORTAL - PROTECTED ROUTES
// ─────────────────────────────────────────
Route::middleware([\App\Http\Middleware\FinanceMiddleware::class])->prefix('finance')->name('finance.')->group(function () {
    // Main Dashboard
    Route::get('/dashboard', [\App\Http\Controllers\Finance\DashboardController::class, 'index'])->name('dashboard');
    
    // Profile Management
    Route::get('/profile', [\App\Http\Controllers\Finance\AuthController::class, 'showProfile'])->name('profile');
    Route::put('/profile', [\App\Http\Controllers\Finance\AuthController::class, 'updateProfile'])->name('profile.update');
    
    // Change Password
    Route::get('/change-password', [\App\Http\Controllers\Finance\AuthController::class, 'showChangePasswordForm'])->name('change-password');
    Route::post('/change-password', [\App\Http\Controllers\Finance\AuthController::class, 'changePassword']);
    
    // Payments Management (view-only — no approve/reject)
    Route::prefix('payments')->name('payments.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Finance\DashboardController::class, 'payments'])->name('index');
    });
    
    // All-Students Payment Overview
    Route::get('/students', [\App\Http\Controllers\Finance\DashboardController::class, 'students'])->name('students.index');

    // Installments Management
    Route::prefix('installments')->name('installments.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Finance\DashboardController::class, 'installments'])->name('index');
        Route::get('/{enrollment}/details', [\App\Http\Controllers\Finance\DashboardController::class, 'installmentDetails'])->name('details');
        Route::post('/{enrollment}/pay', [\App\Http\Controllers\Finance\DashboardController::class, 'recordWalkInPayment'])->name('pay');
    });

    // Admin Payment Processing
    Route::post('/enrollments/{enrollment}/payment', [\App\Http\Controllers\Finance\DashboardController::class, 'processAdminPayment'])->name('process-payment');
    
    // Fee Management
    Route::prefix('fees')->name('fees.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Finance\DashboardController::class, 'fees'])->name('index');
        Route::post('/update', [\App\Http\Controllers\Finance\DashboardController::class, 'updateFees'])->name('update');
    });
    
    // Reports
    Route::prefix('reports')->name('reports.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Finance\DashboardController::class, 'reports'])->name('index');
    });
});

// ─────────────────────────────────────────
// PROMISSORY NOTES (Admin + Finance access)
// ─────────────────────────────────────────
Route::middleware([\App\Http\Middleware\FinanceMiddleware::class])->prefix('promissory-notes')->name('promissory.')->group(function () {
    Route::get('/',                  [\App\Http\Controllers\PromissoryNoteController::class, 'getNotes'])->name('list');
    Route::post('/',                 [\App\Http\Controllers\PromissoryNoteController::class, 'store'])->name('store');
    Route::put('/{note}/status',     [\App\Http\Controllers\PromissoryNoteController::class, 'updateStatus'])->name('status');
    Route::get('/{note}/print',      [\App\Http\Controllers\PromissoryNoteController::class, 'printNote'])->name('print');
    Route::delete('/{note}',         [\App\Http\Controllers\PromissoryNoteController::class, 'destroy'])->name('destroy');
});

// ─────────────────────────────────────────
// CASHIER PORTAL
// ─────────────────────────────────────────
// Xendit webhook — no CSRF (called by Xendit servers)
Route::post('/cashier/webhook/xendit', [\App\Http\Controllers\CashierController::class, 'xenditWebhook'])
    ->name('cashier.webhook.xendit')
    ->withoutMiddleware([\Illuminate\Foundation\Http\Middleware\VerifyCsrfToken::class]);

// Root /cashier → redirect to login (or dashboard if already authenticated)
Route::get('/cashier', function () {
    if (\Illuminate\Support\Facades\Auth::guard('cashier')->check()) {
        return redirect()->route('cashier.dashboard');
    }
    return redirect()->route('cashier.login');
})->name('cashier.root');

Route::prefix('cashier')->name('cashier.')->group(function () {

    // ── Public: login / logout ──
    Route::get('/login',  [\App\Http\Controllers\CashierController::class, 'showLogin'])->name('login');
    Route::post('/login', [\App\Http\Controllers\CashierController::class, 'login'])->name('login.post');
    Route::post('/logout', [\App\Http\Controllers\CashierController::class, 'logout'])->name('logout');

    // ── Protected: require cashier role ──
    Route::middleware([\App\Http\Middleware\CashierMiddleware::class])->group(function () {
        Route::get('/dashboard', [\App\Http\Controllers\CashierController::class, 'dashboard'])->name('dashboard');
        Route::get('/students/search', [\App\Http\Controllers\CashierController::class, 'searchStudent'])->name('students.search');
        Route::get('/students', [\App\Http\Controllers\CashierController::class, 'listStudents'])->name('students.list');
        Route::get('/payment-options', [\App\Http\Controllers\CashierController::class, 'getPaymentOptions'])->name('payment.options');
        Route::post('/enrollment/set-plan', [\App\Http\Controllers\CashierController::class, 'setEnrollmentPlan'])->name('enrollment.set-plan');
        Route::get('/daily-report', [\App\Http\Controllers\CashierController::class, 'dailyReport'])->name('daily.report');
        Route::get('/receipts', [\App\Http\Controllers\CashierController::class, 'receiptsList'])->name('receipts.list');
        Route::post('/payment/cash', [\App\Http\Controllers\CashierController::class, 'processCash'])->name('payment.cash');
        Route::post('/payment/xendit-link', [\App\Http\Controllers\CashierController::class, 'generateXenditLink'])->name('payment.xendit-link');
        Route::post('/change-password', [\App\Http\Controllers\CashierController::class, 'changePassword'])->name('change-password');
    });
});

// ─────────────────────────────────────────
// FINANCE USER MANAGEMENT (Admin/Superadmin Only)
// ─────────────────────────────────────────
Route::middleware(['auth', 'admin'])->prefix('admin/finance-users')->name('admin.finance.')->group(function () {
    Route::get('/', [\App\Http\Controllers\Finance\AuthController::class, 'listFinanceUsers'])->name('index');
    Route::post('/', [\App\Http\Controllers\Finance\AuthController::class, 'createFinanceUser'])->name('create');
    Route::post('/{user}/toggle-status', [\App\Http\Controllers\Finance\AuthController::class, 'toggleUserStatus'])->name('toggle-status');
});

// ─────────────────────────────────────────
// TEACHER DASHBOARD
// ─────────────────────────────────────────
Route::middleware(['auth', 'maintenance'])->prefix('teacher')->name('teacher.')->group(function () {
    Route::get('/dashboard', [\App\Http\Controllers\Teacher\DashboardController::class, 'index'])->name('dashboard');

    // My Students — grade management
    Route::post('/grades/load-class',  [\App\Http\Controllers\Teacher\DashboardController::class, 'loadClassRecord'])->name('grades.loadClass');
    Route::post('/grades/save',        [\App\Http\Controllers\Teacher\DashboardController::class, 'saveGrades'])->name('grades.save');
    Route::get('/grades/export',       [\App\Http\Controllers\Teacher\DashboardController::class, 'exportGrades'])->name('grades.export');
    Route::get('/grades/template',     [\App\Http\Controllers\Teacher\DashboardController::class, 'downloadGradeTemplate'])->name('grades.template');
    Route::post('/grades/import',      [\App\Http\Controllers\Teacher\DashboardController::class, 'importGrades'])->name('grades.import');
    Route::post('/grades/drafts',      [\App\Http\Controllers\Teacher\DashboardController::class, 'getDrafts'])->name('grades.drafts');
    Route::post('/grades/submit-draft',[\App\Http\Controllers\Teacher\DashboardController::class, 'submitDraft'])->name('grades.submitDraft');
    Route::post('/grades/discard-draft',[\App\Http\Controllers\Teacher\DashboardController::class, 'discardDraft'])->name('grades.discardDraft');

    // Attendance
    Route::post('/attendance/load', [\App\Http\Controllers\Teacher\DashboardController::class, 'loadAttendance'])->name('attendance.load');
    Route::post('/attendance/save', [\App\Http\Controllers\Teacher\DashboardController::class, 'saveAttendance'])->name('attendance.save');

    // Announcements
    Route::get('/announcements',              [\App\Http\Controllers\Teacher\DashboardController::class, 'getAnnouncements'])->name('announcements.index');
    Route::post('/announcements',             [\App\Http\Controllers\Teacher\DashboardController::class, 'storeAnnouncement'])->name('announcements.store');
    Route::delete('/announcements/{announcement}', [\App\Http\Controllers\Teacher\DashboardController::class, 'deleteAnnouncement'])->name('announcements.destroy');

    // Parent-Teacher Conference
    Route::get('/ptc',               [\App\Http\Controllers\Teacher\DashboardController::class, 'getPtcMeetings'])->name('ptc.index');
    Route::post('/ptc',              [\App\Http\Controllers\Teacher\DashboardController::class, 'storePtc'])->name('ptc.store');
    Route::put('/ptc/{ptc}/status',  [\App\Http\Controllers\Teacher\DashboardController::class, 'updatePtcStatus'])->name('ptc.updateStatus');

    // Reports
    Route::get('/reports/class-grades',        [\App\Http\Controllers\Teacher\DashboardController::class, 'getClassGradeReport'])->name('reports.classGrades');
    Route::get('/reports/student-report-card', [\App\Http\Controllers\Teacher\DashboardController::class, 'getStudentReportCard'])->name('reports.studentReportCard');

    // DepEd Forms
    Route::get('/sf9/{student}',  [\App\Http\Controllers\Teacher\DashboardController::class, 'printSF9'])->name('sf9');
    Route::get('/sf5',            [\App\Http\Controllers\Teacher\DashboardController::class, 'exportSF5'])->name('sf5');

    // Settings — password (OTP-protected)
    Route::post('/settings/password/send-otp', [\App\Http\Controllers\Teacher\DashboardController::class, 'sendPasswordOtp'])->name('settings.password.otp');
    Route::put('/settings/password', [\App\Http\Controllers\Teacher\DashboardController::class, 'updatePassword'])->name('settings.password');
    // Settings — profile photo
    Route::post('/settings/photo', [\App\Http\Controllers\Teacher\DashboardController::class, 'updatePhoto'])->name('settings.photo');
});

// ─────────────────────────────────────────
// STUDENT PORTAL
// ─────────────────────────────────────────
Route::middleware(['auth', 'student', 'maintenance'])->prefix('student')->name('student.')->group(function () {
    Route::get('/dashboard', [StudentPortalController::class, 'dashboard'])->name('dashboard');
    Route::get('/portal', [StudentPortalController::class, 'dashboard'])->name('portal');
    Route::get('/info', [StudentPortalController::class, 'showInfo'])->name('info');
    Route::post('/info', [StudentPortalController::class, 'updateInfo'])->name('info.update');
    Route::get('/documents', [StudentPortalController::class, 'showDocuments'])->name('documents');
    Route::post('/documents/upload', [StudentPortalController::class, 'uploadDocument'])->name('documents.upload');
    Route::post('/documents/upload-all', [StudentPortalController::class, 'uploadAllDocuments'])->name('documents.upload.all');
    Route::delete('/documents/{document}', [StudentPortalController::class, 'deleteDocument'])->name('documents.delete');
    Route::post('/payment/process', [StudentPortalController::class, 'processPayment'])->name('payment.process');
    Route::post('/change-section', [StudentPortalController::class, 'changeSection'])->name('section.change');
    Route::post('/reenroll', [StudentPortalController::class, 'submitReenrollment'])->name('reenroll');
    Route::post('/photo',            [StudentPortalController::class, 'uploadPhoto'])->name('photo.upload');
    // Xendit online payment link
    Route::post('/payment/xendit-link', [StudentPortalController::class, 'generateXenditLink'])->name('payment.xendit-link');
    // Settings — password (OTP-protected)
    Route::post('/settings/password/send-otp', [StudentPortalController::class, 'sendPasswordOtp'])->name('settings.password.otp');
    Route::put('/settings/password', [StudentPortalController::class, 'updatePassword'])->name('settings.password');
});

// Debug route for login redirect issue

// ─────────────────────────────────────────
// PROFILE COMPLETION (PROTECTED)
// ─────────────────────────────────────────
Route::middleware(['auth', 'verified'])->prefix('profile')->name('profile.')->group(function () {
    Route::get('/complete', [ProfileController::class, 'showCompleteProfile'])->name('complete');
    Route::post('/personal', [ProfileController::class, 'updatePersonal'])->name('personal.update');
    Route::post('/health', [ProfileController::class, 'updateHealth'])->name('health.update');
    Route::post('/address', [ProfileController::class, 'updateAddress'])->name('address.update');
    Route::post('/guardian', [ProfileController::class, 'updateGuardian'])->name('guardian.update');
    Route::post('/school', [ProfileController::class, 'updatePreviousSchool'])->name('school.update');
    Route::post('/enrollment', [ProfileController::class, 'updateEnrollment'])->name('enrollment.update');
    Route::get('/status', [ProfileController::class, 'checkCompletionStatus'])->name('status');
});