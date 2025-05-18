<?php

namespace App\Http\Controllers\User;

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
        $reports = Report::where('reporter_type', 'App\Models\User')
                        ->where('reporter_id', Auth::id())
                        ->with(['course'])
                        ->latest()
                        ->get();

        return view('user.reports.index', compact('reports'));
    }

    public function create()
    {
        $courses = Course::where('status', 1)->get(['id', 'course_title']);
        $reportCategories = ReportCategory::get(['id', 'name']);
        return view('user.reports.create', compact('courses', 'reportCategories'));
    }

    public function store(Request $request)
    {
        if (!Auth::check()) {
            return redirect()->back()->with('error', 'You must be logged in to submit a report.')->withInput();
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'report_category_id' => 'required|exists:report_categories,id',
            'course_id' => 'nullable|exists:courses,id',
            'description' => 'required|string',
        ]);

        try {
            Report::create([
                'reporter_id' => Auth::id(),
                'reporter_type' => 'App\Models\User',
                'course_id' => $validated['course_id'] ?: null,
                'report_category_id' => $validated['report_category_id'],
                'title' => $validated['title'],
                'description' => $validated['description'],
                'status' => 'pending',
            ]);

            return redirect()->route('report.index')
                ->with('success', 'Report submitted successfully. Our team will review it soon.');
        } catch (\Exception $e) {
            \Log::error('Report submission failed: ' . $e->getMessage(), [
                'user_id' => Auth::id(),
                'validated_data' => $validated,
                'trace' => $e->getTraceAsString(),
            ]);

            return redirect()->back()
                ->with('error', 'Failed to submit report: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function show(Report $report)
    {
        // Ensure the report belongs to the authenticated user
        if ($report->reporter_type !== 'App\Models\User' || $report->reporter_id !== auth('web')->id()) {
            abort(403, 'Unauthorized access.');
        }

        // Mark related notifications as read
        auth('web')->user()->notifications()
            ->where('data->type', 'report_resolution')
            ->where('data->report_id', $report->id)
            ->update(['read_at' => now()]);

        return view('user.reports.show', compact('report'));
    }

    public function storeFeedback(Request $request, Report $report)
    {
        // Ensure the report belongs to the authenticated user
        if ($report->reporter_type !== 'App\Models\User' || $report->reporter_id !== auth('web')->id()) {
            abort(403, 'Unauthorized access.');
        }

        // Delegate to Admin\ReportController's storeFeedback method
        return app(\App\Http\Controllers\Admin\ReportController::class)->storeFeedback($request, $report);
    }
}