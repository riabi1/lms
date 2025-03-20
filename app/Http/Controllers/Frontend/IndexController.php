<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\SubCategory;
use App\Models\Course;
use App\Models\User;
use App\Models\Course_goal;
use App\Models\CourseSection;
use App\Models\CourseLecture;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class IndexController extends Controller
{
    public function CourseDetails($id, $slug)
    {
        $course = Course::with(['instructor', 'category', 'subcategory', 'sections', 'lectures', 'goals'])->findOrFail($id);
        $goals = $course->goals->pluck('goal_name')->toArray(); // Corrigé 'goal' en 'goal_name' si c'est le nom de la colonne
        $categories = Category::orderBy('category_name', 'ASC')->get();
        $instructorId = $course->instructor_id;
        $instructorCourses = Course::where('instructor_id', $instructorId)->latest()->get();
        $relatedCourses = Course::where('category_id', $course->category_id)
            ->where('id', '!=', $id)
            ->latest()
            ->limit(5)
            ->get();

        return view('frontend.course.course_details', compact('course', 'goals', 'categories', 'instructorCourses', 'relatedCourses'));
    }

    public function CategoryCourse($id, $slug)
    {
        $courses = Course::where('category_id', $id)->where('status', '1')->get();
        $category = Category::where('id', $id)->first();
        $categories = Category::latest()->get();
        return view('frontend.category.category_all', compact('courses', 'category', 'categories'));
    }

    public function SubCategoryCourse($id, $slug)
    {
        $courses = Course::where('subcategory_id', $id)->where('status', '1')->get();
        $subcategory = SubCategory::where('id', $id)->first();
        $categories = Category::latest()->get();
        return view('frontend.category.subcategory_all', compact('courses', 'subcategory', 'categories'));
    }

    public function InstructorDetails($id)
    {
        $instructor = User::findOrFail($id); // Ajouté findOrFail pour sécurité
        $courses = Course::where('instructor_id', $id)->get();
        return view('frontend.instructor.instructor_details', compact('instructor', 'courses'));
    }
}