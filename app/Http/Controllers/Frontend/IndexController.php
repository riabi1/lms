<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\BlogPost;
use App\Models\Category;
use App\Models\Course;
use App\Models\CourseGoal;
use App\Models\CourseLecture;
use App\Models\CourseSection;
use App\Models\Coupon;
use App\Models\Instructor;
use App\Models\Order;
use App\Models\Payment;
use App\Models\SubCategory;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Session;

class IndexController extends Controller
{
    public function CourseDetails($id, $slug)
    {
        $course = Course::with([
            'subcategory.category',
            'sections.lectures',
            'goals',
            'reviews' => fn($query) => $query->where('status', 1),
            'courseable'
        ])->findOrFail($id);

        if ($slug !== $course->course_name_slug) {
            return redirect()->route('course.details', [$course->id, $course->course_name_slug]);
        }

        $instructor = $course->courseable_type === 'App\Models\Instructor' && $course->courseable_id
            ? Instructor::find($course->courseable_id)
            : null;

        $goals = $course->goals->pluck('goal_name')->toArray();
        $categories = Cache::remember('categories', 3600, fn() => Category::orderBy('category_name', 'ASC')->get());

        $instructorCourses = $instructor
            ? Course::with(['courseable', 'reviews' => fn($query) => $query->where('status', 1), 'goals'])
                ->where('courseable_type', 'App\Models\Instructor')
                ->where('courseable_id', $instructor->id)
                ->where('id', '!=', $course->id)
                ->where('status', 1)
                ->latest()
                ->take(5)
                ->get()
            : collect();

        $relatedCourses = Course::with(['courseable', 'reviews' => fn($query) => $query->where('status', 1), 'goals'])
            ->where('subcategory_id', $course->subcategory_id)
            ->where('id', '!=', $id)
            ->where('status', 1)
            ->latest()
            ->take(5)
            ->get();

        $hasPurchased = Auth::check() && Order::where('user_id', Auth::id())
            ->where('course_id', $course->id)
            ->where('payment_status', 'paid')
            ->exists();

        $reviewCount = $course->reviews()
            ->where('status', 1)
            ->select('rating', \DB::raw('count(*) as count'))
            ->groupBy('rating')
            ->orderBy('rating', 'desc')
            ->get();

        $totalReviews = $reviewCount->sum('count');
        $percentages = [];
        for ($i = 5; $i >= 1; $i--) {
            $ratingCount = $reviewCount->where('rating', $i)->first();
            $count = $ratingCount ? $ratingCount->count : 0;
            $percent = $totalReviews > 0 ? ($count / $totalReviews) * 100 : 0;
            $percentages[] = [
                'rating' => $i,
                'percent' => $percent,
                'count' => $count,
            ];
        }

        $average = $course->reviews()->where('status', 1)->avg('rating') ?? 0;

        return view('frontend.course.course_details', compact(
            'course',
            'goals',
            'categories',
            'instructorCourses',
            'relatedCourses',
            'instructor',
            'hasPurchased',
            'percentages',
            'average',
            'reviewCount'
        ));
    }

    public function CategoryCourse(Request $request, $id, $slug)
    {
        $category = Category::where('id', $id)->where('category_slug', $slug)->firstOrFail();
        $query = Course::with(['goals', 'courseable', 'reviews' => fn($query) => $query->where('status', 1)])
            ->where('status', 1)
            ->whereHas('subcategory', fn($q) => $q->where('category_id', $category->id));

        if ($request->filled('label')) {
            $query->where('label', $request->input('label'));
        }

        if ($request->filled('price')) {
            if ($request->input('price') === 'free') {
                $query->where(fn($q) => $q->where('selling_price', 0)->orWhere('discount_price', 0));
            } elseif ($request->input('price') === 'paid') {
                $query->where(fn($q) => $q->where('selling_price', '>', 0)->orWhere('discount_price', '>', 0));
            }
        }

        if ($request->filled('bestseller') && $request->input('bestseller') == 1) {
            $query->where('bestseller', 1);
        }

        if ($request->filled('rating')) {
            $query->whereHas('reviews', fn($q) => $q->havingRaw('AVG(rating) >= ?', [$request->input('rating')]));
        }

        $courses = $query->paginate(10);
        $categories = Cache::remember('categories', 3600, fn() => Category::orderBy('category_name', 'ASC')->get());

        return view('frontend.category.category_all', compact('courses', 'category', 'categories'));
    }

