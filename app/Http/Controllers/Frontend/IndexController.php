<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\SubCategory;
use App\Models\Course;
use App\Models\Instructor; // Changé de User à Instructor
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
     * Display the homepage with popular courses for the carousel.
     */
    public function index()
    {
        $popularCourses = Course::where('status', 1)
            ->orderBy('id', 'ASC')
            ->limit(6)
            ->get();

        return view('frontend.index', compact('popularCourses'));
    }

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
            return $course->reviews_count ?? 0; // À adapter si tu as une table de reviews
        });
        return view('instructor.instructor_details', compact('instructor', 'courses', 'totalStudents', 'totalReviews'));
    }

    /**
     * Add a course to the user's cart (stored in session).
     */
    public function addToCart(Request $request, $courseId)
    {
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Please log in to add courses to your cart.');
        }

        $course = Course::findOrFail($courseId);
        $cart = Session::get('cart', []);

        if (isset($cart[$courseId])) {
            return redirect()->route('cart.view')->with('info', 'This course is already in your cart.');
        }

        $finalPrice = $course->discount_price !== null 
            ? max(0, $course->selling_price - $course->discount_price) 
            : $course->selling_price;

        $cart[$courseId] = [
            'course_id' => $course->id,
            'course_name' => $course->course_name,
            'price' => $finalPrice,
            'original_price' => $course->selling_price,
            'instructor_id' => $course->instructor_id,
        ];

        Session::put('cart', $cart);

        return redirect()->route('cart.view')->with('success', 'Course added to cart successfully!');
    }

    /**
     * Remove a course from the user's cart.
     */
    public function removeFromCart($courseId)
    {
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Please log in to manage your cart.');
        }

        $cart = Session::get('cart', []);

        if (isset($cart[$courseId])) {
            unset($cart[$courseId]);
            Session::put('cart', $cart);
            return redirect()->route('cart.view')->with('success', 'Course removed from cart successfully!');
        }

        return redirect()->route('cart.view')->with('error', 'Course not found in cart.');
    }

    /**
     * Display the user's cart.
     */
    public function viewCart()
    {
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Please log in to view your cart.');
        }

        $cartItems = Session::get('cart', []);
        $subtotal = array_sum(array_column($cartItems, 'price'));
        $coupon = Session::get('coupon');
        $totalPrice = $coupon ? max(0, $subtotal - $coupon['discount']) : $subtotal;

        return view('User.mycart.view_mycart', compact('cartItems', 'subtotal', 'coupon', 'totalPrice'));
    }

    /**
     * Apply a single coupon to the cart.
     */
    public function applyCoupon(Request $request)
    {
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Please log in to apply a coupon.');
        }

        $request->validate([
            'coupon_name' => 'required|string|max:255',
        ]);

        $coupon = Coupon::where('coupon_name', strtoupper($request->coupon_name))
            ->where('coupon_validity', '>=', Carbon::today()->toDateString())
            ->first();

        if (!$coupon) {
            return redirect()->route('cart.view')->withErrors(['coupon_name' => 'Invalid or expired coupon code.']);
        }

        $cartItems = Session::get('cart', []);
        if (empty($cartItems)) {
            return redirect()->route('cart.view')->withErrors(['coupon_name' => 'Your cart is empty.']);
        }

        $applicableCourseIds = array_column($cartItems, 'course_id');
        if (!in_array($coupon->course_id, $applicableCourseIds)) {
            return redirect()->route('cart.view')->withErrors(['coupon_name' => 'This coupon is not applicable to any course in your cart.']);
        }

        if (Session::has('coupon')) {
            return redirect()->route('cart.view')->withErrors(['coupon_name' => 'A coupon is already applied. Only one coupon is allowed.']);
        }

        $subtotal = array_sum(array_column($cartItems, 'price'));
        $discount = ($subtotal * $coupon->coupon_discount) / 100;

        Session::put('coupon', [
            'name' => $coupon->coupon_name,
            'discount' => $discount,
            'coupon_id' => $coupon->id,
        ]);

        return redirect()->route('cart.view')->with('success', 'Coupon applied successfully!');
    }

    /**
     * Remove the coupon from the cart.
     */
    public function removeCoupon(Request $request)
    {
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Please log in to remove a coupon.');
        }

        Session::forget('coupon');
        return redirect()->route('cart.view')->with('success', 'Coupon removed successfully!');
    }

    /**
     * Display the checkout page.
     */
    public function cartCheckout()
    {
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Please log in to checkout.');
        }

        $cartItems = Session::get('cart', []);
        if (empty($cartItems)) {
            return redirect()->route('cart.view')->with('error', 'Your cart is empty.');
        }

        $subtotal = array_sum(array_column($cartItems, 'price'));
        $coupon = Session::get('coupon');
        $totalPrice = $coupon ? max(0, $subtotal - $coupon['discount']) : $subtotal;

        return view('frontend.cart.checkout', compact('cartItems', 'subtotal', 'coupon', 'totalPrice'));
    }

    /**
     * Process the checkout and create payment and orders.
     */
    public function checkout(Request $request)
    {
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Please log in to checkout.');
        }

        $cartItems = Session::get('cart', []);
        if (empty($cartItems)) {
            return redirect()->route('cart.view')->with('error', 'Your cart is empty.');
        }

        $user = Auth::user();
        $coupon = Session::get('coupon');
        $subtotal = array_sum(array_column($cartItems, 'price'));
        $discount = $coupon ? $coupon['discount'] : 0;
        $totalPrice = max(0, $subtotal - $discount);

        $request->validate([
            'phone' => 'required|string|max:15',
            'address' => 'required|string|max:255',
            'payment_type' => 'required|string|in:Manual,Stripe,Paypal',
        ]);

        $payment = Payment::create([
            'name' => $user->name,
            'email' => $user->email,
            'phone' => $request->input('phone'),
            'address' => $request->input('address'),
            'cash_delivery' => $request->input('cash_delivery', 'No'),
            'total_amount' => $totalPrice,
            'payment_type' => $request->input('payment_type'),
            'invoice_no' => 'INV-' . rand(1000, 9999),
            'order_date' => Carbon::today()->toDateString(),
            'order_month' => Carbon::today()->month,
            'order_year' => Carbon::today()->year,
            'status' => 'Pending',
        ]);

        foreach ($cartItems as $item) {
            Order::create([
                'payment_id' => $payment->id,
                'user_id' => $user->id,
                'course_id' => $item['course_id'],
                'instructor_id' => $item['instructor_id'],
                'course_title' => $item['course_name'],
                'price' => $item['price'],
            ]);
        }

        Session::forget('cart');
        Session::forget('coupon');

        return redirect()->route('home')->with('success', 'Order placed successfully! Payment is pending.');
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