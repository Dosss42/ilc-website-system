<?php

namespace App\Http\Controllers;

use App\Models\Enrollment;
use App\Models\PromissoryNote;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class PromissoryNoteController extends Controller
{
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

        $note = PromissoryNote::create([
            'reference_number' => PromissoryNote::generateReference(),
            'enrollment_id'    => $enrollment->id,
            'student_id'       => $enrollment->user_id,
            'created_by'       => Auth::id(),
            'amount_overdue'   => $validated['amount_overdue'],
            'amount_promised'  => $validated['amount_promised'],
            'promise_date'     => $validated['promise_date'],
            'date_issued'      => Carbon::today(),
            'parent_guardian'  => $validated['parent_guardian'],
            'remarks'          => $validated['remarks'],
            'status'           => 'pending',
        ]);

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

    public function printNote(PromissoryNote $note)
    {
        $note->load(['student:id,name', 'enrollment', 'createdBy:id,name']);
        return view('promissory-note-print', compact('note'));
    }

    public function destroy(PromissoryNote $note)
    {
        $note->delete();

        if (request()->expectsJson()) {
            return response()->json(['success' => true]);
        }

        return back()->with('success', 'Promissory note deleted.');
    }
}