    public function SubCategoryCourse(Request $request, $id, $slug)
    {
        $subcategory = SubCategory::where('id', $id)->where('subcategory_slug', $slug)->firstOrFail();
        $query = Course::with(['goals', 'courseable', 'reviews' => fn($query) => $query->where('status', 1)])
            ->where('status', 1)
            ->where('subcategory_id', $subcategory->id);

        if ($request->filled('label')) {
            $query->where('label', $request->input('label'));
        }

        if ($request->filled('price')) {
            if ($request->input('price') === 'free') {
                $query->where(fn($q) => $q->where('selling_price', 0)->orWhere('discount_price', 0));
            } elseif ($request->input('price') === 'paid') {
                $query->where(fn($q) => $q->where('selling_price', '>', 0)->orWhere('discount_price', '>', 0));
            }
        }

        if ($request->filled('bestseller') && $request->input('bestseller') == 1) {
            $query->where('bestseller', 1);
        }

        if ($request->filled('rating')) {
            $query->whereHas('reviews', fn($q) => $q->havingRaw('AVG(rating) >= ?', [$request->input('rating')]));
        }

        $courses = $query->paginate(10);
        $categories = Cache::remember('categories', 3600, fn() => Category::orderBy('category_name', 'ASC')->get());

        return view('frontend.category.subcategory_all', compact('courses', 'subcategory', 'categories'));
    }

    public function InstructorDetails($id)
    {
        $instructor = Instructor::with([
            'courses' => fn($query) => $query
                ->where('status', 1)
                ->select('id', 'courseable_id', 'courseable_type', 'course_name', 'course_image', 'selling_price', 'discount_price', 'course_name_slug')
                ->with([
                    'reviews' => fn($q) => $q->where('status', 1),
                    'courseable',
                    'goals'
                ])
        ])->find($id);
    
        if (!$instructor) {
            return redirect()->route('home')->with('error', 'Instructor not found.');
        }
    
        $courses = $instructor->courses()
            ->with([
                'reviews' => fn($q) => $q->where('status', 1),
                'courseable',
                'goals'
            ])
            ->where('status', 1)
            ->select('id', 'courseable_id', 'courseable_type', 'course_name', 'course_image', 'selling_price', 'discount_price', 'course_name_slug')
            ->paginate(6);
    
        $totalStudents = Order::whereIn('course_id', $instructor->courses()->where('status', 1)->pluck('id'))
            ->distinct('user_id')
            ->count();
    
        $totalReviews = $instructor->courses()->where('status', 1)->withCount('reviews')->get()->sum('reviews_count');
    
        $courses->getCollection()->transform(function ($course) {
            $course->final_price = $course->discount_price !== null
                ? max(0, $course->selling_price - $course->discount_price)
                : $course->selling_price;
            $course->discount_percentage = ($course->selling_price > 0 && $course->discount_price !== null)
                ? round(($course->selling_price - $course->final_price) / $course->selling_price * 100)
                : 0;
            $course->rating = $course->reviews->avg('rating') ?? 0;
            $course->reviews_count = $course->reviews->count();
            $course->course_image = $course->course_image ?? 'default-course.jpg';
            $course->slug = $course->course_name_slug;
            return $course;
        });
    
        return view('instructor.instructor_details', compact('instructor', 'courses', 'totalStudents', 'totalReviews'));
    }
    public function getCourseTooltip($id)
    {
        $course = Course::with([
            'reviews' => fn($q) => $q->where('status', 1),
            'courseable',
            'goals'
        ])->findOrFail($id);

        $course->final_price = $course->discount_price !== null
            ? max(0, $course->selling_price - $course->discount_price)
            : $course->selling_price;
        $course->rating = $course->reviews->avg('rating') ?? 0;
        $course->reviews_count = $course->reviews->count();
        $course->is_wishlisted = auth()->check() && \App\Models\Wishlist::where('trackable_type', 'App\Models\User')
            ->where('trackable_id', auth()->id())
            ->where('course_id', $course->id)
            ->exists();
        $course->is_in_cart = \Darryldecode\Cart\Facades\CartFacade::get($course->id) !== null;
        $course->has_purchased = auth()->check() && Order::where('user_id', auth()->id())
            ->where('course_id', $course->id)
            ->where('payment_status', 'paid')
            ->exists();

        return view('instructor.partials.course_tooltip', compact('course'))->render();
    }

    public function courses(Request $request)
    {
        $query = Course::with(['goals', 'courseable', 'reviews' => fn($query) => $query->where('status', 1)])
            ->where('status', 1);

        if ($request->filled('category_id')) {
            $query->whereHas('subcategory', fn($q) => $q->where('category_id', $request->input('category_id')));
        }

        if ($request->filled('label')) {
            $query->where('label', $request->input('label'));
        }

        if ($request->filled('price')) {
            if ($request->input('price') === 'free') {
                $query->where(fn($q) => $q->where('selling_price', 0)->orWhere('discount_price', 0));
            } elseif ($request->input('price') === 'paid') {
                $query->where(fn($q) => $q->where('selling_price', '>', 0)->orWhere('discount_price', '>', 0));
            }
        }

        if ($request->filled('bestseller') && $request->input('bestseller') == 1) {
            $query->where('bestseller', 1);
        }

        if ($request->filled('rating')) {
            $query->whereHas('reviews', fn($q) => $q->havingRaw('AVG(rating) >= ?', [$request->input('rating')]));
        }

        $courses = $query->paginate(10);
        $categories = Cache::remember('categories', 3600, fn() => Category::orderBy('category_name', 'ASC')->get());

        return view('frontend.course.course_list', compact('courses', 'categories'));
    }
}