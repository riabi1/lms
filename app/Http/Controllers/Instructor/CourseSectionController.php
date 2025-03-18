<?php
namespace App\Http\Controllers\Instructor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Course;
use App\Models\CourseSection;
use Illuminate\Support\Facades\Auth;

class CourseSectionController extends Controller
{
    public function index(Course $course)
    {
        $this->authorizeCourse($course);
        $sections = CourseSection::where('course_id', $course->id)->latest()->get();
        return view('instructor.course_sections.index', compact('course', 'sections'));
    }

    public function create(Course $course)
    {
        $this->authorizeCourse($course);
        return view('instructor.course_sections.create', compact('course'));
    }

    public function store(Request $request, Course $course)
    {
        $this->authorizeCourse($course);
        CourseSection::create([
            'course_id' => $course->id,
            'section_title' => $request->section_title,
        ]);
        return redirect()->route('instructor.course_sections.index', $course)->with([
            'message' => 'Section Added Successfully',
            'alert-type' => 'success'
        ]);
    }

    public function show(Course $course, CourseSection $section)
    {
        $this->authorizeCourse($course);
        if ($section->course_id !== $course->id) {
            abort(404);
        }
        return view('instructor.course_sections.show', compact('course', 'section'));
    }

    public function edit(Course $course, CourseSection $section)
    {
        $this->authorizeCourse($course);
        if ($section->course_id !== $course->id) {
            abort(404);
        }
        return view('instructor.course_sections.edit', compact('course', 'section'));
    }

    public function update(Request $request, Course $course, CourseSection $section)
    {
        $this->authorizeCourse($course);
        if ($section->course_id !== $course->id) {
            abort(404);
        }
        $section->update(['section_title' => $request->section_title]);
        return redirect()->route('instructor.course_sections.index', $course)->with([
            'message' => 'Section Updated Successfully',
            'alert-type' => 'success'
        ]);
    }

    public function destroy(Course $course, CourseSection $section)
    {
        $this->authorizeCourse($course);
        if ($section->course_id !== $course->id) {
            abort(404);
        }
        $section->lectures()->delete();
        $section->delete();
        return redirect()->route('instructor.course_sections.index', $course)->with([
            'message' => 'Section Deleted Successfully',
            'alert-type' => 'success'
        ]);
    }

    private function authorizeCourse(Course $course)
    {
        if ($course->instructor_id !== Auth::guard('instructor')->id()) {
            abort(403, 'Unauthorized action.');
        }
    }
}