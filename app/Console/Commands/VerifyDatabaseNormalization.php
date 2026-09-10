<?php

namespace App\Console\Commands;

use App\Models\Section;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Schema;

/**
 * Reusable verification harness for DATABASE_NORMALIZATION_PLAN.md.
 *
 * Each phase of that plan adds one check method here and registers it in
 * $checks below. A check method returns an array of human-readable issue
 * strings (empty array = clean). This command never modifies data — it
 * only reports drift between a denormalized/duplicated value and the
 * value it's supposed to agree with, so it's safe to run at any time,
 * including against production, and re-run after every phase's backfill
 * step to confirm before moving on to that phase's "contract" step.
 */
class VerifyDatabaseNormalization extends Command
{
    protected $signature = 'db:verify-normalization {--check=* : Run only these check names instead of all}';
    protected $description = 'Verify data consistency for each phase of the database normalization plan';

    public function handle(): int
    {
        $checks = [
            'sections.current_enrollment' => fn () => $this->checkSectionEnrollmentDrift(),
            'fee_settings vs fee_components' => fn () => $this->checkFeeComponentsDrift(),
            'fee calculators agree' => fn () => $this->checkFeeCalculatorsAgree(),
            'schedules vs teacher_assignments' => fn () => $this->checkScheduleAssignmentCoverage(),
            'advisory-only grades vs teacher_assignments' => fn () => $this->checkAdvisoryOnlyGradeCoverage(),
            'enrollments.section vs section_student' => fn () => $this->checkEnrollmentSectionDrift(),
            'user_id/student_id vs enrollments.user_id' => fn () => $this->checkTransitiveUserIdDrift(),
            'student_data JSON vs normalized profile tables' => fn () => $this->checkStudentDataDrift(),
            'payment_transactions.installment_month vs installment_id' => fn () => $this->checkInstallmentMonthDrift(),
            'sections.teacher_id vs teacher_assignments' => fn () => $this->checkSectionTeacherIdDrift(),
            'promotions.lrn vs users.lrn' => fn () => $this->checkPromotionLrnDrift(),
            // ...and so on, one entry per phase, never removed once added
            // so this command keeps growing into a full regression check.
        ];

        $only = $this->option('check');
        if (!empty($only)) {
            $checks = array_intersect_key($checks, array_flip($only));
        }

        if (empty($checks)) {
            $this->info('No checks registered yet — this command is scaffolding for the normalization plan.');
            $this->line('Phase 1 onward will add checks here as each fix is built.');
            return self::SUCCESS;
        }

        $totalIssues = 0;

        foreach ($checks as $name => $callback) {
            $this->line("Checking: {$name}");
            $issues = $callback();

            if (empty($issues)) {
                $this->info("  ✓ clean");
                continue;
            }

            $totalIssues += count($issues);
            $this->warn('  ✗ ' . count($issues) . ' issue(s) found:');
            foreach ($issues as $issue) {
                $this->line('    - ' . $issue);
            }
        }

        $this->newLine();
        if ($totalIssues === 0) {
            $this->info('All registered checks passed.');
            return self::SUCCESS;
        }

        $this->error("{$totalIssues} total issue(s) found across " . count($checks) . ' check(s).');
        return self::FAILURE;
    }

    /**
     * Phase 1 check: sections.current_enrollment (stored) vs. a live
     * section_student count for every section. Once the Contract step drops
     * the column entirely, this check has nothing left to compare and passes
     * trivially rather than reporting every section as "drifted" against a
     * column that no longer exists — kept registered (not removed) as a
     * permanent guard: if the column is ever reintroduced by mistake, this
     * starts actually checking it again automatically.
     */
    private function checkSectionEnrollmentDrift(): array
    {
        if (!Schema::hasColumn('sections', 'current_enrollment')) {
            return [];
        }

        $issues = [];

        Section::withCount('students')->get()->each(function (Section $section) use (&$issues) {
            $stored = (int) $section->getRawOriginal('current_enrollment');
            $live   = (int) $section->students_count;

            if ($stored !== $live) {
                $issues[] = "Section #{$section->id} ({$section->name}): stored current_enrollment={$stored}, live count={$live}";
            }
        });

        return $issues;
    }

