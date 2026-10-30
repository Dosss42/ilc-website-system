<?php

namespace App\Http\Controllers;

use App\Models\Enrollment;
use App\Models\GuidanceRecord;
use App\Models\Setting;
use App\Models\User;
use App\Services\ActivityLogger;
use App\Services\PaymentService;
use App\Support\DefaultSettings;
use Carbon\Carbon;
use Illuminate\Http\Request;

/**
 * Certificate of Good Moral Character (Student Management › student › Good Moral). Admin and Super Admin.
 *
 * DepEd prescribes no national template — it is a school-issued certification (school decisions,
 * October 30, 2026): the Guidance Counselor certifies, the School Head notes; the admin types the
 * purpose; an unpaid balance or guidance records are WARNED about before printing, never blocked.
 */
class GoodMoralController extends Controller
{
    private const GRADES = ['nursery' => 'Nursery', 'kindergarten' => 'Kindergarten', 'grade1' => 'Grade 1', 'grade2' => 'Grade 2',
                            'grade3' => 'Grade 3', 'grade4' => 'Grade 4', 'grade5' => 'Grade 5', 'grade6' => 'Grade 6'];

    /** What the admin should know before printing: balances still owed and guidance records. */
    public function check(User $user)
    {
        $enrollment = $this->latestEnrollment($user);
        if (!$enrollment) {
            return response()->json(['ok' => false, 'message' => 'This student has no enrollment at ILC yet — there is nothing to certify.'], 422);
        }

        $balances = Enrollment::where('user_id', $user->id)->whereIn('status', ['enrolled', 'approved', 'completed'])->get()
            ->map(fn ($e) => ['school_year' => $e->school_year, 'grade' => self::GRADES[$e->grade_level] ?? $e->grade_level,
                              'amount' => (float) (PaymentService::balanceDue($e) ?? 0)])
            ->filter(fn ($b) => $b['amount'] > 0)->values();
        $guidance = GuidanceRecord::where('student_id', $user->id)->orderByDesc('date')->get(['date', 'concern_type', 'status']);

        return response()->json([
            'ok'       => true,
            'student'  => $user->name,
            'covers'   => (self::GRADES[$enrollment->grade_level] ?? $enrollment->grade_level) . ', S.Y. ' . $enrollment->school_year,
            'balances' => $balances,
            'guidance' => $guidance->map(fn ($g) => ['date' => optional($g->date)->format('M j, Y') ?? (string) $g->date,
                                                     'concern' => $g->concern_type, 'status' => $g->status])->values(),
        ]);
    }

    public function pdf(Request $request, User $user)
    {
        $purpose = trim((string) $request->validate(['purpose' => 'nullable|string|max:120'])['purpose'] ?? '');
        $user->load(['profile', 'guardian']);
        $enrollment = $this->latestEnrollment($user);
        abort_unless($enrollment, 422, 'This student has no enrollment at ILC yet — there is nothing to certify.');

        $p = $user->profile;
        $name = $p
            ? trim(implode(' ', array_filter([$p->first_name, $p->middle_name ? mb_substr($p->middle_name, 0, 1) . '.' : null, $p->last_name])))
            : $user->name;
        $gender = strtolower((string) ($p->gender ?? ($enrollment->student_data['gender'] ?? '')));
        $current = in_array($enrollment->status, ['enrolled', 'approved'], true) && $enrollment->school_year === Setting::getCurrentSchoolYear();
        $today = Carbon::now();
        $day = (int) $today->format('j');

        $data = [
            'name'       => mb_strtoupper($name),
            'lrn'        => $user->lrn,
            'he'         => $gender === 'female' ? 'she' : ($gender === 'male' ? 'he' : 'he/she'),
            'his'        => $gender === 'female' ? 'her' : ($gender === 'male' ? 'his' : 'his/her'),
            'current'    => $current,
            'grade'      => self::GRADES[$enrollment->grade_level] ?? $enrollment->grade_level,
            'section'    => $current ? ($user->current_section?->name ?? $enrollment->section) : $enrollment->section,
            'schoolYear' => $enrollment->school_year,
            'requester'  => $user->guardian?->name,
            'purpose'    => $purpose !== '' ? $purpose : null,
            'issuedDay'  => $day . ($day % 100 >= 11 && $day % 100 <= 13 ? 'th' : ([1 => 'st', 2 => 'nd', 3 => 'rd'][$day % 10] ?? 'th')),
            'issuedMonthYear' => $today->format('F Y'),
            'schoolName' => Setting::get('school_name') ?: 'IEMELIF Learning Center',
            'schoolAddress' => Setting::get('school_address') ?: 'General Tinio, Nueva Ecija',
            'schoolId'   => Setting::get('school_id') ?: '',
            'district'   => Setting::get('school_district') ?: '',
            'counselor'  => Setting::get('guidance_counselor_name') ?: (DefaultSettings::find('guidance_counselor_name')['value'] ?? ''),
            'schoolHead' => Setting::get('principal_name') ?: '',
        ];

        ActivityLogger::log('create', "Printed Certificate of Good Moral Character for {$user->name}"
            . ($purpose !== '' ? " (purpose: {$purpose})" : ''), 'Student');

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('pdf.good-moral', $data)->setPaper('letter', 'portrait');
        $filename = 'Good_Moral_' . preg_replace('/[^A-Za-z0-9_\-]/', '_', $user->name) . '_' . date('Ymd') . '.pdf';
        return $request->boolean('print') ? $pdf->stream($filename) : $pdf->download($filename);
    }

    /** The school year the certificate speaks for: the latest one the student actually attended. */
    private function latestEnrollment(User $user): ?Enrollment
    {
        return Enrollment::where('user_id', $user->id)->whereIn('status', ['enrolled', 'approved', 'completed'])
            ->orderByDesc('school_year')->orderByDesc('id')->first();
    }
}
