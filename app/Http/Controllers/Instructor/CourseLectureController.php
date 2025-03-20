<?php
namespace App\Http\Controllers\Instructor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Course;
use App\Models\CourseSection;
use App\Models\CourseLecture;
use Illuminate\Support\Facades\Auth;

class CourseLectureController extends Controller
{
    public function index(Course $course, CourseSection $section)
    {
        $this->authorizeCourse($course);
        if ($section->course_id !== $course->id) {
            abort(404);
        }
        $lectures = CourseLecture::where('section_id', $section->id)->latest()->get();
        return view('instructor.course_lectures.index', compact('course', 'section', 'lectures'));
    }

   public function create(Course $course)
    {
      
        $sections = CourseSection::where('course_id', $course->id)->with('lectures')->get();
        return view('instructor.course_lectures.create', compact('course', 'sections'));
    }

    public function store(Request $request, Course $course)
{
    $request->validate([
        'lecture_title' => 'required|string|max:255',
        'section_id' => 'required|exists:course_sections,id',
        'url' => 'nullable|url',
        'video' => 'nullable|file|mimes:mp4,webm|max:102400',
        'content' => 'nullable|string',
    ]);

    $data = $request->all();
    $data['course_id'] = $course->id;

    if ($request->hasFile('video')) {
        $data['video'] = $request->file('video')->store('upload/lectures', 'public');
    }

    CourseLecture::create($data);

    return response()->json(['success' => 'Lecture added successfully']);
}

    public function show(Course $course, CourseLecture $lecture)
    {
        $this->authorizeCourse($course);
        if ($lecture->course_id !== $course->id) {
            abort(404);
        }
        return view('instructor.course_lectures.show', compact('course', 'lecture'));
    }

   public function edit(Course $course, CourseLecture $lecture)
    {
       
        return view('instructor.course_lectures.edit', compact('course', 'lecture'));
    }

   public function update(Request $request, Course $course, CourseLecture $lecture)
{
    $request->validate([
        'lecture_title' => 'required|string|max:255',
        'url' => 'nullable|url',
        'video' => 'nullable|file|mimes:mp4,webm|max:102400',
        'content' => 'nullable|string',
    ]);

    $data = $request->all();

    if ($request->hasFile('video')) {
        // Supprimer l’ancienne vidéo si elle existe
        if ($lecture->video) {
            Storage::disk('public')->delete($lecture->video);
        }
        $data['video'] = $request->file('video')->store('upload/lectures', 'public');
    }

    $lecture->update($data);

    return redirect()->route('instructor.course_lectures.create', $course->id)->with('success', 'Lecture updated successfully');
}

    public function destroy(Course $course, CourseLecture $lecture)
    {
        $this->authorizeCourse($course);
        if ($lecture->course_id !== $course->id) {
            abort(404);
        }
        $lecture->delete();
        return redirect()->back()->with([
            'message' => 'Lecture Deleted Successfully',
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