    /**
     * Phase 2 check #1: fee_settings (legacy, still admin-editable) vs.
     * fee_components (new normalized table) — confirms the dual-write in
     * Finance\DashboardController::updateFees() is keeping both in sync.
     * Same option/grade-group mapping the backfill migration and the
     * dual-write both use.
     */
    private function checkFeeComponentsDrift(): array
    {
        if (!Schema::hasTable('fee_components')) {
            return ['fee_components table does not exist yet — run the Phase 2 migration.'];
        }

        $fee = \App\Models\FeeSetting::first();
        if (!$fee) {
            return [];
        }

        $expected = [
            [null, null, 'tuition', $fee->tuition],
            [null, null, 'misc', $fee->misc],
            [null, null, 'insurance', $fee->insurance],
            [null, null, 'electric', $fee->electric],
            [null, 'nursery', 'books', $fee->books_nursery],
            [null, 'kindergarten', 'books', $fee->books_nursery],
            [null, 'grade1', 'books', $fee->books_grade1],
            [null, 'grade2', 'books', $fee->books_grade1],
            [null, 'grade3', 'books', $fee->books_grade3],
            [null, 'grade4', 'books', $fee->books_grade4],
            [null, 'grade5', 'books', $fee->books_grade4],
            [null, 'grade6', 'books', $fee->books_grade4],
            ['A', null, 'discount', $fee->option_a_discount],
            ['B', 'nursery', 'downpayment', $fee->optb_dp_nursery],
            ['B', 'kindergarten', 'downpayment', $fee->optb_dp_kinder],
            ['B', 'grade1', 'downpayment', $fee->optb_dp_grade1],
            ['B', 'grade2', 'downpayment', $fee->optb_dp_grade1],
            ['B', 'grade3', 'downpayment', $fee->optb_dp_grade3],
            ['B', 'grade4', 'downpayment', $fee->optb_dp_grade4],
            ['B', 'grade5', 'downpayment', $fee->optb_dp_grade4],
            ['B', 'grade6', 'downpayment', $fee->optb_dp_grade4],
            ['B', null, 'monthly_tuition', $fee->optb_monthly_tuition],
            ['B', null, 'monthly_electric', $fee->optb_monthly_electric],
            ['C', 'grade1', 'downpayment', $fee->optc_dp_grade1],
            ['C', 'grade2', 'downpayment', $fee->optc_dp_grade1],
            ['C', 'grade3', 'downpayment', $fee->optc_dp_grade3],
            ['C', 'grade4', 'downpayment', $fee->optc_dp_grade4],
            ['C', 'grade5', 'downpayment', $fee->optc_dp_grade4],
            ['C', 'grade6', 'downpayment', $fee->optc_dp_grade4],
            ['C', null, 'monthly_tuition', $fee->optc_monthly_tuition],
            ['C', null, 'monthly_misc', $fee->optc_monthly_misc],
            ['C', null, 'monthly_electric', $fee->optc_monthly_electric],
            ['D', 'nursery', 'downpayment', $fee->optd_dp_nursery],
            ['D', 'kindergarten', 'downpayment', $fee->optd_dp_kinder],
            ['D', null, 'monthly_tuition', $fee->optd_monthly_tuition],
            ['D', null, 'monthly_misc', $fee->optd_monthly_misc],
            ['D', null, 'monthly_electric', $fee->optd_monthly_electric],
        ];

        $issues = [];
        foreach ($expected as [$option, $gradeLevel, $feeType, $amount]) {
            $stored = \Illuminate\Support\Facades\DB::table('fee_components')
                ->where('option', $option)
                ->where('grade_level', $gradeLevel)
                ->where('fee_type', $feeType)
                ->value('amount');

            if ($stored === null || abs((float) $stored - (float) $amount) > 0.001) {
                $label = ($option ?? 'ALL') . '/' . ($gradeLevel ?? 'ALL') . '/' . $feeType;
                $issues[] = "fee_components {$label}: fee_settings says {$amount}, fee_components has " . ($stored ?? 'MISSING');
            }
        }

        return $issues;
    }

