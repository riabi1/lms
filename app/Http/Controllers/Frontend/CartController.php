<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Course;
use App\Models\Coupon;
use App\Models\Order;
use App\Models\Instructor;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use App\Notifications\OrderPlacedNotification;
use Carbon\Carbon;
use Stripe\Charge;
use Stripe\Stripe;

class CartController extends Controller
{
    public function AddToCart(Request $request, $id)
    {
        $course = Course::with('courseable')->find($id);
        if (!$course) {
            return redirect()->back()->with('error', 'Course not found');
        }

        // Vérifier si l'utilisateur a déjà acheté ce cours
        if (Auth::check()) {
            $existingOrder = Order::where('user_id', Auth::id())
                ->where('course_id', $id)
                ->where('payment_status', 'paid')
                ->exists();

            if ($existingOrder) {
                return redirect()->back()->with('info', 'You have already purchased this course. Start learning now!');
            }
        }

        $cart = Session::get('cart', []);
        if (isset($cart[$id])) {
            return redirect()->back()->with('info', 'Course already in cart');
        }

        $effectivePrice = $course->discount_price !== null && $course->discount_price > 0 
            ? max(0, $course->selling_price - $course->discount_price) 
            : $course->selling_price;

        $instructor = $course->courseable_type === 'App\Models\Instructor' && $course->courseable_id
            ? Instructor::find($course->courseable_id)
            : null;

        $cart[$id] = [
            'id' => $course->id,
            'name' => $course->course_name,
            'instructor_name' => $instructor ? $instructor->name : 'Unknown Instructor',
            'selling_price' => $course->selling_price ?? 0,
            'discount_price' => $course->discount_price ?? 0,
            'price' => $effectivePrice,
            'image' => $course->course_image,
            'instructor_id' => $instructor ? $instructor->id : null,
        ];

        Session::put('cart', $cart);

        return redirect()->back()->with('success', 'Course added to the cart successfully!');
    }

    public function MyCart()
    {
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Please log in.');
        }

        $cart = Session::get('cart', []);
        $subtotal = array_sum(array_column($cart, 'price'));

        $coupons = Session::get('coupons', []);
        $couponDiscount = array_sum(array_column($coupons, 'discount_amount'));
        $total = max(0, $subtotal - $couponDiscount);

