<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Course;
use App\Models\Coupon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Carbon\Carbon;

class CartController extends Controller
{
public function AddToCart(Request $request, $id)
    {
        $course = Course::with('instructor')->find($id);
        if (!$course) {
            return redirect()->back()->with('error', 'Course not found');
        }

        $cart = Session::get('cart', []);
        if (isset($cart[$id])) {
            return redirect()->route('cart')->with('info', 'Course already in cart');
        }

        $effectivePrice = $course->discount_price !== null && $course->discount_price > 0 
            ? ($course->selling_price - $course->discount_price) 
            : $course->selling_price;

        $cart[$id] = [
            'id' => $course->id,
            'name' => $course->course_name,
            'instructor_name' => $course->instructor ? ($course->instructor->name ?? 'Unknown Instructor') : 'Unknown Instructor',
            'selling_price' => $course->selling_price ?? 0,
            'discount_price' => $course->discount_price ?? 0,
            'price' => $effectivePrice,
            'image' => $course->course_image,
            'instructor_id' => $course->instructor_id,
        ];

        Session::put('cart', $cart);
        return redirect()->route('cart')->with('success', 'Course added to cart!');
    }

    public function MyCart()
    {
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Please log in.');
        }

        $cart = Session::get('cart', []);
        $subtotal = array_sum(array_column($cart, 'price')); // Sum of effective prices

        $couponDiscount = 0;
        if (Session::has('coupon')) {
            $coupon = Session::get('coupon');
            $couponDiscount = $coupon['discount_amount'];
        }

        $total = $subtotal - $couponDiscount;

        return view('User.mycart.view_mycart', compact('cart', 'subtotal', 'couponDiscount', 'total'));
    }

    public function CartRemove($id)
    {
        $cart = Session::get('cart', []);
        if (isset($cart[$id])) {
            unset($cart[$id]);
            Session::put('cart', $cart);

            if (Session::has('coupon')) {
                $coupon = Coupon::where('coupon_name', Session::get('coupon')['coupon_name'])->first();
                if ($coupon) {
                    $newSubtotal = array_sum(array_column($cart, 'price'));
                    Session::put('coupon', [
                        'coupon_name' => $coupon->coupon_name,
                        'discount_amount' => round($newSubtotal * $coupon->coupon_discount / 100),
                    ]);
                }
            }
        }

        return redirect()->route('cart')->with('success', 'Course removed!');
    }

    public function CouponApply(Request $request)
    {
        $coupon = Coupon::where('coupon_name', $request->coupon_name)
            ->where('coupon_validity', '>=', Carbon::now()->format('Y-m-d'))
            ->where('status', 1)
            ->first();

        if (!$coupon) {
            return redirect()->route('cart')->with('error', 'Invalid or expired coupon');
        }

        $cart = Session::get('cart', []);
        if (empty($cart)) {
            return redirect()->route('cart')->with('error', 'Cart is empty');
        }

        $applicable = false;
        foreach ($cart as $item) {
            if ($coupon->course_id == $item['id'] && $coupon->instructor_id == $item['instructor_id']) {
                $applicable = true;
                break;
            }
        }
        if (!$applicable) {
            return redirect()->route('cart')->with('error', 'Coupon not applicable to any course in cart');
        }

        $subtotal = array_sum(array_column($cart, 'price'));
        $discount = round($subtotal * $coupon->coupon_discount / 100);

        Session::put('coupon', [
            'coupon_name' => $coupon->coupon_name,
            'discount_amount' => $discount,
        ]);

        return redirect()->route('cart')->with('success', 'Coupon applied!');
    }

    public function CheckoutCreate()
    {
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Please log in.');
        }

        $cart = Session::get('cart', []);
        if (empty($cart)) {
            return redirect('/')->with('error', 'Add at least one course');
        }

        $subtotal = array_sum(array_column($cart, 'price'));
        $couponDiscount = Session::has('coupon') ? Session::get('coupon')['discount_amount'] : 0;
        $total = $subtotal - $couponDiscount;

        return view('User.checkout.checkout_view', compact('cart', 'subtotal', 'couponDiscount', 'total'));
    }
}