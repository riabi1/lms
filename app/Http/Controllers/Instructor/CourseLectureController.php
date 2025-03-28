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
            'video' => 'nullable|file|mimes:mp4,webm|max:102400', // Vidéo principale
            'content' => 'nullable|string',
            'additional_video' => 'nullable|file|mimes:mp4,webm|max:102400', // Vidéo supplémentaire
            'file_path' => 'nullable|file|mimes:pdf,doc,docx,jpg,png|max:20480', // Fichier (20MB max)
            'external_link' => 'nullable|url', // Lien externe
            'resources_description' => 'nullable|string|max:1000', // Description des ressources
        ]);

        $data = $request->all();
        $data['course_id'] = $course->id;

        // Gestion de la vidéo principale
        if ($request->hasFile('video')) {
            $data['video'] = $request->file('video')->store('upload/lectures/videos', 'public');
        }

        // Gestion de la vidéo supplémentaire
        if ($request->hasFile('additional_video')) {
            $data['additional_video'] = $request->file('additional_video')->store('upload/lectures/videos', 'public');
        }

        // Gestion du fichier
        if ($request->hasFile('file_path')) {
            $data['file_path'] = $request->file('file_path')->store('upload/lectures/files', 'public');
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
        $this->authorizeCourse($course); // Ajouté pour la sécurité
        if ($lecture->course_id !== $course->id) {
            abort(404);
        }
        return view('instructor.course_lectures.edit', compact('course', 'lecture'));
    }

    public function update(Request $request, Course $course, CourseLecture $lecture)
    {
        $this->authorizeCourse($course); // Ajouté pour la sécurité
        if ($lecture->course_id !== $course->id) {
            abort(404);
        }

        $request->validate([
            'lecture_title' => 'required|string|max:255',
            'url' => 'nullable|url',
            'video' => 'nullable|file|mimes:mp4,webm|max:102400',
            'content' => 'nullable|string',
            'additional_video' => 'nullable|file|mimes:mp4,webm|max:102400',
            'file_path' => 'nullable|file|mimes:pdf,doc,docx,jpg,png|max:20480',
            'external_link' => 'nullable|url',
            'resources_description' => 'nullable|string|max:1000',
        ]);

        $data = $request->all();

        // Gestion de la vidéo principale
        if ($request->hasFile('video')) {
            if ($lecture->video) {
                Storage::disk('public')->delete($lecture->video);
            }
            $data['video'] = $request->file('video')->store('upload/lectures/videos', 'public');
        }

        // Gestion de la vidéo supplémentaire
        if ($request->hasFile('additional_video')) {
            if ($lecture->additional_video) {
                Storage::disk('public')->delete($lecture->additional_video);
            }
            $data['additional_video'] = $request->file('additional_video')->store('upload/lectures/videos', 'public');
        }

        // Gestion du fichier
        if ($request->hasFile('file_path')) {
            if ($lecture->file_path) {
                Storage::disk('public')->delete($lecture->file_path);
            }
            $data['file_path'] = $request->file('file_path')->store('upload/lectures/files', 'public');
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

        // Supprimer les fichiers associés avant de supprimer la lecture
        if ($lecture->video) {
            Storage::disk('public')->delete($lecture->video);
        }
        if ($lecture->additional_video) {
            Storage::disk('public')->delete($lecture->additional_video);
        }
        if ($lecture->file_path) {
            Storage::disk('public')->delete($lecture->file_path);
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