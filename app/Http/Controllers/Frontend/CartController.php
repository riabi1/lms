<?php
namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Course;
use App\Models\Coupon;
use App\Models\Order;
use App\Models\Instructor;
use Illuminate\Support\Facades\Auth;
use Darryldecode\Cart\Facades\CartFacade as Cart;
use App\Notifications\OrderPlacedNotification;
use App\Models\Invoice;

class CartController extends Controller
{
    public function AddToCart(Request $request, $id)
    {
        $course = Course::with('courseable')->find($id);
        if (!$course) {
            return redirect()->back()->with('error', 'Course not found');
        }

        if (Auth::check()) {
            $existingOrder = Order::where('user_id', Auth::id())
                ->where('course_id', $id)
                ->where('payment_status', 'paid')
                ->exists();

            if ($existingOrder) {
                return redirect()->back()->with('info', 'You have already purchased this course. Start learning now!');
            }
        }

        if (Cart::get($id)) {
            return redirect()->back()->with('info', 'Course already in cart');
        }

        $effectivePrice = $course->discount_price !== null && $course->discount_price > 0 
            ? max(0, $course->selling_price - $course->discount_price) 
            : $course->selling_price;

        $instructor = $course->courseable_type === 'App\Models\Instructor' && $course->courseable_id
            ? Instructor::find($course->courseable_id)
            : null;

        Cart::add([
            'id' => $course->id,
            'name' => $course->course_name,
            'price' => $effectivePrice,
            'quantity' => 1,
            'attributes' => [
                'instructor_name' => $instructor ? $instructor->name : 'Unknown Instructor',
                'selling_price' => $course->selling_price ?? 0,
                'discount_price' => $course->discount_price ?? 0,
                'image' => $course->course_image,
                'instructor_id' => $instructor ? $instructor->id : null,
            ]
        ]);

        return redirect()->back()->with('success', 'Course added to the cart successfully!');
    }

    public function MyCart()
    {
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Please log in.');
        }

        $cartItems = Cart::getContent();
        $subtotal = Cart::getSubTotal();
        $coupons = session('coupons', []);
        $couponDiscount = array_sum(array_column($coupons, 'discount_amount'));
        $total = max(0, $subtotal - $couponDiscount);