    /**
     * Phase 2 check #2: EnrollmentController::calculatePaymentBreakdown()
     * (the real charging path) vs. FeeController's quote endpoint — the
     * actual bug this phase fixed (they used to disagree, e.g. a live ₱1
     * mismatch on the Option A discount, since EnrollmentController used to
     * hardcode its own numbers instead of reading fee_settings at all).
     * Both are now backed by the same FeeCalculator, so this should always
     * be clean — kept as a permanent guard against a future regression
     * reintroducing a second, independent calculator.
     */
    private function checkFeeCalculatorsAgree(): array
    {
        $issues = [];
        $enrollmentController = new \App\Http\Controllers\EnrollmentController();
        $feeController = new \App\Http\Controllers\FeeController();
        $reflection = new \ReflectionMethod($feeController, 'calculateFeeBreakdown');

        $grades = ['nursery', 'kindergarten', 'grade1', 'grade2', 'grade3', 'grade4', 'grade5', 'grade6'];
        foreach ($grades as $grade) {
            foreach (['A', 'B', 'C', 'D'] as $option) {
                $enrollmentTotal = $enrollmentController->calculatePaymentBreakdown($grade, $option)['total_due'] ?? null;
                $feeTotal = $reflection->invoke($feeController, $grade, $option)['total_payable'] ?? null;

                if ($enrollmentTotal !== null && $feeTotal !== null && abs($enrollmentTotal - $feeTotal) > 0.01) {
                    $issues[] = "{$grade}/{$option}: EnrollmentController charges {$enrollmentTotal}, FeeController quotes {$feeTotal}";
                }
            }
        }

        return $issues;
    }

    /**
     * Phase 3 check: every real (teacher, subject, section) combination
     * found in schedules has a matching non-advisory teacher_assignments
     * row. teacher_assignments started with zero subject-teaching rows —
     * only advisory ones — so this both confirms the one-time backfill
     * migration worked and catches future drift if a new Schedule row is
     * ever created without a corresponding assignment being kept in sync.
     */
    private function checkScheduleAssignmentCoverage(): array
    {
        $issues = [];

        $combos = \Illuminate\Support\Facades\DB::table('schedules')
            ->join('sections', 'sections.id', '=', 'schedules.section_id')
            ->whereNotNull('schedules.teacher_id')
            ->whereNotNull('schedules.subject_id')
            ->select('schedules.teacher_id', 'schedules.subject_id', 'schedules.section_id', 'sections.school_year')
            ->distinct()
            ->get();

        foreach ($combos as $combo) {
            $exists = \App\Models\TeacherAssignment::where('teacher_id', $combo->teacher_id)
                ->where('subject_id', $combo->subject_id)
                ->where('section_id', $combo->section_id)
                ->where('school_year', $combo->school_year)
                ->where('is_advisory', false)
                ->exists();

            if (!$exists) {
                $issues[] = "teacher={$combo->teacher_id} subject={$combo->subject_id} section={$combo->section_id} school_year={$combo->school_year}: in schedules but missing from teacher_assignments";
            }
        }

        return $issues;
    }

