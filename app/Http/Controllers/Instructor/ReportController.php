<?php

namespace App\Http\Controllers\Instructor;

use App\Models\Course;
use App\Models\Report;
use Illuminate\Http\Request;
use App\Models\ReportCategory;
use App\Http\Controllers\Controller;
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
        $reportCategories = ReportCategory::get(['id', 'name']);

        return view('instructor.reports.create', compact('courses', 'reportCategories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'report_category_id' => 'required|exists:report_categories,id',
            'course_id' => 'nullable|exists:courses,id',
            'description' => 'required|string',
        ]);

        try {
            Report::create([
                'reporter_id' => Auth::guard('instructor')->id(),
                'reporter_type' => 'App\Models\Instructor',
                'course_id' => $validated['course_id'] ?? null,
                'report_category_id' => $validated['report_category_id'],
                'title' => $validated['title'],
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

    public function show(Report $report)
    {
        // Ensure the report belongs to the authenticated instructor
        if ($report->reporter_type !== 'App\Models\Instructor' || $report->reporter_id !== auth('instructor')->id()) {
            abort(403, 'Unauthorized access.');
        }

        // Mark related notifications as read
        auth('instructor')->user()->notifications()
            ->where('data->type', 'report_resolution')
            ->where('data->report_id', $report->id)
            ->update(['read_at' => now()]);

        return view('instructor.reports.show', compact('report'));
    }

    public function storeFeedback(Request $request, Report $report)
    {
        // Ensure the report belongs to the authenticated instructor
        if ($report->reporter_type !== 'App\Models\Instructor' || $report->reporter_id !== auth('instructor')->id()) {
            abort(403, 'Unauthorized access.');
        }

        // Delegate to Admin\ReportController's storeFeedback method
        return app(\App\Http\Controllers\Admin\ReportController::class)->storeFeedback($request, $report);
    }
}