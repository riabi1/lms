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
    public function CategoryCourse($id, $slug)
    {
        $courses = Course::where('category_id', $id)->where('status', '1')->get();
        $category = Category::where('id', $id)->first();
        $categories = Category::latest()->get();
        return view('frontend.category.category_all', compact('courses', 'category', 'categories'));
    }

    /**
     * Display all courses in a specific subcategory.
     */
    public function SubCategoryCourse($id, $slug)
    {
        $courses = Course::where('subcategory_id', $id)->where('status', '1')->get();
        $subcategory = SubCategory::where('id', $id)->first();
        $categories = Category::latest()->get();
        return view('frontend.category.subcategory_all', compact('courses', 'subcategory', 'categories'));
    }

    /**
     * Display the details of a specific instructor.
     */
    public function InstructorDetails($id)
    {
        $instructor = User::findOrFail($id);
        $courses = Course::where('instructor_id', $id)->get();
        return view('frontend.instructor.instructor_details', compact('instructor', 'courses'));
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
     * Apply a coupon to the cart.
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
    public function AllCourses()
    {
        $courses = Course::where('status', '1')->latest()->get();
        $categories = Category::latest()->get();
        return view('frontend.course.all_courses', compact('courses', 'categories'));
    }
}