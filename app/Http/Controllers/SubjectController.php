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
        $subjects = $query->orderBy('name')->get();
        return response()->json(['subjects' => $subjects]);
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
