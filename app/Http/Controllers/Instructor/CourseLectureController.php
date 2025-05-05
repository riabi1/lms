<?php

namespace App\Http\Controllers\Instructor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Course;
use App\Models\CourseSection;
use App\Models\CourseLecture;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class CourseLectureController extends Controller
{
    /**
     * Display a listing of lectures for a section.
     */
    public function index(Course $course, CourseSection $section)
    {
        $this->authorizeCourse($course);
        $this->authorizeSection($course, $section);
        $lectures = $section->lectures()->latest()->get();
        return view('instructor.course_lectures.index', compact('course', 'section', 'lectures'));
    }

    /**
     * Show the form for creating a new lecture.
     */
    public function create(Course $course, CourseSection $section = null)
    {
        $this->authorizeCourse($course);
        $sections = CourseSection::where('course_id', $course->id)->with('lectures')->get();
        return view('instructor.course_lectures.create', compact('course', 'sections', 'section'));
    }

    /**
     * Store a newly created lecture.
     */
    public function store(Request $request, Course $course)
    {
        $this->authorizeCourse($course);

        try {
            $validated = $this->validateLectureData($request);
            $section = CourseSection::findOrFail($validated['section_id']);
            $this->authorizeSection($course, $section);

            $data = $validated;
            $data['course_id'] = $course->id;

            // Handle file uploads
            $data['video'] = $this->uploadFile($request->file('video'), 'upload/lectures/videos');
            $data['additional_video'] = $this->uploadFile($request->file('additional_video'), 'upload/lectures/videos');
            $data['file_path'] = $this->uploadFile($request->file('file_path'), 'upload/lectures/files');

            CourseLecture::create($data);

            return redirect()->route('instructor.course_sections.show', [$course->id, $validated['section_id']])
                ->with(['message' => 'Lecture added successfully', 'alert-type' => 'success']);
        } catch (\Exception $e) {
            Log::error('Lecture creation failed:', ['error' => $e->getMessage()]);
            return redirect()->back()->with([
                'message' => 'Failed to create lecture: ' . $e->getMessage(),
                'alert-type' => 'error'
            ])->withInput();
        }
    }

    /**
     * Display the specified lecture.
     */
    public function show(Course $course, CourseLecture $lecture)
    {
        $this->authorizeCourse($course);
        $this->authorizeLecture($course, $lecture);
        return view('instructor.course_lectures.show', compact('course', 'lecture'));
    }

    /**
     * Show the form for editing the specified lecture.
     */
    public function edit(Course $course, CourseLecture $lecture)
    {
        $this->authorizeCourse($course);
        $this->authorizeLecture($course, $lecture);
        $sections = CourseSection::where('course_id', $course->id)->get();
        return view('instructor.course_lectures.edit', compact('course', 'lecture', 'sections'));
    }

    /**
     * Update the specified lecture.
     */
    public function update(Request $request, Course $course, CourseLecture $lecture)
    {
        $this->authorizeCourse($course);
        $this->authorizeLecture($course, $lecture);

        try {
            $validated = $this->validateLectureData($request);
            $section = CourseSection::findOrFail($validated['section_id']);
            $this->authorizeSection($course, $section);

            $data = $validated;

            // Handle file uploads and delete old files
            if ($request->hasFile('video')) {
                $this->deleteFile($lecture->video, 'upload/lectures/videos');
                $data['video'] = $this->uploadFile($request->file('video'), 'upload/lectures/videos');
            }
            if ($request->hasFile('additional_video')) {
                $this->deleteFile($lecture->additional_video, 'upload/lectures/videos');
                $data['additional_video'] = $this->uploadFile($request->file('additional_video'), 'upload/lectures/videos');
            }
            if ($request->hasFile('file_path')) {
                $this->deleteFile($lecture->file_path, 'upload/lectures/files');
                $data['file_path'] = $this->uploadFile($request->file('file_path'), 'upload/lectures/files');
            }

            $lecture->update($data);

            return redirect()->route('instructor.course_sections.show', [$course->id, $lecture->section_id])
                ->with(['message' => 'Lecture updated successfully', 'alert-type' => 'success']);
        } catch (\Exception $e) {
            Log::error('Lecture update failed:', ['error' => $e->getMessage()]);
            return redirect()->back()->with([
                'message' => 'Failed to update lecture: ' . $e->getMessage(),
                'alert-type' => 'error'
            ])->withInput();
        }
    }

    /**
     * Remove the specified lecture.
     */
    public function destroy(Course $course, CourseLecture $lecture)
    {
        $this->authorizeCourse($course);
        $this->authorizeLecture($course, $lecture);

        try {
            // Delete associated files
            $this->deleteFile($lecture->video, 'upload/lectures/videos');
            $this->deleteFile($lecture->additional_video, 'upload/lectures/videos');
            $this->deleteFile($lecture->file_path, 'upload/lectures/files');

            $sectionId = $lecture->section_id;
            $lecture->delete();

            return redirect()->route('instructor.course_sections.show', [$course->id, $sectionId])
                ->with(['message' => 'Lecture deleted successfully', 'alert-type' => 'success']);
        } catch (\Exception $e) {
            Log::error('Lecture deletion failed:', ['error' => $e->getMessage()]);
            return redirect()->back()->with([
                'message' => 'Failed to delete lecture: ' . $e->getMessage(),
                'alert-type' => 'error'
            ]);
        }
    }

    /**
     * Validate lecture data for store and update.
     */
    private function validateLectureData(Request $request)
    {
        return $request->validate([
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
    }

    /**
     * Authorize course access for the instructor.
     */
    private function authorizeCourse(Course $course)
    {
        $instructor = Auth::guard('instructor')->user();
        if ($course->courseable_type !== get_class($instructor) || $course->courseable_id !== $instructor->id) {
            abort(403, 'Unauthorized action.');
        }
    }

    /**
     * Authorize lecture access within the course.
     */
    private function authorizeLecture(Course $course, CourseLecture $lecture)
    {
        if ($lecture->course_id !== $course->id) {
            abort(404, 'Lecture not found.');
        }
    }

    /**
     * Authorize section access within the course.
     */
    private function authorizeSection(Course $course, CourseSection $section)
    {
        if ($section->course_id !== $course->id) {
            abort(404, 'Section not found.');
        }
    }

    /**
     * Upload a file to the specified public path.
     */
    private function uploadFile($file, $path)
    {
        if (!$file) {
            return null;
        }

        $fileName = time() . '_' . $file->getClientOriginalName();
        $publicPath = public_path($path);

        if (!file_exists($publicPath)) {
            mkdir($publicPath, 0755, true);
        }

        try {
            $file->move($publicPath, $fileName);
            Log::info('File uploaded:', ['path' => $publicPath . '/' . $fileName]);
        } catch (\Exception $e) {
            Log::error('File upload failed:', ['path' => $publicPath, 'error' => $e->getMessage()]);
            throw $e;
        }

        return $fileName;
    }

    /**
     * Delete a file from the specified public path.
     */
    private function deleteFile($filename, $path)
    {
        if ($filename) {
            $filePath = public_path($path . '/' . $filename);
            if (file_exists($filePath)) {
                unlink($filePath);
                Log::info('File deleted:', ['path' => $filePath]);
            }
        }
    }
}