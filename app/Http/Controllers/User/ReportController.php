<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Report;
use App\Models\Course;
use Illuminate\Http\Request;
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

        return view('User.reports.index', compact('reports'));
    }

    public function create()
    {
        $courses = Course::where('status', 1)->get(['id', 'course_title']);
        return view('User.reports.create', compact('courses'));
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
                'reporter_id' => Auth::id(),
                'reporter_type' => 'App\Models\User',
                'course_id' => $validated['course_id'] ?: null,
                'title' => $validated['title'],
                'type' => $validated['type'],
                'description' => $validated['description'],
                'status' => 'pending',
            ]);

            return redirect()->route('report.submit')
                ->with('success', 'Report submitted successfully. Our team will review it soon.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Failed to submit report. Please try again.')
                ->withInput();
        }
    }
}