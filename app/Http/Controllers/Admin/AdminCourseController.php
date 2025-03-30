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
        $courses = Course::with(['courseable', 'subcategory', 'category'])
            ->whereHas('courseable', function ($query) {
                $query->where('courseable_type', 'App\\Models\\Instructor'); // Filtrer les cours des instructeurs
            })
            ->whereHas('subcategory') // Vérifie l'existence de la sous-catégorie
            ->whereHas('category')    // Vérifie l'existence de la catégorie
            ->latest()
            ->get();

        return view('admin.courses.index', compact('courses'));
    }

    /**
     * Display the specified course.
     */
    public function show($id)
    {
        $course = Course::with(['courseable', 'subcategory', 'category'])
            ->findOrFail($id);

        // Vérifie si le cours appartient à un instructeur
        if ($course->courseable_type !== 'App\\Models\\Instructor') {
            abort(403, 'Ce cours n\'est pas associé à un instructeur.');
        }

        return view('admin.courses.show', compact('course'));
    }

    /**
     * Update the status of a course.
     */
    public function updateCourseStatus(Request $request)
    {
        $request->validate([
            'course_id' => 'required|exists:courses,id',
            'is_checked' => 'required|boolean',
        ]);

        $course = Course::findOrFail($request->course_id);

        // Vérifie si le cours appartient à un instructeur
        if ($course->courseable_type !== 'App\\Models\\Instructor') {
            return response()->json([
                'message' => 'Impossible de modifier le statut : ce cours n\'est pas associé à un instructeur.',
            ], 403);
        }

        $course->status = $request->is_checked;
        $course->save();

        return response()->json([
            'message' => 'Course status updated successfully',
            'status' => $course->status,
        ]);
    }
}