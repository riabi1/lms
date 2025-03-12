<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Instructor;
use Illuminate\Http\Request;

class InstructorManagementController extends Controller
{
    public function index()
    {
        $allinstructor = Instructor::all();
        return view('admin.backend.instructor.all_instructor', compact('allinstructor'));
    }

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