<?php

namespace App\Http\Controllers;

use App\Models\Grade;
use App\Models\Enrollment;
use App\Models\User;
use App\Models\Section;
use App\Models\Subject;
use App\Models\GuidanceRecord;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ReportController extends Controller
{
    /**
     * Get academic reports - student grades
     */
    public function academicReport(Request $request)
    {
        $query = Grade::with(['student:id,name', 'subject:id,name,code', 'teacher:id,name']);

        // Apply filters
        if ($request->filled('student_id')) {
            $query->where('student_id', $request->student_id);
        }

        if ($request->filled('section_id')) {
            $query->whereHas('student.profile', function($q) use ($request) {
                $q->where('section_id', $request->section_id);
            });
        }

        if ($request->filled('subject_id')) {
            $query->where('subject_id', $request->subject_id);
        }

        if ($request->filled('grade_level')) {
            $query->whereHas('subject', function($q) use ($request) {
                $q->where('grade_level', $request->grade_level);
            });
        }

        if ($request->filled('quarter')) {
            $query->where('quarter', $request->quarter);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $grades = $query->orderBy('created_at', 'desc')->get();

        // Calculate statistics
        $stats = [
            'total_grades' => $grades->count(),
            'average_grade' => $grades->avg('final_grade'),
            'passed' => $grades->where('final_grade', '>=', 75)->count(),
            'failed' => $grades->where('final_grade', '<', 75)->count(),
            'by_quarter' => $grades->groupBy('quarter')->map(function($g) {
                return [
                    'count' => $g->count(),
                    'average' => $g->avg('final_grade')
                ];
            })
        ];

        return response()->json([
            'success' => true,
            'grades' => $grades,
            'statistics' => $stats
        ]);
    }

    /**
     * Get financial reports - payments and revenue
     */
    public function financialReport(Request $request)
    {
        $query = Enrollment::whereNotNull('payment_status');

        if ($request->filled('date_from')) {
            $query->whereDate('updated_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('updated_at', '<=', $request->date_to);
        }

        if ($request->filled('grade_level')) {
            $query->where('grade_level', $request->grade_level);
        }

        if ($request->filled('payment_status')) {
            $query->where('payment_status', $request->payment_status);
        }

        $enrollments = $query->get();

        // Calculate financial statistics
        $stats = [
            'total_collected' => $enrollments->where('payment_status', 'paid')->sum('payment_amount'),
            'total_pending' => $enrollments->where('payment_status', 'pending')->count(),
            'total_partial' => $enrollments->where('payment_status', 'partial')->count(),
            'total_paid' => $enrollments->where('payment_status', 'paid')->count(),
            'total_revenue' => $enrollments->sum('total_fee'),
            'outstanding_balance' => $enrollments->sum('remaining_balance'),
            'by_payment_option' => $enrollments->groupBy('payment_option')->map(function($e) {
                return [
                    'count' => $e->count(),
                    'revenue' => $e->sum('payment_amount')
                ];
            })
        ];

        return response()->json([
            'success' => true,
            'enrollments' => $enrollments,
            'statistics' => $stats
        ]);
    }

    /**
     * Get enrollment reports
     */
    public function enrollmentReport(Request $request)
    {
        $query = Enrollment::with('student:id,name');

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        if ($request->filled('grade_level')) {
            $query->where('grade_level', $request->grade_level);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('student_type')) {
            $query->where('student_type', $request->student_type);
        }

        $enrollments = $query->orderBy('created_at', 'desc')->get();

        // Calculate statistics
        $stats = [
            'total' => $enrollments->count(),
            'new_students' => $enrollments->where('student_type', 'new')->count(),
            'returning' => $enrollments->where('student_type', 'returning')->count(),
            'transferees' => $enrollments->where('student_type', 'transferee')->count(),
            'approved' => $enrollments->where('status', 'approved')->count(),
            'pending' => $enrollments->where('status', 'pending')->count(),
            'by_grade' => $enrollments->groupBy('grade_level')->map->count(),
            'by_month' => $enrollments->groupBy(function($e) {
                return $e->created_at->format('Y-m');
            })->map->count()
        ];

        return response()->json([
            'success' => true,
            'enrollments' => $enrollments,
            'statistics' => $stats
        ]);
    }

    /**
     * Get guidance/counseling reports
     */
    public function guidanceReport(Request $request)
    {
        $query = GuidanceRecord::with(['student:id,name', 'counselor:id,name']);

        if ($request->filled('date_from')) {
            $query->whereDate('date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('date', '<=', $request->date_to);
        }

        if ($request->filled('concern_type')) {
            $query->where('concern_type', $request->concern_type);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('student_id')) {
            $query->where('student_id', $request->student_id);
        }

        $records = $query->orderBy('date', 'desc')->get();

        // Calculate statistics
        $stats = [
            'total' => $records->count(),
            'open' => $records->where('status', 'open')->count(),
            'in_progress' => $records->where('status', 'in_progress')->count(),
            'resolved' => $records->where('status', 'resolved')->count(),
            'closed' => $records->where('status', 'closed')->count(),
            'by_type' => $records->groupBy('concern_type')->map->count(),
            'by_month' => $records->groupBy(function($r) {
                return $r->date->format('Y-m');
            })->map->count()
        ];

        return response()->json([
            'success' => true,
            'records' => $records,
            'statistics' => $stats
        ]);
    }

    /**
     * Get dashboard summary statistics
     */
    public function dashboardSummary()
    {
        $today = Carbon::today();
        $thisMonth = Carbon::now()->startOfMonth();
        $thisYear = Carbon::now()->startOfYear();

        return response()->json([
            'success' => true,
            'data' => [
                'students' => [
                    'total' => User::where('role', 'student')->count(),
                    'new_today' => User::where('role', 'student')->whereDate('created_at', $today)->count(),
                    'new_this_month' => User::where('role', 'student')->whereMonth('created_at', $thisMonth->month)->count(),
                ],
                'enrollments' => [
                    'total' => Enrollment::count(),
                    'pending' => Enrollment::where('status', 'pending')->count(),
                    'approved_this_month' => Enrollment::where('status', 'approved')->whereMonth('created_at', $thisMonth->month)->count(),
                ],
                'finance' => [
                    'collected_today' => Enrollment::where('payment_status', 'paid')->whereDate('updated_at', $today)->sum('payment_amount'),
                    'collected_this_month' => Enrollment::where('payment_status', 'paid')->whereMonth('updated_at', $thisMonth->month)->sum('payment_amount'),
                    'outstanding_balance' => Enrollment::sum('remaining_balance'),
                ],
                'grades' => [
                    'total_entries' => Grade::count(),
                    'this_month' => Grade::whereMonth('created_at', $thisMonth->month)->count(),
                    'average_grade' => Grade::avg('final_grade'),
                ],
                'guidance' => [
                    'open_cases' => GuidanceRecord::where('status', 'open')->count(),
                    'resolved_this_month' => GuidanceRecord::where('status', 'resolved')->whereMonth('updated_at', $thisMonth->month)->count(),
                ]
            ]
        ]);
    }
}
