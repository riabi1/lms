<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Instructor;
use Illuminate\Http\Request;

class InstructorManagementController extends Controller
{
    /**
     * Display a listing of the instructors or handle section display.
     */
    public function index(Request $request)
    {
        $allinstructor = Instructor::withCount('courses')->latest()->get();
        $section = $request->query('section', 'list');
        $instructor = null;

        if ($section === 'show' && $request->query('id')) {
            $instructor = Instructor::with('courses')->find($request->query('id'));
            if (!$instructor) {
                return redirect()->route('admin.instructors.index')->with('message', 'Instructor not found')->with('alert-type', 'error');
            }
        }

        return view('admin.instructor.index', compact('allinstructor', 'section', 'instructor'));
    }

    /**
     * Display the specified instructor.
     */
    public function show($id)
    {
        return redirect()->route('admin.instructors.index', ['section' => 'show', 'id' => $id]);
    }

    /**
     * Update the status of an instructor.
     */
    public function updateStatus(Request $request)
    {
        $request->validate([
            'instructor_id' => 'required|exists:instructors,id',
            'status' => 'required|in:0,1',
        ]);

        $instructor = Instructor::findOrFail($request->instructor_id);
        $instructor->update(['status' => $request->status]);

        return response()->json([
            'message' => 'Instructor status updated successfully!',
            'status' => $instructor->status,
        ]);
    }

    /**
     * Download the instructor's CV.
     */
    public function downloadCv($id)
    {
        $instructor = Instructor::findOrFail($id);

        // Check if CV exists
        if (!$instructor->cv || !file_exists(public_path('upload/instructor_cvs/' . $instructor->cv))) {
            return redirect()->back()->with('message', 'CV file not found')->with('alert-type', 'error');
        }

        $filePath = public_path('upload/instructor_cvs/' . $instructor->cv);

        return response()->download($filePath, $instructor->cv);
    }
}