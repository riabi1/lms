<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Course;

class AdminCourseController extends Controller
{
  public function __construct()
  {
    $this->middleware('auth:admin');
  }

  public function AdminAllCourse()
  {
    $courses = Course::latest()->get();
    return view('admin.backend.courses.all_course', compact('courses'));
  }

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

  public function AdminCourseDetails($id)
  {
    $course = Course::findOrFail($id);
    return view('admin.backend.courses.course_details', compact('course'));
  }
}
