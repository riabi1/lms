<?php

namespace App\Http\Controllers\Instructor;

use App\Http\Controllers\Controller;
use App\Models\Report;
use App\Models\Course;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReportController extends Controller
{
    public function index()
    {
        $reports = Report::where('reporter_type', 'App\Models\Instructor')
                        ->where('reporter_id', Auth::guard('instructor')->id())
                        ->with(['course'])
                        ->latest()
                        ->get();

        return view('instructor.reports.index', compact('reports'));
    }

    public function create()
    {
        $courses = Course::where('courseable_type', 'App\Models\Instructor')
                         ->where('courseable_id', Auth::guard('instructor')->id())
                         ->where('status', 1)
                         ->get(['id', 'course_title']);

        return view('instructor.reports.create', compact('courses'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'type' => 'required|in:course_issue,technical,content_error,billing,other',
            'course_id' => 'nullable|exists:courses,id',
            'description' => 'required|string',
        ]);

        try {
            Report::create([
                'reporter_id' => Auth::guard('instructor')->id(),
                'reporter_type' => 'App\Models\Instructor',
                'course_id' => $validated['course_id'] ?? null,
                'title' => $validated['title'],
                'type' => $validated['type'],
                'description' => $validated['description'],
                'status' => 'pending',
            ]);

            return redirect()->route('instructor.reports.index')
                ->with('success', 'Report submitted successfully. Our team will review it soon.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Failed to submit report: ' . $e->getMessage())
                ->withInput();
        }
    }
}