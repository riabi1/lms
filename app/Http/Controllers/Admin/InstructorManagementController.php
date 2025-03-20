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
        $allinstructor = Instructor::all();
        $section = $request->query('section', 'list'); // Par défaut : liste
        $instructor = null;

        if ($section === 'show') {
            $instructor = Instructor::find($request->query('id'));
        }

        return view('admin.backend.instructor.index', compact('allinstructor', 'section', 'instructor'));
    }

    /**
     * Display the specified instructor (handled in index).
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
            'message' => 'Instructor status updated successfully!'
        ]);
    }

}