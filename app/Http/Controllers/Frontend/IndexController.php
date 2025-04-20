<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\SubCategory;
use App\Models\Course;
use App\Models\Instructor;
use App\Models\CourseGoal;
use App\Models\CourseSection;
use App\Models\CourseLecture;
use App\Models\Payment;
use App\Models\Order;
use App\Models\Coupon;
use App\Models\BlogPost;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Carbon\Carbon;

class IndexController extends Controller
{
    public function index()
    {
        $courses = Course::with(['courseable', 'reviews', 'goals'])
            ->where('status', 1)
            ->orderBy('id', 'ASC')
            ->limit(6)
            ->get();

        $categories = Category::orderBy('category_name', 'ASC')->get();

        // Recommendation logic
        $recommendedCourses = collect();
        $recommendationMessage = '';

        if (Auth::check()) {
            // Get categories where user has purchased 2 or more courses
            $userOrders = Order::where('user_id', Auth::id())
                ->where('payment_status', 'paid')
                ->with(['course.subcategory.category'])
                ->get();

            // Count courses per category
            $categoryCounts = $userOrders->groupBy(function ($order) {
                return $order->course->subcategory->category->id;
            })->map->count();

            // Find categories with 2 or more purchases
            $preferredCategories = $categoryCounts->filter(function ($count) {
                return $count >= 2;
            })->keys();

            if ($preferredCategories->isNotEmpty()) {
                // Get up to 3 unpurchased courses from preferred categories
                $purchasedCourseIds = $userOrders->pluck('course_id')->toArray();
                $recommendedCourses = Course::with(['courseable', 'reviews', 'goals'])
                    ->where('status', 1)
                    ->whereNotIn('id', $purchasedCourseIds)
                    ->whereHas('subcategory', function ($query) use ($preferredCategories) {
                        $query->whereIn('category_id', $preferredCategories);
                    })
                    ->inRandomOrder()
                    ->limit(3)
                    ->get();

                if ($recommendedCourses->isNotEmpty()) {
                    $categoryNames = Category::whereIn('id', $preferredCategories)
                        ->pluck('category_name')
                        ->implode(', ');
                    $recommendationMessage = "Since you like courses in {$categoryNames}, we recommend these courses:";
                }
            }
        }

        return view('frontend.index', compact(
            'courses',
            'categories',
            'recommendedCourses',
            'recommendationMessage'
        ));
    }

    public function CourseDetails($id, $slug)
    {
        $course = Course::with([
            'subcategory.category',
            'sections.lectures',
            'goals',
            'reviews'
        ])->findOrFail($id);

        if ($slug !== $course->course_name_slug) {
            return redirect()->route('course.details', [$course->id, $course->course_name_slug]);
        }

        $instructor = $course->courseable_type === 'App\Models\Instructor' && $course->courseable_id
            ? Instructor::find($course->courseable_id)
            : null;

        $goals = $course->goals->pluck('goal_name')->toArray();
        $categories = Category::orderBy('category_name', 'ASC')->get();

        $instructorCourses = $instructor
            ? Course::where('courseable_type', 'App\Models\Instructor')
                    ->where('courseable_id', $instructor->id)
                    ->where('id', '!=', $course->id)
                    ->latest()
                    ->get()
            : collect();

        $relatedCourses = Course::where('subcategory_id', $course->subcategory_id)
            ->where('id', '!=', $id)
            ->latest()
            ->limit(5)
            ->get();

        $hasPurchased = Auth::check() && Order::where('user_id', Auth::id())
            ->where('course_id', $course->id)
            ->where('payment_status', 'paid')
            ->exists();

        return view('frontend.course.course_details', compact(
            'course',
            'goals',
            'categories',
            'instructorCourses',
            'relatedCourses',
            'instructor',
            'hasPurchased'
        ));
    }

    public function CategoryCourse(Request $request, $id, $slug)
    {
        $category = Category::where('id', $id)->where('category_slug', $slug)->firstOrFail();
        $query = Course::with(['goals', 'courseable', 'reviews'])
            ->where('status', 1)
            ->whereHas('subcategory', function ($q) use ($category) {
                $q->where('category_id', $category->id);
            });

        if ($request->filled('label')) {
            $query->where('label', $request->input('label'));
        }

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

        if ($request->filled('bestseller') && $request->input('bestseller') == 1) {
            $query->where('bestseller', 1);
        }

        if ($request->filled('rating')) {
            $query->whereHas('reviews', function ($q) use ($request) {
                $q->havingRaw('AVG(rating) >= ?', [$request->input('rating')]);
            });
        }

        $courses = $query->paginate(10);

        foreach ($courses as $course) {
            $course->rating = $course->reviews->avg('rating') ?? 0;
            $course->reviews_count = $course->reviews->count();
        }

        $categories = Category::orderBy('category_name', 'ASC')->get();

        return view('frontend.category.category_all', compact('courses', 'category', 'categories'));
    }

    public function SubCategoryCourse(Request $request, $id, $slug)
    {
        $subcategory = SubCategory::where('id', $id)->where('subcategory_slug', $slug)->firstOrFail();
        $query = Course::with(['goals', 'courseable', 'reviews'])
            ->where('status', 1)
            ->where('subcategory_id', $subcategory->id);

        if ($request->filled('label')) {
            $query->where('label', $request->input('label'));
        }

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

        if ($request->filled('bestseller') && $request->input('bestseller') == 1) {
            $query->where('bestseller', 1);
        }

        if ($request->filled('rating')) {
            $query->whereHas('reviews', function ($q) use ($request) {
                $q->havingRaw('AVG(rating) >= ?', [$request->input('rating')]);
            });
        }

        $courses = $query->paginate(10);

        foreach ($courses as $course) {
            $course->rating = $course->reviews->avg('rating') ?? 0;
            $course->reviews_count = $course->reviews->count();
        }

        $categories = Category::orderBy('category_name', 'ASC')->get();

        return view('frontend.category.subcategory_all', compact('courses', 'subcategory', 'categories'));
    }

    public function InstructorDetails($id)
    {
        $instructor = Instructor::with(['courses' => function ($query) {
            $query->where('status', 1)->withCount('reviews');
        }])->find($id);

        if (!$instructor) {
            return redirect()->route('home')->with('error', 'Instructor not found.');
        }

        $courses = $instructor->courses;
        $totalStudents = Order::whereIn('course_id', $courses->pluck('id'))
            ->distinct('user_id')
            ->count();
        $totalReviews = $courses->sum('reviews_count');

        return view('instructor.instructor_details', compact('instructor', 'courses', 'totalStudents', 'totalReviews'));
    }

    public function courses(Request $request)
    {
        $query = Course::with(['goals', 'courseable', 'reviews'])->where('status', 1);

        if ($request->filled('category_id')) {
            $query->whereHas('subcategory', function ($q) use ($request) {
                $q->where('category_id', $request->input('category_id'));
            });
        }

        if ($request->filled('label')) {
            $query->where('label', $request->input('label'));
        }

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

        if ($request->filled('bestseller') && $request->input('bestseller') == 1) {
            $query->where('bestseller', 1);
        }

        if ($request->filled('rating')) {
            $query->whereHas('reviews', function ($q) use ($request) {
                $q->havingRaw('AVG(rating) >= ?', [$request->input('rating')]);
            });
        }

        $courses = $query->paginate(10);

        foreach ($courses as $course) {
            $course->rating = $course->reviews->avg('rating') ?? 0;
            $course->reviews_count = $course->reviews->count();
        }

        $categories = Category::orderBy('category_name', 'ASC')->get();

        return view('frontend.course.course_list', compact('courses', 'categories'));
    }
}