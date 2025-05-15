<?php

namespace App\Http\Controllers\Admin;

use App\Models\Report;
use Illuminate\Http\Request;
use App\Models\ReportCategory;
use App\Http\Controllers\Controller;

class ReportController extends Controller
{
  public function index()
  {
    $reports = Report::with(['reporter', 'course', 'reportCategory'])->latest()->get();
    return view('admin.reports.index', compact('reports'));
  }

  public function update(Request $request, ReportCategory $reportCategory)
  {
    $validated = $request->validate([
      'name' => 'required|string|max:255|unique:report_categories,name,' . $reportCategory->id,
      'description' => 'nullable|string',
      'is_active' => 'boolean',
    ]);

    try {
      $reportCategory->update([
        'name' => $validated['name'],
        'slug' => Str::slug($validated['name']),
        'description' => $validated['description'],
        'is_active' => $request->has('is_active'),
      ]);

      return redirect()->route('admin.report-categories.index')
        ->with('success', 'Report category updated successfully.');
    } catch (\Exception $e) {
      return redirect()->back()
        ->with('error', 'Failed to update category: ' . $e->getMessage())
        ->withInput();
    }
  }
}