<?php

namespace App\Http\Controllers;

use App\Models\GuidanceRecord;
use App\Services\ActivityLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class GuidanceController extends Controller
{
    /**
     * Display a listing of guidance records
     */
    public function index(Request $request)
    {
        $query = GuidanceRecord::with(['student:id,name', 'counselor:id,name']);

        if ($request->filled('student_id')) {
            $query->where('student_id', $request->student_id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('concern_type')) {
            $query->where('concern_type', $request->concern_type);
        }

        $records = $query->orderBy('date', 'desc')->get();
        return response()->json(['records' => $records]);
    }

    /**
     * Store a new guidance record
     */
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'student_id' => 'required|exists:users,id',
                'counselor_id' => 'required|exists:users,id',
                'date' => 'required|date',
                'concern_type' => 'required|string|max:100',
                'concern_description' => 'required|string',
                'action_taken' => 'nullable|string',
                'recommendations' => 'nullable|string',
                'follow_up_date' => 'nullable|date',
                'status' => 'required|in:open,in_progress,resolved,closed',
                'notes' => 'nullable|string',
            ]);

            $record = GuidanceRecord::create($validated);
            $record->load(['student:id,name', 'counselor:id,name']);

            // Description intentionally omits concern_description/notes — those are
            // sensitive counseling content and don't belong in a general audit log.
            ActivityLogger::log('create', "Added guidance record ({$record->concern_type}) for " . ($record->student->name ?? 'student #' . $record->student_id), 'GuidanceRecord', $record->id);

            return response()->json([
                'success' => true,
                'message' => 'Guidance record created successfully',
                'record' => $record
            ]);
        } catch (\Exception $e) {
            Log::error('Guidance record creation error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to create record: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display a specific guidance record
     */
    public function show(GuidanceRecord $guidance)
    {
        $guidance->load(['student:id,name', 'counselor:id,name']);
        return response()->json(['record' => $guidance]);
    }

    /**
     * Update a guidance record
     */
    public function update(Request $request, GuidanceRecord $guidance)
    {
        try {
            $validated = $request->validate([
                'student_id' => 'required|exists:users,id',
                'counselor_id' => 'required|exists:users,id',
                'date' => 'required|date',
                'concern_type' => 'required|string|max:100',
                'concern_description' => 'required|string',
                'action_taken' => 'nullable|string',
                'recommendations' => 'nullable|string',
                'follow_up_date' => 'nullable|date',
                'status' => 'required|in:open,in_progress,resolved,closed',
                'notes' => 'nullable|string',
            ]);

            $guidance->update($validated);
            $guidance->load(['student:id,name', 'counselor:id,name']);

            ActivityLogger::log('update', "Updated guidance record ({$guidance->concern_type}) for " . ($guidance->student->name ?? 'student #' . $guidance->student_id), 'GuidanceRecord', $guidance->id);

            return response()->json([
                'success' => true,
                'message' => 'Guidance record updated successfully',
                'record' => $guidance
            ]);
        } catch (\Exception $e) {
            Log::error('Guidance record update error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to update record: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove a guidance record
     */
    public function destroy(GuidanceRecord $guidance)
    {
        try {
            $guidance->load('student:id,name');
            $studentName = $guidance->student->name ?? 'student #' . $guidance->student_id;
            $concernType = $guidance->concern_type;
            $guidanceId  = $guidance->id;
            $guidance->delete();
            ActivityLogger::log('delete', "Deleted guidance record ({$concernType}) for {$studentName}", 'GuidanceRecord', $guidanceId);
            return response()->json([
                'success' => true,
                'message' => 'Guidance record deleted successfully'
            ]);
        } catch (\Exception $e) {
            Log::error('Guidance record deletion error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete record'
            ], 500);
        }
    }
}