    /**
     * Phase 3 check #2: Nursery/Kindergarten-style sections (single advisory
     * teacher covers every subject, no real schedule rows to derive from —
     * confirmed live: Kindergarten had zero schedule rows and was entirely
     * missed by the schedules-based backfill) have a non-advisory
     * teacher_assignments row for every active subject in that grade level.
     * Mirrors Grade::NURSERY_KINDER_LEVELS and Teacher\DashboardController's
     * runtime "advisory teacher handles ALL subjects" rule.
     */
    private function checkAdvisoryOnlyGradeCoverage(): array
    {
        $issues = [];
        $gradeLevels = \App\Models\Grade::NURSERY_KINDER_LEVELS;

        $advisoryRows = \Illuminate\Support\Facades\DB::table('teacher_assignments')
            ->join('sections', 'sections.id', '=', 'teacher_assignments.section_id')
            ->where('teacher_assignments.is_advisory', true)
            ->whereIn('sections.grade_level', $gradeLevels)
            ->select('teacher_assignments.teacher_id', 'teacher_assignments.section_id', 'teacher_assignments.school_year', 'sections.grade_level', 'sections.name')
            ->get();

        foreach ($advisoryRows as $row) {
            $expectedSubjectIds = \App\Models\Subject::where('grade_level', $row->grade_level)
                ->where('is_active', true)
                ->pluck('id');

            $coveredCount = \App\Models\TeacherAssignment::where('teacher_id', $row->teacher_id)
                ->where('section_id', $row->section_id)
                ->where('school_year', $row->school_year)
                ->where('is_advisory', false)
                ->whereIn('subject_id', $expectedSubjectIds)
                ->count();

            if ($coveredCount < $expectedSubjectIds->count()) {
                $issues[] = "Section {$row->name} (advisory teacher {$row->teacher_id}, {$row->school_year}): covers {$coveredCount} of {$expectedSubjectIds->count()} {$row->grade_level} subjects in teacher_assignments";
            }
        }

        return $issues;
    }

    /**
     * Phase 4 check: for every currently-enrolled student, does the
     * denormalized enrollments.section string still agree with their real
     * section_student membership? section_student is trusted for reads now
     * (see User::getCurrentSectionAttribute()), so this only flags drift for
     * awareness — it doesn't block anything, same spirit as Phase 1's
     * current_enrollment check. Only checks the student's CURRENT enrollment;
     * historical enrollments legitimately keep their own point-in-time
     * section string, which section_student was never meant to reconstruct.
     */
    private function checkEnrollmentSectionDrift(): array
    {
        $issues = [];

        \App\Models\Enrollment::where('status', 'enrolled')->get()->each(function ($enrollment) use (&$issues) {
            $user = \App\Models\User::find($enrollment->user_id);
            if (!$user || !$user->latestEnrollment || $user->latestEnrollment->id !== $enrollment->id) {
                return; // not this student's current enrollment — historical, skip
            }

            $realSection = $user->current_section?->name;
            $stored = $enrollment->section;

            if ($stored !== $realSection) {
                $issues[] = "Enrollment #{$enrollment->id} (user {$enrollment->user_id}): stored section=\"" . ($stored ?? 'NULL') . "\", real section_student membership=\"" . ($realSection ?? 'NULL') . '"';
            }
        });

        return $issues;
    }

