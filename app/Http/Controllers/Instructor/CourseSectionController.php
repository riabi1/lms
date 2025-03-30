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
        $sections = $course->sections; // Utilise la relation si définie, sinon requête explicite
        // Ou : $sections = CourseSection::where('course_id', $course->id)->latest()->get();
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

        // Validation des données
        $request->validate([
            'section_title' => 'required|string|max:255',
        ]);

        CourseSection::create([
            'course_id' => $course->id,
            'section_title' => $request->section_title,
        ]);

        return redirect()->route('instructor.course_sections.index', $course)
            ->with('message', 'Section Added Successfully')
            ->with('alert-type', 'success');
    }

    public function show(Course $course, CourseSection $section)
    {
        $this->authorizeCourse($course);
        $this->authorizeSection($course, $section);
        return view('instructor.course_sections.show', compact('course', 'section'));
    }

    public function edit(Course $course, CourseSection $section)
    {
        $this->authorizeCourse($course);
        $this->authorizeSection($course, $section);
        return view('instructor.course_sections.edit', compact('course', 'section'));
    }

    public function update(Request $request, Course $course, CourseSection $section)
    {
        $this->authorizeCourse($course);
        $this->authorizeSection($course, $section);

        // Validation des données
        $request->validate([
            'section_title' => 'required|string|max:255',
        ]);

        $section->update(['section_title' => $request->section_title]);

        return redirect()->route('instructor.course_sections.index', $course)
            ->with('message', 'Section Updated Successfully')
            ->with('alert-type', 'success');
    }

    public function destroy(Course $course, CourseSection $section)
    {
        $this->authorizeCourse($course);
        $this->authorizeSection($course, $section);

        // Supprime les lectures associées (si relation définie) et la section
        $section->lectures()->delete();
        $section->delete();

        return redirect()->route('instructor.course_sections.index', $course)
            ->with('message', 'Section Deleted Successfully')
            ->with('alert-type', 'success');
    }

    /**
     * Vérifie si l'instructeur est autorisé à accéder au cours (polymorphique).
     */
    private function authorizeCourse(Course $course)
    {
        $instructor = Auth::guard('instructor')->user();
        if ($course->courseable_type !== get_class($instructor) || $course->courseable_id !== $instructor->id) {
            abort(403, 'Unauthorized action.');
        }
    }

    /**
     * Vérifie si la section appartient bien au cours.
     */
    private function authorizeSection(Course $course, CourseSection $section)
    {
        if ($section->course_id !== $course->id) {
            abort(404, 'Section not found.');
        }
    }
}