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

        return view('admin.backend.courses.index', compact('courses'));
    }

    /**
     * Show the form for creating a new course.
     */
    public function create()
    {
        return view('admin.backend.courses.create');
    }

    /**
     * Store a newly created course in storage.
     */
    public function store(Request $request)
    {
        // Pour l'instant, pas de logique d'ajout côté admin.
        // Si vous voulez permettre la création, ajoutez la validation et la logique ici.
        return redirect()->route('admin.courses.index')->with([
            'message' => 'Course creation not implemented yet.',
            'alert-type' => 'info'
        ]);
    }

    /**
     * Display the specified course.
     */
    public function show($id)
    {
        $course = Course::findOrFail($id);
        return view('admin.backend.courses.show', compact('course'));
    }

    /**
     * Remove the specified course from storage.
     */
    public function destroy($id)
    {
        // Pour l'instant, pas de logique de suppression.
        // Si vous voulez permettre la suppression, ajoutez la logique ici.
        return redirect()->route('admin.courses.index')->with([
            'message' => 'Course deletion not implemented yet.',
            'alert-type' => 'info'
        ]);
    }

    /**
     * Update the status of a course (méthode personnalisée).
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