        return view('User.mycart.view_mycart', compact('cartItems', 'subtotal', 'couponDiscount', 'total', 'coupons'));
    }

    public function CartRemove($id)
    {
        if (!Auth::check()) {
            return response()->json([
                'success' => false,
                'message' => 'Please log in to manage your cart.'
            ], 401);
        }

        if (Cart::get($id)) {
            Cart::remove($id);

            $subtotal = Cart::getSubTotal();
            $coupons = session('coupons', []);
            $totalPrice = $subtotal;
            $couponDiscount = 0;

            if (!empty($coupons) && !Cart::isEmpty()) {
                $updatedCoupons = [];
                foreach ($coupons as $couponData) {
                    $coupon = Coupon::where('coupon_name', $couponData['coupon_name'])->first();
                    if ($coupon && $this->isCouponApplicable($coupon, Cart::getContent()->toArray())) {
                        $discount = round($subtotal * $coupon->coupon_discount / 100);
                        $updatedCoupons[$coupon->coupon_name] = [
                            'coupon_name' => $coupon->coupon_name,
                            'discount_amount' => $discount,
                        ];
                        $couponDiscount += $discount;
                    }
                }
                session(['coupons' => $updatedCoupons]);
                $totalPrice = max(0, $subtotal - $couponDiscount);
            } else {
                session()->forget('coupons');
            }

            return response()->json([
                'success' => true,
                'subtotal' => number_format($subtotal, 2),
                'totalPrice' => number_format($totalPrice, 2),
                'couponDiscount' => number_format($couponDiscount, 2),
                'cartCount' => Cart::getContent()->count(),
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
            if ($coupon->course_id == $item['id'] && $coupon->instructor_id == $item['attributes']['instructor_id']) {
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

        if (Cart::isEmpty()) {
            return redirect()->route('cart')->with('error', 'Cart is empty');
        }

        if (!$this->isCouponApplicable($coupon, Cart::getContent()->toArray())) {
            return redirect()->route('cart')->with('error', 'Coupon not applicable to any course in cart');
        }

        $coupons = session('coupons', []);
        if (isset($coupons[$coupon->coupon_name])) {
            return redirect()->route('cart')->with('info', 'Coupon already applied');
        }

        $subtotal = Cart::getSubTotal();
        $discount = round($subtotal * $coupon->coupon_discount / 100);

        $coupons[$coupon->coupon_name] = [
            'coupon_name' => $coupon->coupon_name,
            'discount_amount' => $discount,
        ];
        session(['coupons' => $coupons]);

        return redirect()->route('cart')->with('success', 'Coupon applied!');
    }

    public function CouponRemove($couponName)
    {
        $coupons = session('coupons', []);
        if (isset($coupons[$couponName])) {
            unset($coupons[$couponName]);
            session(['coupons' => $coupons]);
            return redirect()->route('cart')->with('success', 'Coupon removed!');
        }

        return redirect()->route('cart')->with('info', 'No such coupon was applied.');
    }

    public function CheckoutCreate()
    {
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Please log in.');
        }

        if (Cart::isEmpty()) {
            return redirect('/')->with('error', 'Add at least one course');
        }

        $cartItems = Cart::getContent();
        $subtotal = Cart::getSubTotal();
        $coupons = session('coupons', []);
        $couponDiscount = array_sum(array_column($coupons, 'discount_amount'));
        $total = max(0, $subtotal - $couponDiscount);

        $adjustedPrices = [];
        if ($couponDiscount > 0 && $subtotal > 0) {
            foreach ($cartItems as $item) {
                $proportion = $item->price / $subtotal;
                $discountForItem = $couponDiscount * $proportion;
                $adjustedPrices[$item->id] = max(0, $item->price - $discountForItem);
            }
        } else {
            foreach ($cartItems as $item) {
                $adjustedPrices[$item->id] = $item->price;
            }
        }

        return view('User.checkout.checkout', compact('cartItems', 'subtotal', 'couponDiscount', 'total', 'coupons', 'adjustedPrices'));
    }

  public function processOrder($transactionId, $paymentMethod)
{
    $cartItems = Cart::getContent();
    $subtotal = Cart::getSubTotal();
    $coupons = session('coupons', []);
    $couponDiscount = array_sum(array_column($coupons, 'discount_amount'));
    $totalDiscount = $couponDiscount > 0 && $subtotal > 0 ? $couponDiscount : 0;
    $total = max(0, $subtotal - $totalDiscount);

    $orders = [];
    foreach ($cartItems as $item) {
        $itemDiscount = $totalDiscount > 0 ? ($item->price / $subtotal) * $totalDiscount : 0;
        $order = Order::create([
            'user_id' => Auth::id(),
            'course_id' => $item->id,
            'instructor_id' => $item->attributes['instructor_id'],
            'course_title' => $item->name,
            'price' => $item->price,
            'discount_amount' => round($itemDiscount, 2),
            'currency' => 'USD',
            'payment_status' => 'paid',
            'payment_id' => $transactionId,
            'payment_method' => $paymentMethod,
        ]);

        $instructor = Instructor::find($item->attributes['instructor_id']);
        if ($instructor) {
            $instructor->notify(new OrderPlacedNotification($order));
        }

        $orders[] = [
            'course_title' => $item->name,
            'price' => $item->price,
            'discount' => round($itemDiscount, 2),
        ];
    }

    $invoiceNumber = 'INV-' . strtoupper(uniqid());
    $invoice = Invoice::create([
        'user_id' => Auth::id(),
        'invoice_number' => $invoiceNumber,
        'subtotal' => $subtotal,
        'discount' => $totalDiscount,
        'total' => $total,
        'payment_method' => $paymentMethod,
        'payment_id' => $transactionId,
        'items' => json_encode($orders), // Assurez-vous que c'est bien "items"
    ]);

    Cart::clear();
    session()->forget('coupons');

    return $invoice->id;
}
}