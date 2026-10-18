<?php

namespace App\Http\Controllers;

use App\Models\Enrollment;
use App\Models\PromissoryNote;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class PromissoryNoteController extends Controller
{
    use \App\Support\Traits\BuildsSchoolPdf;

    /**
     * Id of whichever staff account actually created/is viewing this note.
     *
     * Every route on this controller sits behind FinanceMiddleware, which
     * authenticates via Auth::guard('finance') specifically — Auth::id()
     * (no guard = the default 'web' guard) has nothing to do with that
     * login at all. It was silently recording whatever 'web'-guard session
     * happened to also be active in the same browser (e.g. an Admin tab),
     * not the finance account that actually submitted the form — same bug
     * class already fixed in Finance\DashboardController::actingStaffId().
     */
    private function actingStaffId(): ?int
    {
        return Auth::guard('finance')->id() ?? Auth::id();
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'enrollment_id'   => 'required|exists:enrollments,id',
            'amount_overdue'  => 'required|numeric|min:0',
            'amount_promised' => 'required|numeric|min:0.01',
            'promise_date'    => 'required|date|after:today',
            'parent_guardian' => 'nullable|string|max:255',
            'remarks'         => 'nullable|string|max:1000',
        ]);

        $enrollment = Enrollment::findOrFail($validated['enrollment_id']);

        // generateReference() reads-then-increments without a lock, so two
        // staff submitting at the same instant could compute the same
        // reference_number; the column's unique constraint stops the dupe
        // from being saved, but without a retry that just surfaces as an
        // uncaught 500 instead of the second submitter simply getting the
        // next number.
        $attempts = 0;
        do {
            try {
                $note = PromissoryNote::create([
                    'reference_number' => PromissoryNote::generateReference(),
                    'enrollment_id'    => $enrollment->id,
                    'student_id'       => $enrollment->user_id,
                    'created_by'       => $this->actingStaffId(),
                    'amount_overdue'   => $validated['amount_overdue'],
                    'amount_promised'  => $validated['amount_promised'],
                    'promise_date'     => $validated['promise_date'],
                    'date_issued'      => Carbon::today(),
                    'parent_guardian'  => $validated['parent_guardian'],
                    'remarks'          => $validated['remarks'],
                    'status'           => 'pending',
                ]);
                break;
            } catch (\Illuminate\Database\QueryException $e) {
                $attempts++;
                if ($attempts >= 3 || !str_contains($e->getMessage(), 'reference_number')) {
                    throw $e;
                }
            }
        } while (true);

        if ($request->expectsJson()) {
            return response()->json([
                'success'   => true,
                'message'   => 'Promissory note created successfully.',
                'reference' => $note->reference_number,
                'id'        => $note->id,
            ]);
        }

        return back()->with('success', 'Promissory note ' . $note->reference_number . ' created.');
    }

    public function updateStatus(Request $request, PromissoryNote $note)
    {
        $request->validate([
            'status'        => 'required|in:pending,fulfilled,broken,extended',
            'extended_date' => 'required_if:status,extended|nullable|date|after:today',
            'remarks'       => 'nullable|string|max:1000',
        ]);

        // 'fulfilled' is fully terminal. 'broken' can only still move to
        // 'fulfilled' (a parent settling the balance after the promise date
        // already passed). Blocking same-status resubmission too — not just
        // a *different* status — matters because the old check let a
        // resubmitted "fulfilled" fall through and silently re-stamp
        // fulfilled_at to the new request time, with no record it happened.
        if (
            $note->status === 'fulfilled'
            || ($note->status === 'broken' && $request->status !== 'fulfilled')
        ) {
            $msg = 'This promissory note is already marked "' . ucfirst($note->status) . '" and cannot be changed further.';
            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'message' => $msg], 422);
            }
            return back()->with('error', $msg);
        }

        $data = ['status' => $request->status];

        if ($request->status === 'fulfilled') {
            $data['fulfilled_at'] = now();
        }
        if ($request->status === 'extended' && $request->extended_date) {
            $data['extended_date'] = $request->extended_date;
            $data['promise_date']  = $request->extended_date;
        }
        if ($request->remarks) {
            $data['remarks'] = $request->remarks;
        }

        $note->update($data);

        if ($request->expectsJson()) {
            return response()->json(['success' => true, 'status' => $note->status]);
        }

        return back()->with('success', 'Promissory note status updated to ' . ucfirst($request->status) . '.');
    }

    public function getNotes(Request $request)
    {
        $enrollmentId = $request->query('enrollment_id');

        $notes = PromissoryNote::with('createdBy:id,name')
            ->when($enrollmentId, fn($q) => $q->where('enrollment_id', $enrollmentId))
            ->orderByDesc('created_at')
            ->get()
            ->map(fn($n) => [
                'id'               => $n->id,
                'reference_number' => $n->reference_number,
                'amount_overdue'   => $n->amount_overdue,
                'amount_promised'  => $n->amount_promised,
                'promise_date'     => $n->promise_date->format('M d, Y'),
                'date_issued'      => $n->date_issued->format('M d, Y'),
                'parent_guardian'  => $n->parent_guardian,
                'remarks'          => $n->remarks,
                'status'           => $n->status,
                'is_overdue'       => $n->is_overdue,
                'created_by_name'  => $n->createdBy->name ?? 'Unknown',
                'fulfilled_at'     => $n->fulfilled_at?->format('M d, Y'),
                'extended_date'    => $n->extended_date?->format('M d, Y'),
            ]);

        return response()->json(['success' => true, 'data' => $notes]);
    }

    /**
     * Real DomPDF-rendered promissory note, on the same shared letterhead/
     * running-header-footer design as every other Finance (and Cashier)
     * report — this used to be a hand-styled HTML page relying on
     * display:grid (which Dompdf doesn't support at all; it was only ever
     * viewed in-browser via window.print(), never actually run through
     * Dompdf). ->stream() (inline), not ->download(), since the "Print"
     * button it's linked from opens this in a new tab for the browser's
     * own PDF viewer to print from — matches the previous behavior more
     * closely than forcing a file save.
     */
    public function printNote(PromissoryNote $note)
    {
        $note->load(['student:id,name', 'enrollment', 'createdBy:id,name,email']);

        $data = array_merge($this->schoolPdfLetterhead($note->createdBy->name ?? 'Finance Staff'), [
            'reportTitle'    => 'Promissory Note',
            'dateRangeLabel' => 'Reference No.: ' . $note->reference_number,
            'note'           => $note,
        ]);

        return $this->schoolPdfOptions(
            \Barryvdh\DomPDF\Facade\Pdf::loadView('pdf.finance.promissory-note', $data),
            $data
        )->stream($note->reference_number . '.pdf');
    }

    public function destroy(PromissoryNote $note)
    {
        \App\Services\ActivityLogger::log('delete', "Deleted promissory note {$note->reference_number}", 'PromissoryNote', $note->id);
        $note->delete();

        if (request()->expectsJson()) {
            return response()->json(['success' => true]);
        }

        return back()->with('success', 'Promissory note deleted.');
    }
}
