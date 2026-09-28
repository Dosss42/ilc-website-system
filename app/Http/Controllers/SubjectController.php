<?php

namespace App\Http\Controllers;

use App\Models\Subject;
use App\Services\ActivityLogger;
use Illuminate\Http\Request;

class SubjectController extends Controller
{
    public function index(Request $request)
    {
        $query = Subject::query();
        if ($request->filled('grade_level')) {
            $query->where('grade_level', $request->grade_level);
        }
        if ($request->filled('is_active')) {
            $query->where('is_active', filter_var($request->is_active, FILTER_VALIDATE_BOOLEAN));
        }
        $query->orderByDesc('created_at');

        // Pagination is opt-in (?paginated=1) — other callers (dropdown
        // population elsewhere, the dedicated Subject Management page) need
        // the full list, so the default response stays unpaginated.
        if ($request->boolean('paginated')) {
            $page = $query->paginate((int) $request->input('per_page', 15));
            return response()->json([
                'subjects'     => $page->items(),
                'current_page' => $page->currentPage(),
                'last_page'    => $page->lastPage(),
                'per_page'     => $page->perPage(),
                'total'        => $page->total(),
                'from'         => $page->firstItem(),
                'to'           => $page->lastItem(),
            ]);
        }

        return response()->json(['subjects' => $query->get()]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:subjects',
            'description' => 'nullable|string',
            'grade_level' => 'required|string|max:50',
            'is_active' => 'boolean'
        ]);

        $subject = Subject::create($validated);
        ActivityLogger::log('create', "Added subject \"{$subject->name}\" ({$subject->code}) — {$subject->grade_level}", 'Subject', $subject->id);
        return response()->json($subject, 201);
    }

    public function show(Subject $subject)
    {
        $subject->load(['sections', 'schedules']);
        return response()->json($subject);
    }

    public function update(Request $request, Subject $subject)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:subjects,code,' . $subject->id,
            'description' => 'nullable|string',
            'grade_level' => 'required|string|max:50',
            'is_active' => 'boolean'
        ]);

        $subject->update($validated);
        ActivityLogger::log('update', "Updated subject \"{$subject->name}\" ({$subject->code})", 'Subject', $subject->id);
        return response()->json($subject);
    }

    public function destroy(Subject $subject)
    {
        $name = $subject->name;
        $code = $subject->code;
        $subject->delete();
        ActivityLogger::log('delete', "Deleted subject \"{$name}\" ({$code})", 'Subject', $subject->id);
        return response()->json(['success' => true]);
    }
}
