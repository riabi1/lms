<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ReportCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ReportCategoryController extends Controller
{
  public function index()
  {
    $categories = ReportCategory::latest()->get();
    return view('admin.report-categories.index', compact('categories'));
  }

  public function create()
  {
    return view('admin.report-categories.create');
  }

  public function store(Request $request)
  {
    $validated = $request->validate([
      'name' => 'required|string|max:255|unique:report_categories',
      'description' => 'nullable|string',
      'is_active' => 'boolean',
    ]);

    try {
      ReportCategory::create([
        'name' => $validated['name'],
        'slug' => Str::slug($validated['name']),
        'description' => $validated['description'],
        'is_active' => $request->has('is_active'),
      ]);

      return redirect()->route('admin.report-categories.index')
        ->with('success', 'Report category created successfully.');
    } catch (\Exception $e) {
      return redirect()->back()
        ->with('error', 'Failed to create category: ' . $e->getMessage())
        ->withInput();
    }
  }

  public function edit(ReportCategory $reportCategory)
  {
    return view('admin.report-categories.edit', compact('reportCategory'));
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

  public function destroy(ReportCategory $reportCategory)
  {
    try {
      $reportCategory->delete();
      return redirect()->route('admin.report-categories.index')
        ->with('success', 'Report category deleted successfully.');
    } catch (\Exception $e) {
      return redirect()->back()
        ->with('error', 'Failed to delete category: ' . $e->getMessage());
    }
  }
}
