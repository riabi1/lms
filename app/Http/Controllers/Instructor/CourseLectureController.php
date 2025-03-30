<?php

namespace App\Http\Controllers\Instructor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Course;
use App\Models\CourseSection;
use App\Models\CourseLecture;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class CourseLectureController extends Controller
{
    public function index(Course $course, CourseSection $section)
    {
        $this->authorizeCourse($course);
        $this->authorizeSection($course, $section);
        $lectures = $section->lectures()->latest()->get(); // Utilise la relation
        return view('instructor.course_lectures.index', compact('course', 'section', 'lectures'));
    }

    public function create(Course $course, CourseSection $section = null)
    {
        $this->authorizeCourse($course);
        $sections = CourseSection::where('course_id', $course->id)->with('lectures')->get();
        return view('instructor.course_lectures.create', compact('course', 'sections', 'section'));
    }

    public function store(Request $request, Course $course)
    {
        $this->authorizeCourse($course);

        $request->validate([
            'lecture_title' => 'required|string|max:255',
            'section_id' => 'required|exists:course_sections,id',
            'url' => 'nullable|url',
            'video' => 'nullable|file|mimes:mp4,webm|max:102400', // 100MB max
            'content' => 'nullable|string',
            'additional_video' => 'nullable|file|mimes:mp4,webm|max:102400', // 100MB max
            'file_path' => 'nullable|file|mimes:pdf,doc,docx,jpg,png|max:20480', // 20MB max
            'external_link' => 'nullable|url',
            'resources_description' => 'nullable|string|max:1000',
        ]);

        $data = $request->except(['video', 'additional_video', 'file_path']);
        $data['course_id'] = $course->id;

        // Gestion des fichiers
        if ($request->hasFile('video')) {
            $data['video'] = $request->file('video')->store('upload/lectures/videos', 'public');
        }
        if ($request->hasFile('additional_video')) {
            $data['additional_video'] = $request->file('additional_video')->store('upload/lectures/videos', 'public');
        }
        if ($request->hasFile('file_path')) {
            $data['file_path'] = $request->file('file_path')->store('upload/lectures/files', 'public');
        }

        CourseLecture::create($data);

        return redirect()->route('instructor.course_sections.show', [$course->id, $request->section_id])
            ->with('message', 'Lecture added successfully')
            ->with('alert-type', 'success');
    }

    public function show(Course $course, CourseLecture $lecture)
    {
        $this->authorizeCourse($course);
        $this->authorizeLecture($course, $lecture);
        return view('instructor.course_lectures.show', compact('course', 'lecture'));
    }

    public function edit(Course $course, CourseLecture $lecture)
    {
        $this->authorizeCourse($course);
        $this->authorizeLecture($course, $lecture);
        $sections = CourseSection::where('course_id', $course->id)->get(); // Pour permettre de changer de section
        return view('instructor.course_lectures.edit', compact('course', 'lecture', 'sections'));
    }

    public function update(Request $request, Course $course, CourseLecture $lecture)
    {
        $this->authorizeCourse($course);
        $this->authorizeLecture($course, $lecture);

        $request->validate([
            'lecture_title' => 'required|string|max:255',
            'section_id' => 'required|exists:course_sections,id',
            'url' => 'nullable|url',
            'video' => 'nullable|file|mimes:mp4,webm|max:102400',
            'content' => 'nullable|string',
            'additional_video' => 'nullable|file|mimes:mp4,webm|max:102400',
            'file_path' => 'nullable|file|mimes:pdf,doc,docx,jpg,png|max:20480',
            'external_link' => 'nullable|url',
            'resources_description' => 'nullable|string|max:1000',
        ]);

        $data = $request->except(['video', 'additional_video', 'file_path']);

        // Gestion des fichiers avec suppression des anciens si remplacés
        if ($request->hasFile('video')) {
            if ($lecture->video) {
                Storage::disk('public')->delete($lecture->video);
            }
            $data['video'] = $request->file('video')->store('upload/lectures/videos', 'public');
        }
        if ($request->hasFile('additional_video')) {
            if ($lecture->additional_video) {
                Storage::disk('public')->delete($lecture->additional_video);
            }
            $data['additional_video'] = $request->file('additional_video')->store('upload/lectures/videos', 'public');
        }
        if ($request->hasFile('file_path')) {
            if ($lecture->file_path) {
                Storage::disk('public')->delete($lecture->file_path);
            }
            $data['file_path'] = $request->file('file_path')->store('upload/lectures/files', 'public');
        }

        $lecture->update($data);

        return redirect()->route('instructor.course_sections.show', [$course->id, $lecture->section_id])
            ->with('message', 'Lecture updated successfully')
            ->with('alert-type', 'success');
    }

    public function destroy(Course $course, CourseLecture $lecture)
    {
        $this->authorizeCourse($course);
        $this->authorizeLecture($course, $lecture);

        // Supprimer les fichiers associés
        if ($lecture->video) {
            Storage::disk('public')->delete($lecture->video);
        }
        if ($lecture->additional_video) {
            Storage::disk('public')->delete($lecture->additional_video);
        }
        if ($lecture->file_path) {
            Storage::disk('public')->delete($lecture->file_path);
        }

        $sectionId = $lecture->section_id;
        $lecture->delete();

        return redirect()->route('instructor.course_sections.show', [$course->id, $sectionId])
            ->with('message', 'Lecture deleted successfully')
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
     * Vérifie si la leçon appartient au cours.
     */
    private function authorizeLecture(Course $course, CourseLecture $lecture)
    {
        if ($lecture->course_id !== $course->id) {
            abort(404, 'Lecture not found.');
        }
    }

    /**
     * Vérifie si la section appartient au cours.
     */
    private function authorizeSection(Course $course, CourseSection $section)
    {
        if ($section->course_id !== $course->id) {
            abort(404, 'Section not found.');
        }
    }
}