    /**
     * Phase 5 check: payment_installments.user_id, payment_transactions.user_id,
     * promissory_notes.student_id, and grades.student_id (where enrollment_id
     * is set) should always match their linked enrollment's real user_id.
     * These are a deliberate, accepted transitive dependency (Option B —
     * kept for cheap reads instead of forcing a join through enrollments
     * everywhere) rather than a bug to fix — see the docblocks on
     * PaymentInstallment, PaymentTransaction, PromissoryNote, and Grade.
     * This check exists purely to catch the risk that trade-off accepts:
     * silent drift between the two, which nothing in the schema itself
     * (e.g. a DB-level CHECK constraint) prevents.
     */
    private function checkTransitiveUserIdDrift(): array
    {
        $issues = [];

        $tables = [
            ['table' => 'payment_installments', 'column' => 'user_id'],
            ['table' => 'payment_transactions', 'column' => 'user_id'],
            ['table' => 'promissory_notes', 'column' => 'student_id'],
            ['table' => 'grades', 'column' => 'student_id'],
        ];

        foreach ($tables as $t) {
            $mismatches = \Illuminate\Support\Facades\DB::table($t['table'])
                ->join('enrollments', 'enrollments.id', '=', $t['table'] . '.enrollment_id')
                ->whereColumn($t['table'] . '.' . $t['column'], '!=', 'enrollments.user_id')
                ->select($t['table'] . '.id', $t['table'] . '.' . $t['column'] . ' as stored_user_id', 'enrollments.user_id as real_user_id', 'enrollments.id as enrollment_id')
                ->get();

            foreach ($mismatches as $row) {
                $issues[] = "{$t['table']}#{$row->id}: {$t['column']}={$row->stored_user_id}, but enrollment #{$row->enrollment_id} belongs to user {$row->real_user_id}";
            }
        }

        return $issues;
    }

    /**
     * Phase 6 check: does every enrollment's student_data JSON have a
     * corresponding, non-empty value in the normalized tables? This isn't
     * checking for exact equality — after a real profile edit (via
     * ProfileController, which writes only to the normalized tables) the
     * normalized side is EXPECTED to differ from and be more current than
     * the original JSON snapshot, and that's fine, even desired. What this
     * actually flags is the real gap: a student who submitted a field at
     * enrollment time but whose normalized row is still empty for it —
     * meaning the enrollment-time dual-write missed it (exactly how the
     * StudentAddress 'street'/'city' field-name bug was found and fixed).
     */
    private function checkStudentDataDrift(): array
    {
        $issues = [];

        \App\Models\Enrollment::whereNotNull('student_data')->get()->each(function ($enrollment) use (&$issues) {
            $d = $enrollment->student_data ?? [];
            $user = \App\Models\User::find($enrollment->user_id);
            if (!$user) {
                return;
            }

            $profile = $user->profile;
            $address = $user->address;
            $guardian = $user->guardian;

            $fieldChecks = [
                ['json' => 'first_name', 'normalized' => $profile?->first_name, 'table' => 'student_profiles'],
                ['json' => 'last_name', 'normalized' => $profile?->last_name, 'table' => 'student_profiles'],
                ['json' => 'birthdate', 'normalized' => $profile?->birthdate, 'table' => 'student_profiles'],
                ['json' => 'province', 'normalized' => $address?->province, 'table' => 'student_addresses'],
                ['json' => 'city', 'normalized' => $address?->city, 'table' => 'student_addresses'],
                ['json' => 'barangay', 'normalized' => $address?->barangay, 'table' => 'student_addresses'],
                ['json' => 'street_address', 'normalized' => $address?->street_address, 'table' => 'student_addresses'],
                ['json' => 'guardian_name', 'normalized' => $guardian?->name, 'table' => 'guardians'],
                ['json' => 'guardian_phone', 'normalized' => $guardian?->contact, 'table' => 'guardians'],
            ];

            foreach ($fieldChecks as $check) {
                $jsonValue = $d[$check['json']] ?? null;
                if (!empty($jsonValue) && empty($check['normalized'])) {
                    $issues[] = "Enrollment #{$enrollment->id} (user {$enrollment->user_id}): {$check['table']} missing '{$check['json']}' (JSON has \"{$jsonValue}\")";
                }
            }
        });

        return $issues;
    }