        return view('User.mycart.view_mycart', compact('cart', 'subtotal', 'couponDiscount', 'total', 'coupons'));
    }

    public function CartRemove($id)
    {
        if (!Auth::check()) {
            return response()->json([
                'success' => false,
                'message' => 'Please log in to manage your cart.'
            ], 401);
        }

        $cart = Session::get('cart', []);
        if (isset($cart[$id])) {
            unset($cart[$id]);
            Session::put('cart', $cart);

            $subtotal = array_sum(array_column($cart, 'price'));

            $coupons = Session::get('coupons', []);
            $totalPrice = $subtotal;
            $couponDiscount = 0;

            if (!empty($coupons) && !empty($cart)) {
                $updatedCoupons = [];
                foreach ($coupons as $couponData) {
                    $coupon = Coupon::where('coupon_name', $couponData['coupon_name'])->first();
                    if ($coupon && $this->isCouponApplicable($coupon, $cart)) {
                        $discount = round($subtotal * $coupon->coupon_discount / 100);
                        $updatedCoupons[$coupon->coupon_name] = [
                            'coupon_name' => $coupon->coupon_name,
                            'discount_amount' => $discount,
                        ];
                        $couponDiscount += $discount;
                    }
                }
                Session::put('coupons', $updatedCoupons);
                $totalPrice = max(0, $subtotal - $couponDiscount);
            } else {
                Session::forget('coupons');
            }

            return response()->json([
                'success' => true,
                'subtotal' => number_format($subtotal, 2),
                'totalPrice' => number_format($totalPrice, 2),
                'couponDiscount' => number_format($couponDiscount, 2),
                'cartCount' => count($cart),
                'message' => 'Item removed from cart!'
            ], 200);
        }

        return response()->json([
            'success' => false,
            'message' => 'Item not found in cart'
        ], 404);
    }

    private function isCouponApplicable($coupon, $cart)
    {
        foreach ($cart as $item) {
            if ($coupon->course_id == $item['id'] && $coupon->instructor_id == $item['instructor_id']) {
                return true;
            }
        }
        return false;
    }

    public function CouponApply(Request $request)
    {
        $request->validate([
            'coupon_name' => 'required|string|max:255',
        ]);

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

        if (!$this->isCouponApplicable($coupon, $cart)) {
            return redirect()->route('cart')->with('error', 'Coupon not applicable to any course in cart');
        }

        $coupons = Session::get('coupons', []);
        if (isset($coupons[$coupon->coupon_name])) {
            return redirect()->route('cart')->with('info', 'Coupon already applied');
        }

        $subtotal = array_sum(array_column($cart, 'price'));
        $discount = round($subtotal * $coupon->coupon_discount / 100);

        $coupons[$coupon->coupon_name] = [
            'coupon_name' => $coupon->coupon_name,
            'discount_amount' => $discount,
        ];
        Session::put('coupons', $coupons);

        return redirect()->route('cart')->with('success', 'Coupon applied!');
    }

    public function CouponRemove($couponName)
    {
        $coupons = Session::get('coupons', []);
        if (isset($coupons[$couponName])) {
            unset($coupons[$couponName]);
            Session::put('coupons', $coupons);
            return redirect()->route('cart')->with('success', 'Coupon removed!');
        }

        return redirect()->route('cart')->with('info', 'No such coupon was applied.');
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
        $coupons = Session::get('coupons', []);
        $couponDiscount = array_sum(array_column($coupons, 'discount_amount'));
        $total = max(0, $subtotal - $couponDiscount);

        $adjustedPrices = [];
        if ($couponDiscount > 0 && $subtotal > 0) {
            foreach ($cart as $id => $item) {
                $proportion = $item['price'] / $subtotal;
                $discountForItem = $couponDiscount * $proportion;
                $adjustedPrices[$id] = max(0, $item['price'] - $discountForItem);
            }
        } else {
            foreach ($cart as $id => $item) {
                $adjustedPrices[$id] = $item['price'];
            }
        }

        return view('User.checkout.checkout', compact('cart', 'subtotal', 'couponDiscount', 'total', 'coupons', 'adjustedPrices'));
    }

    public function CheckoutProcess(Request $request)
    {
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Please log in.');
        }

        $cart = Session::get('cart', []);
        if (empty($cart)) {
            return redirect('/')->with('error', 'Cart is empty');
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'address' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:20',
            'course_id' => 'required|array',
            'course_title' => 'required|array',
            'price' => 'required|array',
            'instructor_id' => 'required|array',
            'adjusted_price' => 'required|array',
            'total' => 'required|numeric|min:0',
            'stripeToken' => 'required',
        ]);

        // Vérifier la cohérence des données du panier
        foreach ($request->course_id as $index => $courseId) {
            if (!isset($cart[$courseId]) || 
                $cart[$courseId]['name'] !== $request->course_title[$index] || 
                $cart[$courseId]['instructor_id'] != $request->instructor_id[$index] ||
                abs($cart[$courseId]['price'] - $request->price[$index]) > 0.01) {
                return redirect()->back()->with('error', 'Cart data has been tampered with.');
            }
        }

        // Vérifier les cours déjà achetés
        $purchasedCourses = Order::where('user_id', Auth::id())
            ->where('payment_status', 'paid')
            ->pluck('course_id')
            ->toArray();

        foreach ($request->course_id as $courseId) {
            if (in_array($courseId, $purchasedCourses)) {
                return redirect()->back()->with('error', 'You have already purchased one or more courses in your cart.');
            }
        }

        $subtotal = array_sum(array_column($cart, 'price'));
        $coupons = Session::get('coupons', []);
        $couponDiscount = array_sum(array_column($coupons, 'discount_amount'));
        $total = max(0, $subtotal - $couponDiscount);

        if (abs($request->total - $total) > 0.01) {
            return redirect()->back()->with('error', 'Total amount mismatch.');
        }

        Stripe::setApiKey(env('STRIPE_SECRET'));

        try {
            $charge = Charge::create([
                'amount' => round($total * 100),
                'currency' => 'eur', 
                'source' => $request->stripeToken,
                'description' => 'Payment for multiple courses by ' . Auth::user()->name,
            ]);

            $paymentId = $charge->id;
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Payment failed: ' . $e->getMessage());
        }

        $ordersByInstructor = [];
        foreach ($request->course_id as $index => $courseId) {
            $order = new Order();
            $order->user_id = Auth::id();
            $order->course_id = $courseId;
            $order->instructor_id = $request->instructor_id[$index];
            $order->course_title = $request->course_title[$index];
            $order->price = $request->adjusted_price[$index];
            $order->payment_status = 'paid';
            $order->payment_id = $paymentId;
            $order->save();

            $instructorId = $request->instructor_id[$index];
            if (!isset($ordersByInstructor[$instructorId])) {
                $ordersByInstructor[$instructorId] = [];
            }
            $ordersByInstructor[$instructorId][] = $order;
        }

        foreach ($ordersByInstructor as $instructorId => $orders) {
            $instructor = Instructor::find($instructorId);
            if ($instructor) {
                foreach ($orders as $order) {
                    $instructor->notify(new OrderPlacedNotification($order));
                }
            }
        }

        Session::forget('cart');
        Session::forget('coupons');

        return redirect()->route('home')->with('success', 'Payment successful!');
    }
}