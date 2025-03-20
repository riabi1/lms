<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Course;

class AdminCourseController extends Controller
{
    /**
     * Display a listing of the courses.
     */
    public function index()
    {
        $courses = Course::with(['instructor', 'category'])
            ->whereNotNull('instructor_id')
            ->whereExists(function ($query) {
                $query->select('*')
                      ->from('instructors')
                      ->whereColumn('instructors.id', 'courses.instructor_id');
            })
            ->whereNotNull('category_id')
            ->whereExists(function ($query) {
                $query->select('*')
                      ->from('categories')
                      ->whereColumn('categories.id', 'courses.category_id');
            })
            ->latest()
            ->get();

        return view('admin.courses.index', compact('courses'));
    }



    /**
     * Display the specified course.
     */
    public function show($id)
    {
        $course = Course::findOrFail($id);
        return view('admin.courses.show', compact('course'));
    }



    /**
     * Update the status of a course 
     */
    public function UpdateCourseStatus(Request $request)
    {
        $request->validate([
            'course_id' => 'required|exists:courses,id',
            'is_checked' => 'boolean'
        ]);

        $course = Course::findOrFail($request->input('course_id'));
        $course->status = $request->input('is_checked', 0);
        $course->save();

        return response()->json(['message' => 'Course Status Updated Successfully']);
    }
}