<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\SubCategory;
use App\Models\Course;
use App\Models\Instructor; 
use App\Models\Course_goal;
use App\Models\CourseSection;
use App\Models\CourseLecture;
use App\Models\Payment;
use App\Models\Order;
use App\Models\Coupon;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Carbon\Carbon;

class IndexController extends Controller
{
  
    /**
     * Display the details of a specific course.
     */
    public function CourseDetails($id, $slug)
    {
        $course = Course::with(['instructor', 'category', 'subcategory', 'sections', 'lectures', 'goals'])->findOrFail($id);
        $goals = $course->goals->pluck('goal_name')->toArray();
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

    /**
     * Display all courses in a specific category.
     */
 public function CategoryCourse(Request $request, $id, $slug)
    {
        $category = Category::where('id', $id)->where('category_slug', $slug)->firstOrFail();
        $query = Course::with(['goals', 'instructor', 'reviews'])
            ->where('status', 1)
            ->where('category_id', $category->id);

        // Filtre par niveau (label)
        if ($request->filled('label')) {
            $query->where('label', $request->input('label'));
        }

        // Filtre par prix
        if ($request->filled('price')) {
            if ($request->input('price') === 'free') {
                $query->where(function ($q) {
                    $q->where('selling_price', 0)->orWhere('discount_price', 0);
                });
            } elseif ($request->input('price') === 'paid') {
                $query->where(function ($q) {
                    $q->where('selling_price', '>', 0)->orWhere('discount_price', '>', 0);
                });
            }
        }

        // Filtre par "Bestseller"
        if ($request->filled('bestseller') && $request->input('bestseller') == 1) {
            $query->where('bestseller', 1);
        }

        // Filtre par note moyenne
        if ($request->filled('rating')) {
            $query->whereHas('reviews', function ($q) use ($request) {
                $q->havingRaw('AVG(rating) >= ?', [$request->input('rating')]);
            });
        }

        // Récupérer les cours paginés
        $courses = $query->paginate(10);

        // Ajouter la note moyenne et le nombre d'évaluations à chaque cours
        foreach ($courses as $course) {
            $course->rating = $course->reviews->avg('rating') ?? 0;
            $course->reviews_count = $course->reviews->count();
        }

        // Charger toutes les catégories pour la sidebar
        $categories = Category::orderBy('category_name', 'ASC')->get();

        return view('frontend.category.category_all', compact('courses', 'category', 'categories'));
    }

    /**
     * Display all courses in a specific subcategory.
     */
   public function SubCategoryCourse(Request $request, $id, $slug)
    {
        $subcategory = SubCategory::where('id', $id)->where('subcategory_slug', $slug)->firstOrFail();
        $query = Course::with(['goals', 'instructor', 'reviews'])
            ->where('status', 1)
            ->where('subcategory_id', $subcategory->id);

        // Filtre par niveau (label)
        if ($request->filled('label')) {
            $query->where('label', $request->input('label'));
        }

        // Filtre par prix
        if ($request->filled('price')) {
            if ($request->input('price') === 'free') {
                $query->where(function ($q) {
                    $q->where('selling_price', 0)->orWhere('discount_price', 0);
                });
            } elseif ($request->input('price') === 'paid') {
                $query->where(function ($q) {
                    $q->where('selling_price', '>', 0)->orWhere('discount_price', '>', 0);
                });
            }
        }

        // Filtre par "Bestseller"
        if ($request->filled('bestseller') && $request->input('bestseller') == 1) {
            $query->where('bestseller', 1);
        }

        // Filtre par note moyenne
        if ($request->filled('rating')) {
            $query->whereHas('reviews', function ($q) use ($request) {
                $q->havingRaw('AVG(rating) >= ?', [$request->input('rating')]);
            });
        }

        // Récupérer les cours paginés
        $courses = $query->paginate(10);

        // Ajouter la note moyenne et le nombre d'évaluations à chaque cours
        foreach ($courses as $course) {
            $course->rating = $course->reviews->avg('rating') ?? 0;
            $course->reviews_count = $course->reviews->count();
        }

        // Charger toutes les catégories pour la sidebar
        $categories = Category::orderBy('category_name', 'ASC')->get();

        return view('frontend.category.subcategory_all', compact('courses', 'subcategory', 'categories'));
    }

    /**
     * Display the details of a specific instructor.
     */
    public function InstructorDetails($id)
    {
        $instructor = Instructor::find($id); // Changé de User à Instructor
        if (!$instructor) {
            return redirect()->route('home')->with('error', 'Instructor not found.');
        }
        $courses = Course::where('instructor_id', $id)->where('status', '1')->get();
        $totalStudents = Order::whereIn('course_id', $courses->pluck('id'))->distinct('user_id')->count();
        $totalReviews = $courses->sum(function ($course) {
            return $course->reviews_count ?? 0; 
        });
        return view('instructor.instructor_details', compact('instructor', 'courses', 'totalStudents', 'totalReviews'));
    }

    /**
     * Display all available courses.
     */
public function courses(Request $request)
    {
        $query = Course::with(['goals', 'instructor', 'reviews'])->where('status', 1);

        // Filtre par catégorie
        if ($request->filled('category_id')) {
            $query->where('category_id', $request->input('category_id'));
        }

        // Filtre par niveau (label)
        if ($request->filled('label')) {
            $query->where('label', $request->input('label'));
        }

        // Filtre par prix
        if ($request->filled('price')) {
            if ($request->input('price') === 'free') {
                $query->where(function ($q) {
                    $q->where('selling_price', 0)->orWhere('discount_price', 0);
                });
            } elseif ($request->input('price') === 'paid') {
                $query->where(function ($q) {
                    $q->where('selling_price', '>', 0)->orWhere('discount_price', '>', 0);
                });
            }
        }

        // Filtre par "Bestseller"
        if ($request->filled('bestseller') && $request->input('bestseller') == 1) {
            $query->where('bestseller', 1);
        }

        // Filtre par note moyenne
        if ($request->filled('rating')) {
            $query->whereHas('reviews', function ($q) use ($request) {
                $q->havingRaw('AVG(rating) >= ?', [$request->input('rating')]);
            });
        }

        // Récupérer les cours paginés
        $courses = $query->paginate(10);

        // Ajouter la note moyenne et le nombre d'évaluations à chaque cours
        foreach ($courses as $course) {
            $course->rating = $course->reviews->avg('rating') ?? 0;
            $course->reviews_count = $course->reviews->count();
        }

        // Charger les catégories pour le filtre
        $categories = Category::orderBy('category_name', 'ASC')->get();

        return view('frontend.course.course_list', compact('courses', 'categories'));
    }
  
}