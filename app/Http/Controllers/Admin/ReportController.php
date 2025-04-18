<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Report;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function index()
    {
        $reports = Report::with(['reporter', 'course'])->latest()->get();

        return view('admin.reports.index', compact('reports'));
    }

    public function update(Request $request, Report $report)
    {
        $validated = $request->validate([
            'status' => 'required|in:pending,fixed,not_fixed',
        ]);

        try {
            $report->update([
                'status' => $validated['status'],
            ]);

            return redirect()->route('admin.reports.index')
                ->with('success', 'Report status updated successfully.');
        } catch (\Exception $e) {
            return redirect()->route('admin.reports.index')
                ->with('error', 'Failed to update report status.');
        }
    }
}