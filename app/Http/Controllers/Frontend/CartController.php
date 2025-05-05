<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\CartItem;
use App\Models\Course;
use App\Models\Coupon;
use App\Models\Instructor;
use App\Models\Order;
use App\Models\Invoice;
use App\Notifications\OrderPlacedNotification;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class CartController extends Controller
{
    public function AddToCart(Request $request, $id)
    {
        $course = Course::with('courseable')->find($id);
        if (!$course) {
            return response()->json(['error' => 'Course not found'], 404);
        }

        $effectivePrice = $course->discount_price !== null && $course->discount_price > 0
            ? max(0, $course->selling_price - $course->discount_price)
            : $course->selling_price;

        $instructor = $course->courseable_type === 'App\Models\Instructor' && $course->courseable_id
            ? Instructor::find($course->courseable_id)
            : null;

        $options = [
            'course_name' => $course->course_name,
            'image' => $course->course_image,
            'instructor_id' => $instructor ? $instructor->id : null,
            'instructor_name' => $instructor ? $instructor->name : 'Unknown Instructor',
            'selling_price' => $course->selling_price ?? 0,
            'discount_price' => $course->discount_price ?? 0,
        ];

        if (Auth::check()) {
            $existingOrder = Order::where('user_id', Auth::id())
                ->where('course_id', $id)
                ->where('payment_status', 'paid')
                ->exists();

            if ($existingOrder) {
                return response()->json(['info' => 'You have already purchased this course. Start learning now!'], 200);
            }

            $existingCartItem = CartItem::where('user_id', Auth::id())
                ->where('cartable_type', 'App\Models\Course')
                ->where('cartable_id', $course->id)
                ->first();

            if ($existingCartItem) {
                $existingCartItem->quantity += 1;
                $existingCartItem->save();
            } else {
                CartItem::create([
                    'cartable_type' => 'App\Models\Course',
                    'cartable_id' => $course->id,
                    'user_id' => Auth::id(),
                    'price' => $effectivePrice,
                    'quantity' => 1,
                    'options' => $options,
                ]);
            }

            $cartItems = CartItem::where('user_id', Auth::id())->get();
            $cartCount = $cartItems->count();
            $cartSubTotal = $cartItems->sum(fn($item) => $item->price * $item->quantity);

            return response()->json([
                'success' => true,
                'message' => 'Course added to the cart successfully!',
                'cartCount' => $cartCount,
                'cartSubTotal' => number_format($cartSubTotal, 2),
            ], 200);
        } else {
            $tempCart = json_decode($request->cookie('tempCart', '[]'), true);
            $existingItemIndex = array_search($course->id, array_column($tempCart, 'courseId'));

            if ($existingItemIndex !== false) {
                $tempCart[$existingItemIndex]['quantity'] += 1;
            } else {
                $tempCart[] = [
                    'courseId' => $course->id,
                    'quantity' => 1,
                    'price' => $effectivePrice,
                    'course_name' => $course->course_name,
                    'image' => $course->course_image,
                    'instructor_id' => $instructor ? $instructor->id : null,
                    'instructor_name' => $instructor ? $instructor->name : 'Unknown Instructor',
                    'selling_price' => $course->selling_price ?? 0,
                    'discount_price' => $course->discount_price ?? 0,
                ];
            }

            $cartCount = count($tempCart);
            $cartSubTotal = array_sum(array_map(fn($item) => $item['price'] * $item['quantity'], $tempCart));

            return response()->json([
                'success' => true,
                'message' => 'Course added to cart successfully!',
                'cartCount' => $cartCount,
                'cartSubTotal' => number_format($cartSubTotal, 2),
            ])->withCookie(cookie('tempCart', json_encode($tempCart), 43200));
        }
    }

    public function syncTempCart(Request $request)
    {
        if (!Auth::check()) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $tempCart = json_decode($request->cookie('tempCart', '[]'), true);
        $response = ['success' => true, 'added' => 0];

        foreach ($tempCart as $item) {
            $course = Course::with('courseable')->find($item['courseId']);
            if (!$course) {
                continue;
            }

            $existingOrder = Order::where('user_id', Auth::id())
                ->where('course_id', $course->id)
                ->where('payment_status', 'paid')
                ->exists();
            if ($existingOrder) {
                continue;
            }

            $effectivePrice = $course->discount_price !== null && $course->discount_price > 0
                ? max(0, $course->selling_price - $course->discount_price)
                : $course->selling_price;

            $instructor = $course->courseable_type === 'App\Models\Instructor' && $course->courseable_id
                ? Instructor::find($course->courseable_id)
                : null;

            $options = [
                'course_name' => $item['course_name'] ?? $course->course_name,
                'image' => $item['image'] ?? $course->course_image,
                'instructor_id' => $item['instructor_id'] ?? ($instructor ? $instructor->id : null),
                'instructor_name' => $item['instructor_name'] ?? ($instructor ? $instructor->name : 'Unknown Instructor'),
                'selling_price' => $item['selling_price'] ?? ($course->selling_price ?? 0),
                'discount_price' => $item['discount_price'] ?? ($course->discount_price ?? 0),
            ];

            $existingCartItem = CartItem::where('user_id', Auth::id())
                ->where('cartable_type', 'App\Models\Course')
                ->where('cartable_id', $course->id)
                ->first();

            if ($existingCartItem) {
                $existingCartItem->quantity += $item['quantity'];
                $existingCartItem->save();
            } else {
                CartItem::create([
                    'cartable_type' => 'App\Models\Course',
                    'cartable_id' => $course->id,
                    'user_id' => Auth::id(),
                    'price' => $effectivePrice,
                    'quantity' => $item['quantity'],
                    'options' => $options,
                ]);
            }

            $response['added']++;
        }

        $cartItems = CartItem::where('user_id', Auth::id())->get();
        $cartCount = $cartItems->count();
        $cartSubTotal = $cartItems->sum(fn($item) => $item->price * $item->quantity);

        return response()->json([
            'success' => true,
            'message' => $response['added'] . ' course(s) added to cart',
            'cartCount' => $cartCount,
            'cartSubTotal' => number_format($cartSubTotal, 2),
        ])->withCookie(cookie('tempCart', json_encode([]), 43200));
    }

    public function MyCart()
    {
        if (!Auth::check()) {
            $tempCart = json_decode(request()->cookie('tempCart', '[]'), true);
            $cartItems = collect([]);
            $subtotal = 0;
            $couponDiscount = 0;
            $total = 0;
            $coupons = [];

            if (!empty($tempCart)) {
                $courseIds = array_column($tempCart, 'courseId');
                $courses = Course::with('courseable')
                    ->whereIn('id', $courseIds)
                    ->select('id', 'course_name', 'course_image', 'selling_price', 'discount_price')
                    ->get()
                    ->keyBy('id');

                $cartItems = collect($tempCart)->map(function ($item) use ($courses) {
                    $course = $courses->get($item['courseId']);
                    if (!$course) {
                        return null;
                    }
                    $instructor = $course->courseable_type === 'App\Models\Instructor' && $course->courseable_id
                        ? Instructor::find($course->courseable_id)
                        : null;
                    return (object) [
                        'id' => $item['courseId'],
                        'name' => $item['course_name'] ?? $course->course_name,
                        'price' => $item['price'] ?? (
                            $course->discount_price !== null && $course->discount_price > 0
                                ? max(0, $course->selling_price - $course->discount_price)
                                : $course->selling_price
                        ),
                        'quantity' => $item['quantity'] ?? 1,
                        'attributes' => [
                            'image' => $item['image'] ?? $course->course_image,
                            'instructor_id' => $item['instructor_id'] ?? ($instructor ? $instructor->id : null),
                            'instructor_name' => $item['instructor_name'] ?? ($instructor ? $instructor->name : 'Unknown Instructor'),
                            'selling_price' => $item['selling_price'] ?? ($course->selling_price ?? 0),
                            'discount_price' => $item['discount_price'] ?? ($course->discount_price ?? 0),
                        ],
                    ];
                })->filter()->values();

                $subtotal = $cartItems->sum(fn($item) => $item->price * $item->quantity);
                $coupons = session('coupons', []);
                $couponDiscount = array_sum(array_column($coupons, 'discount_amount'));
                $total = max(0, $subtotal - $couponDiscount);
            }

            return view('User.mycart.view_mycart', compact('cartItems', 'subtotal', 'couponDiscount', 'total', 'coupons'));
        }

        $cartItems = CartItem::with(['cartable' => function ($query) {
            $query->with('courseable')->select('id', 'course_name', 'course_image', 'selling_price', 'discount_price');
        }])
            ->where('user_id', Auth::id())
            ->where('cartable_type', 'App\Models\Course')
            ->get()
            ->map(function ($item) {
                $instructor = $item->cartable && $item->cartable->courseable_type === 'App\Models\Instructor' && $item->cartable->courseable_id
                    ? Instructor::find($item->cartable->courseable_id)
                    : null;
                return (object) [
                    'id' => $item->cartable_id,
                    'name' => $item->cartable ? $item->cartable->course_name : ($item->options['course_name'] ?? 'Unknown Course'),
                    'price' => $item->price,
                    'quantity' => $item->quantity,
                    'cartable' => $item->cartable,
                    'attributes' => [
                        'image' => $item->cartable ? $item->cartable->course_image : ($item->options['image'] ?? null),
                        'instructor_id' => $instructor ? $instructor->id : ($item->options['instructor_id'] ?? null),
                        'instructor_name' => $instructor ? $instructor->name : ($item->options['instructor_name'] ?? 'Unknown Instructor'),
                        'selling_price' => $item->cartable ? ($item->cartable->selling_price ?? 0) : ($item->options['selling_price'] ?? 0),
                        'discount_price' => $item->cartable ? ($item->cartable->discount_price ?? 0) : ($item->options['discount_price'] ?? 0),
                    ],
                ];
            });

        $subtotal = $cartItems->sum(fn($item) => $item->price * $item->quantity);
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
                'message' => 'Please log in to manage your cart.',
                'redirect' => route('login'),
            ], 401);
        }

        $cartItem = CartItem::where('user_id', Auth::id())
            ->where('cartable_id', $id)
            ->where('cartable_type', 'App\Models\Course')
            ->first();

        if (!$cartItem) {
            return response()->json([
                'success' => false,
                'message' => 'Item not found in cart',
            ], 404);
        }

        $cartItem->delete();

        $cartItems = CartItem::where('user_id', Auth::id())->get();
        $subtotal = $cartItems->sum(fn($item) => $item->price * $item->quantity);
        $coupons = session('coupons', []);
        $couponDiscount = 0;
        $updatedCoupons = [];

        if (!empty($coupons) && $cartItems->isNotEmpty()) {
            foreach ($coupons as $couponData) {
                $coupon = Coupon::where('code', $couponData['code'])->first();
                if ($coupon && $this->isCouponApplicable($coupon, $cartItems)) {
                    $discount = $this->calculateCouponDiscount($coupon, $cartItems);
                    $updatedCoupons[$coupon->code] = [
                        'code' => $coupon->code,
                        'discount_amount' => $discount,
                    ];
                    $couponDiscount += $discount;
                }
            }
            session(['coupons' => $updatedCoupons]);
        } else {
            session()->forget('coupons');
        }

        $totalPrice = max(0, $subtotal - $couponDiscount);

        return response()->json([
            'success' => true,
            'subtotal' => number_format($subtotal, 2),
            'totalPrice' => number_format($totalPrice, 2),
            'couponDiscount' => number_format($couponDiscount, 2),
            'cartCount' => $cartItems->count(),
            'message' => 'Item removed from cart!',
        ], 200);
    }

    private function isCouponApplicable(Coupon $coupon, $cartItems)
    {
        return $cartItems->contains(function ($item) use ($coupon) {
            $options = is_array($item->options) ? $item->options : (array) json_decode($item->options, true);
            return $coupon->couponable_type === 'App\Models\Course' &&
                   $coupon->couponable_id == $item->cartable_id &&
                   $coupon->uses < ($coupon->max_uses ?? PHP_INT_MAX) &&
                   $coupon->coupon_validity >= Carbon::today()->format('Y-m-d') &&
                   $coupon->status == 1;
        });
    }

    private function calculateCouponDiscount(Coupon $coupon, $cartItems)
    {
        $applicablePrice = $cartItems
            ->filter(function ($item) use ($coupon) {
                return $item->cartable_id == $coupon->couponable_id &&
                       $item->cartable_type == 'App\Models\Course';
            })
            ->sum(function ($item) {
                return $item->price * $item->quantity;
            });

        if ($coupon->discount_type === 'percentage') {
            return round($applicablePrice * ($coupon->coupon_discount / 100), 2);
        } else {
            return min($coupon->coupon_discount, $applicablePrice);
        }
    }

    public function CouponApply(Request $request)
    {
        $request->validate([
            'code' => 'required|string|max:255',
        ]);

        if (!Auth::check()) {
            return redirect()->route('cart')->with('error', 'Please log in to apply coupons.');
        }

        $coupon = Coupon::where('code', strtoupper($request->code))
            ->where('coupon_validity', '>=', Carbon::today()->format('Y-m-d'))
            ->where('status', 1)
            ->first();

        if (!$coupon) {
            return redirect()->route('cart')->with('error', 'Invalid or expired coupon.');
        }

        $cartItems = CartItem::where('user_id', Auth::id())->get();
        if ($cartItems->isEmpty()) {
            return redirect()->route('cart')->with('error', 'Cart is empty.');
        }

        if (!$this->isCouponApplicable($coupon, $cartItems)) {
            return redirect()->route('cart')->with('error', 'Coupon not applicable to any course in cart.');
        }

        $coupons = session('coupons', []);
        if (isset($coupons[$coupon->code])) {
            return redirect()->route('cart')->with('info', 'Coupon already applied.');
        }

        $discount = $this->calculateCouponDiscount($coupon, $cartItems);
        $coupons[$coupon->code] = [
            'code' => $coupon->code,
            'discount_amount' => $discount,
        ];
        session(['coupons' => $coupons]);

        // Increment coupon usage
        $coupon->increment('uses');

        return redirect()->route('cart')->with('success', 'Coupon applied successfully!');
    }

    public function CouponRemove($couponCode)
    {
        $coupons = session('coupons', []);
        if (isset($coupons[$couponCode])) {
            // Decrement coupon usage
            $coupon = Coupon::where('code', $couponCode)->first();
            if ($coupon && $coupon->uses > 0) {
                $coupon->decrement('uses');
            }

            unset($coupons[$couponCode]);
            session(['coupons' => $coupons]);
            return redirect()->route('cart')->with('success', 'Coupon removed successfully!');
        }

        return redirect()->route('cart')->with('info', 'No such coupon was applied.');
    }

    private function cleanUpCart()
    {
        CartItem::where('user_id', Auth::id())
            ->where('cartable_type', 'App\Models\Course')
            ->whereNotIn('cartable_id', Course::pluck('id'))
            ->delete();
    }

    public function CheckoutCreate()
    {
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Please log in.');
        }

        $this->cleanUpCart();

        $cartItems = CartItem::with(['cartable' => function ($query) {
            $query->with('courseable')->select('id', 'course_name', 'course_image', 'selling_price', 'discount_price');
        }])
            ->where('user_id', Auth::id())
            ->where('cartable_type', 'App\Models\Course')
            ->get()
            ->filter(function ($item) {
                return $item->cartable && Course::where('id', $item->cartable_id)->exists();
            })
            ->map(function ($item) {
                $instructor = $item->cartable->courseable_type === 'App\Models\Instructor' && $item->cartable->courseable_id
                    ? $item->cartable->courseable
                    : null;
                return (object) [
                    'id' => $item->cartable_id,
                    'name' => $item->cartable->course_name ?? ($item->options['course_name'] ?? 'Unknown Course'),
                    'price' => $item->price,
                    'quantity' => $item->quantity,
                    'cartable' => $item->cartable,
                    'attributes' => [
                        'image' => $item->cartable->course_image ?? ($item->options['image'] ?? null),
                        'instructor_id' => $instructor ? $instructor->id : ($item->options['instructor_id'] ?? null),
                        'instructor_name' => $instructor ? $instructor->name : ($item->options['instructor_name'] ?? 'Unknown Instructor'),
                        'selling_price' => $item->cartable->selling_price ?? ($item->options['selling_price'] ?? 0),
                        'discount_price' => $item->cartable->discount_price ?? ($item->options['discount_price'] ?? 0),
                    ],
                ];
            });

        if ($cartItems->isEmpty()) {
            return redirect('/')->with('error', 'Add at least one course.');
        }

        $subtotal = $cartItems->sum(fn($item) => $item->price * $item->quantity);
        $coupons = session('coupons', []);
        $couponDiscount = array_sum(array_column($coupons, 'discount_amount'));
        $total = max(0, $subtotal - $couponDiscount);

        $adjustedPrices = [];
        if ($couponDiscount > 0 && $subtotal > 0) {
            foreach ($cartItems as $item) {
                $proportion = ($item->price * $item->quantity) / $subtotal;
                $discountForItem = $couponDiscount * $proportion;
                $adjustedPrices[$item->id] = max(0, ($item->price * $item->quantity) - $discountForItem);
            }
        } else {
            foreach ($cartItems as $item) {
                $adjustedPrices[$item->id] = $item->price * $item->quantity;
            }
        }

        return view('User.checkout.checkout', compact('cartItems', 'subtotal', 'couponDiscount', 'total', 'coupons', 'adjustedPrices'));
    }

    public function processOrder($transactionId, $paymentMethod)
    {
        $cartItems = CartItem::where('user_id', Auth::id())->get();
        if ($cartItems->isEmpty()) {
            throw new \Exception('Cart is empty');
        }

        $existingOrder = Order::where('payment_id', $transactionId)->first();
        if ($existingOrder) {
            $invoice = Invoice::where('payment_id', $transactionId)->first();
            return $invoice->id;
        }

        $subtotal = $cartItems->sum(fn($item) => $item->price * $item->quantity);
        $coupons = session('coupons', []);
        $couponDiscount = array_sum(array_column($coupons, 'discount_amount'));
        $totalDiscount = $couponDiscount > 0 && $subtotal > 0 ? $couponDiscount : 0;
        $total = max(0, $subtotal - $totalDiscount);

        $orders = [];
        foreach ($cartItems as $item) {
            $options = is_array($item->options) ? $item->options : (array) json_decode($item->options, true);
            $itemDiscount = $totalDiscount > 0 ? (($item->price * $item->quantity) / $subtotal) * $totalDiscount : 0;

            $order = Order::create([
                'user_id' => Auth::id(),
                'course_id' => $item->cartable_id,
                'instructor_id' => $options['instructor_id'] ?? null,
                'course_title' => $options['course_name'] ?? 'Course',
                'price' => $item->price,
                'discount_amount' => round($itemDiscount, 2),
                'currency' => 'USD',
                'payment_status' => 'paid',
                'payment_id' => $transactionId,
                'payment_method' => $paymentMethod,
            ]);

            $instructor = Instructor::find($options['instructor_id'] ?? null);
            if ($instructor) {
                $instructor->notify(new OrderPlacedNotification($order));
            }

            $orders[] = [
                'course_title' => $options['course_name'] ?? 'Course',
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
            'items' => json_encode($orders),
        ]);

        CartItem::where('user_id', Auth::id())->delete();
        session()->forget('coupons');

        return $invoice->id;
    }
}