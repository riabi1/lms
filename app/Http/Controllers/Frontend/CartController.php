<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Course;
use App\Models\Coupon;
use App\Models\Order;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Carbon\Carbon;
use Stripe\Charge;
use Stripe\Stripe;

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
        return redirect()->back()->with('info', 'Course already in cart');
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

    // Redirige vers la page précédente (détails du cours) avec un message flash
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
        $total = $subtotal - $couponDiscount;

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

        // Recalcule le sous-total après suppression
        $subtotal = array_sum(array_column($cart, 'price'));

        // Gère les coupons si nécessaire
        $coupons = Session::get('coupons', []);
        $totalPrice = $subtotal;
        $couponDiscount = 0;
        if (!empty($coupons)) {
            $newSubtotal = $subtotal;
            $updatedCoupons = [];
            foreach ($coupons as $couponData) {
                $coupon = Coupon::where('coupon_name', $couponData['coupon_name'])->first();
                if ($coupon) {
                    $discount = round($newSubtotal * $coupon->coupon_discount / 100);
                    $updatedCoupons[$coupon->coupon_name] = [
                        'coupon_name' => $coupon->coupon_name,
                        'discount_amount' => $discount,
                    ];
                    $couponDiscount += $discount;
                    $totalPrice = max(0, $newSubtotal - $couponDiscount);
                }
            }
            Session::put('coupons', $updatedCoupons);
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
        $total = $subtotal - $couponDiscount;

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
        'total' => 'required|numeric',
        'stripeToken' => 'required',
    ]);

    $subtotal = array_sum(array_column($cart, 'price'));
    $coupons = Session::get('coupons', []);
    $couponDiscount = array_sum(array_column($coupons, 'discount_amount'));
    $total = $subtotal - $couponDiscount;

    if (abs($request->total - $total) > 0.01) {
        return redirect()->back()->with('error', 'Total amount mismatch.');
    }

    Stripe::setApiKey(env('STRIPE_SECRET')); // Clé secrète Stripe

    try {
        $charge = Charge::create([
            'amount' => $total * 100, // Montant en centimes
            'currency' => 'usd',
            'source' => $request->stripeToken, // Token généré par Stripe.js
            'description' => 'Payment for multiple courses by ' . Auth::user()->name,
        ]);

        $paymentId = $charge->id; // ID de la transaction Stripe
    } catch (\Exception $e) {
        return redirect()->back()->with('error', 'Payment failed: ' . $e->getMessage());
    }

    foreach ($request->course_id as $index => $courseId) {
        $order = new Order();
        $order->user_id = Auth::id();
        $order->course_id = $courseId;
        $order->instructor_id = $request->instructor_id[$index];
        $order->course_title = $request->course_title[$index];
        $order->price = $request->adjusted_price[$index];
        $order->payment_status = 'paid';
        $order->payment_id = $paymentId; // Stocke l'ID Stripe dans la base
        $order->save();
    }

    Session::forget('cart');
    Session::forget('coupons');

    return redirect()->route('home')->with('success', 'Payment successful!');
}
}