    /**
     * Phase 7 check: payment_transactions.installment_month vs the
     * installment_id relation. installment_month is NOT pure redundancy —
     * a true downpayment payment has no payment_installments row at all, so
     * it legitimately stores the literal string 'Downpayment' directly (see
     * the walk-in and admin-payment flows in Finance\DashboardController).
     * What this guards against is the real bug this phase found and fixed:
     * processAdminPayment() used to set installment_month from a resolved
     * installment without ever saving that installment's id onto the
     * transaction, silently dropping the FK link. So this flags two things:
     * (1) a transaction whose installment_month names a real month (not the
     * 'Downpayment' sentinel) but has no installment_id at all — the FK-drop
     * bug reappearing in some other code path — and (2) a transaction whose
     * installment_id IS set but whose installment_month text has drifted
     * from that installment's actual month_name.
     */
    private function checkInstallmentMonthDrift(): array
    {
        $issues = [];

        \App\Models\PaymentTransaction::whereNotNull('installment_month')
            ->where('installment_month', '!=', '')
            ->get()
            ->each(function ($txn) use (&$issues) {
                if ($txn->installment_id) {
                    $installment = \App\Models\PaymentInstallment::find($txn->installment_id);
                    if ($installment && $txn->installment_month !== $installment->month_name) {
                        $issues[] = "PaymentTransaction #{$txn->id}: installment_month=\"{$txn->installment_month}\" but linked installment #{$installment->id} month_name=\"{$installment->month_name}\"";
                    }
                    return;
                }

                if ($txn->installment_month !== 'Downpayment') {
                    $issues[] = "PaymentTransaction #{$txn->id}: installment_month=\"{$txn->installment_month}\" but installment_id is NULL — FK link was dropped when this row was written";
                }
            });

        return $issues;
    }

    /**
     * Found during a general 2NF/3NF re-audit (not part of the original
     * phase plan): sections.teacher_id duplicates the advisory teacher
     * already derivable from teacher_assignments (is_advisory = true), and
     * TeacherAssignmentController re-syncs it on every advisory add/update/
     * remove — but until now nothing checked that sync actually stayed
     * correct. Mirrors that controller's own "first advisory by id, scoped
     * to this section's school_year" resolution exactly, so a clean result
     * here means the cache is trustworthy for any raw-SQL reader.
     */
    private function checkSectionTeacherIdDrift(): array
    {
        $issues = [];

        Section::all(['id', 'name', 'school_year', 'teacher_id'])->each(function (Section $section) use (&$issues) {
            $firstAdvisory = \App\Models\TeacherAssignment::where('section_id', $section->id)
                ->where('school_year', $section->school_year)
                ->where('is_advisory', true)
                ->orderBy('id')
                ->first();

            $expectedTeacherId = $firstAdvisory?->teacher_id;

            if ($section->teacher_id !== $expectedTeacherId) {
                $issues[] = "Section #{$section->id} ({$section->name}): stored teacher_id=" . ($section->teacher_id ?? 'NULL') . ', expected (from teacher_assignments)=' . ($expectedTeacherId ?? 'NULL');
            }
        });

        return $issues;
    }

    /**
     * Found during the same re-audit: promotions.lrn is copied from
     * users.lrn at the moment a promotion record is created — a textbook
     * transitive dependency. Real-world drift risk is low (a DepEd LRN is
     * assigned once and essentially never changes) and a mismatch here
     * could legitimately mean the user's LRN was corrected after the
     * promotion happened, not necessarily a bug — but it's worth surfacing
     * either way since, unlike the app's other deliberate denormalizations,
     * this one was previously undocumented and unchecked.
     */
    private function checkPromotionLrnDrift(): array
    {
        $issues = [];

        \App\Models\Promotion::whereNotNull('lrn')->get(['id', 'student_id', 'lrn'])->each(function ($promotion) use (&$issues) {
            $user = \App\Models\User::find($promotion->student_id);
            if (!$user) {
                return;
            }

            if ($promotion->lrn !== $user->lrn) {
                $issues[] = "Promotion #{$promotion->id} (student {$promotion->student_id}): stored lrn=\"{$promotion->lrn}\", user's current lrn=\"" . ($user->lrn ?? 'NULL') . '" (may be a legitimate later correction, not necessarily a bug)';
            }
        });

        return $issues;
    }
}
