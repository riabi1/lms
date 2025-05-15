<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\CartItem;
use App\Models\Course;
use App\Models\Coupon;
use App\Models\Instructor;
use App\Models\Invoice;
use App\Models\Order;
use App\Notifications\OrderPlacedNotification;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\View\View;

class CartController extends Controller
{
    /**
     * Add a course to the cart.
     *
     * @param Request $request
     * @param int $id
     * @return JsonResponse|RedirectResponse
     */
    public function addToCart(Request $request, int $id): JsonResponse|RedirectResponse
    {
        try {
            $course = Course::with('courseable')->findOrFail($id);
            $quantity = max(1, $request->input('quantity', 1));

            if (!Auth::check()) {
                return $this->handleGuestCart($request, $course, $quantity);
            }

            // Check if the user already purchased the course
            if ($this->userHasPurchasedCourse($id)) {
                return $this->respond($request, ['message' => 'You have already purchased this course. Start learning now!'], 400, 'info');
            }

            // Check if the course is already in the cart
            if ($this->isCourseInCart($id)) {
                return $this->respond($request, ['message' => 'Course already in cart'], 400, 'info');
            }

            $cartItem = $this->createCartItem($course, $quantity);
            $subtotal = CartItem::where('user_id', Auth::id())->sum(DB::raw('price * quantity'));

            return $this->respond($request, [
                'message' => 'Course added to cart successfully!',
                'cartCount' => CartItem::where('user_id', Auth::id())->count(),
                'cartSubTotal' => number_format($subtotal, 2),
            ], 200, 'success');
        } catch (\Exception $e) {
            return $this->respond($request, ['message' => 'An error occurred while adding to cart'], 500, 'error');
        }
    }

    /**
     * Display the user's cart.
     *
     * @return View|RedirectResponse
     */
    public function myCart(): View|RedirectResponse
    {
        $cartItems = collect();
        $subtotal = 0;
        $coupons = session('coupons', []);
        $couponDiscount = array_sum(array_column($coupons, 'discount_amount'));
        $total = 0;

        if (!Auth::check()) {
            $cartItems = collect(Session::get('guest_cart', []))->map(function ($item, $key) {
                return (object) [
                    'id' => $key,
                    'cartable_id' => $item['cartable_id'],
                    'cartable_type' => $item['cartable_type'],
                    'quantity' => $item['quantity'],
                    'price' => $item['price'],
                    'options' => $item['options'],
                ];
            });
        } else {
            $cartItems = CartItem::where('user_id', Auth::id())->get();
        }

        $subtotal = $cartItems->sum(fn($item) => $item->price * $item->quantity);
        $total = max(0, $subtotal - $couponDiscount);

        return view('User.mycart.view_mycart', compact('cartItems', 'subtotal', 'couponDiscount', 'total', 'coupons'));
    }

    /**
     * Get cart items for authenticated or guest users.
     *
     * @return JsonResponse
     */
    public function getCartItems(): JsonResponse
    {
        $cartItems = [];
        $cartCount = 0;
        $cartSubTotal = 0;

        if (Auth::check()) {
            $cartItems = CartItem::where('user_id', Auth::id())
                ->with(['cartable' => fn($query) => $query->select('id', 'course_name', 'course_image')])
                ->get()
                ->map(fn($item) => $this->formatCartItem($item))
                ->toArray();
        } else {
            $guestCart = session('guest_cart', []);
            $cartItems = collect($guestCart)->map(function ($item, $courseId) {
                $course = Course::select('id', 'course_name', 'course_image')->find($courseId);
                return $course ? $this->formatGuestCartItem($item, $course) : null;
            })->filter()->toArray();
        }

        $cartCount = count($cartItems);
        $cartSubTotal = array_sum(array_column($cartItems, 'total'));

        return response()->json([
            'cartItems' => $cartItems,
            'cartCount' => $cartCount,
            'cartSubTotal' => number_format($cartSubTotal, 2),
        ]);
    }

    /**
     * Remove a course from the cart.
     *
     * @param Request $request
     * @param int $id
     * @return JsonResponse
     */
    public function cartRemove(Request $request, int $id): JsonResponse
    {
        try {
            if (!Auth::check()) {
                $cartItems = Session::get('guest_cart', []);
                if (!isset($cartItems[$id])) {
                    return response()->json(['message' => 'Course not found in cart'], 404);
                }

                unset($cartItems[$id]);
                Session::put('guest_cart', $cartItems);
                $subtotal = collect($cartItems)->sum(fn($item) => $item['price'] * $item['quantity']);

                return response()->json([
                    'message' => 'Course removed from cart successfully!',
                    'cartCount' => count($cartItems),
                    'cartSubTotal' => number_format($subtotal, 2),
                ]);
            }

            $cartItem = CartItem::where('id', $id)->where('user_id', Auth::id())->firstOrFail();
            $cartItem->delete();
            $subtotal = CartItem::where('user_id', Auth::id())->sum(DB::raw('price * quantity'));

            return response()->json([
                'message' => 'Course removed from cart successfully!',
                'cartCount' => CartItem::where('user_id', Auth::id())->count(),
                'cartSubTotal' => number_format($subtotal, 2),
            ]);
        } catch (\Exception $e) {
            return response()->json(['message' => 'An error occurred while removing the course'], 500);
        }
    }

    /**
     * Apply a coupon to the cart.
     *
     * @param Request $request
     * @return RedirectResponse
     */
    public function couponApply(Request $request): RedirectResponse
    {
        $request->validate(['coupon_name' => 'required|string|max:255']);

        try {
            $coupon = Coupon::where('coupon_name', $request->coupon_name)
                ->where('coupon_validity', '>=', Carbon::now()->format('Y-m-d'))
                ->where('status', 1)
                ->first();

            if (!$coupon) {
                return redirect()->route('cart')->with('error', 'Invalid or expired coupon');
            }

            $cartItems = Auth::check()
                ? CartItem::where('user_id', Auth::id())->get()
                : collect(Session::get('guest_cart', []))->map(fn($item, $key) => (object) $item);

            if ($cartItems->isEmpty()) {
                return redirect()->route('cart')->with('error', 'Cart is empty');
            }

            $isApplicable = $cartItems->contains(fn($item) => $coupon->course_id == $item->cartable_id && $coupon->instructor_id == ($item->options['instructor_id'] ?? null));
            if (!$isApplicable) {
                return redirect()->route('cart')->with('error', 'Coupon not applicable to any course in cart');
            }

            $subtotal = $cartItems->sum(fn($item) => $item->price * $item->quantity);
            $coupons = session('coupons', []);
            if (isset($coupons[$coupon->coupon_name])) {
                return redirect()->route('cart')->with('info', 'Coupon already applied');
            }

            $discount = round($subtotal * $coupon->coupon_discount / 100);
            $coupons[$coupon->coupon_name] = [
                'coupon_name' => $coupon->coupon_name,
                'discount_amount' => $discount,
            ];
            session(['coupons' => $coupons]);

            return redirect()->route('cart')->with('success', 'Coupon applied!');
        } catch (\Exception $e) {
            return redirect()->route('cart')->with('error', 'An error occurred while applying the coupon');
        }
    }

    /**
     * Remove a coupon from the cart.
     *
     * @param string $couponName
     * @return RedirectResponse
     */
    public function couponRemove(string $couponName): RedirectResponse
    {
        $coupons = session('coupons', []);
        if (isset($coupons[$couponName])) {
            unset($coupons[$couponName]);
            session(['coupons' => $coupons]);
            return redirect()->route('cart')->with('success', 'Coupon removed!');
        }

        return redirect()->route('cart')->with('info', 'No such coupon was applied.');
    }

    /**
     * Display the checkout page.
     *
     * @return View|RedirectResponse
     */
    public function checkoutCreate(): View|RedirectResponse
    {
        if (!Auth::check()) {
            return redirect()->route('login', ['redirect' => route('checkout')])->with('error', 'Please log in to checkout.');
        }
    
        $cartItems = CartItem::where('user_id', Auth::id())->get();
        if ($cartItems->isEmpty()) {
            return redirect('/')->with('error', 'Add at least one course');
        }
    
        $subtotal = $cartItems->sum(fn($item) => $item->price * $item->quantity);
        $coupons = session('coupons', []);
        $couponDiscount = array_sum(array_column($coupons, 'discount_amount'));
        $total = max(0, $subtotal - $couponDiscount);
    
        $adjustedPrices = $cartItems->mapWithKeys(function ($item) use ($couponDiscount, $subtotal) {
            $proportion = $couponDiscount > 0 && $subtotal > 0 ? ($item->price * $item->quantity) / $subtotal : 0;
            return [$item->cartable_id => max(0, ($item->price * $item->quantity) - ($couponDiscount * $proportion))];
        })->toArray();
    
        return view('User.checkout.checkout', compact('cartItems', 'subtotal', 'couponDiscount', 'total', 'coupons', 'adjustedPrices'));
    }

    /**
     * Process the order and create an invoice.
     *
     * @param string $transactionId
     * @param string $paymentMethod
     * @return int
     * @throws \Exception
     */
    public function processOrder(string $transactionId, string $paymentMethod): int
    {
        return DB::transaction(function () use ($transactionId, $paymentMethod) {
            $cartItems = CartItem::where('user_id', Auth::id())->get();
            if ($cartItems->isEmpty()) {
                throw new \Exception('Cart is empty');
            }

            $subtotal = $cartItems->sum(fn($item) => $item->price * $item->quantity);
            $coupons = session('coupons', []);
            $couponDiscount = array_sum(array_column($coupons, 'discount_amount'));
            $total = max(0, $subtotal - $couponDiscount);

            $orders = $cartItems->map(function ($item) use ($subtotal, $couponDiscount) {
                $itemSubtotal = $item->price * $item->quantity;
                $itemDiscount = $couponDiscount > 0 && $subtotal > 0 ? ($itemSubtotal / $subtotal) * $couponDiscount : 0;

                $order = Order::create([
                    'user_id' => Auth::id(),
                    'course_id' => $item->cartable_id,
                    'instructor_id' => $item->options['instructor_id'],
                    'course_title' => $item->options['name'],
                    'price' => $itemSubtotal,
                    'discount_amount' => round($itemDiscount, 2),
                    'currency' => 'USD',
                    'payment_status' => 'paid',
                    'payment_id' => $transactionId,
                    'payment_method' => $paymentMethod,
                ]);

                if ($item->options['instructor_id']) {
                    Instructor::find($item->options['instructor_id'])?->notify(new OrderPlacedNotification($order));
                }

                return [
                    'course_title' => $item->options['name'],
                    'price' => $itemSubtotal,
                    'discount' => round($itemDiscount, 2),
                ];
            })->toArray();

            $invoice = Invoice::create([
                'user_id' => Auth::id(),
                'invoice_number' => 'INV-' . strtoupper(uniqid()),
                'subtotal' => $subtotal,
                'discount' => $couponDiscount,
                'total' => $total,
                'payment_method' => $paymentMethod,
                'payment_id' => $transactionId,
                'items' => json_encode($orders),
            ]);

            CartItem::where('user_id', Auth::id())->delete();
            session()->forget('coupons');

            return $invoice->id;
        });
    }

    /**
     * Merge guest cart with authenticated user's cart after login.
     *
     * @return void
     */
    public function mergeGuestCart(): void
    {
        if (!Auth::check() || !Session::has('guest_cart')) {
            return;
        }

        $guestCart = Session::get('guest_cart', []);
        foreach ($guestCart as $courseId => $item) {
            if (!$this->isValidGuestCartItem($item)) {
                continue;
            }

            // Check if user has already purchased the course
            if ($this->userHasPurchasedCourse($item['cartable_id'])) {
                continue;
            }

            // Check if course is already in the user's cart
            $existingItem = CartItem::where('user_id', Auth::id())
                ->where('cartable_id', $item['cartable_id'])
                ->where('cartable_type', $item['cartable_type'])
                ->first();

            if (!$existingItem) {
                CartItem::create([
                    'cartable_id' => $item['cartable_id'],
                    'cartable_type' => $item['cartable_type'],
                    'user_id' => Auth::id(),
                    'quantity' => $item['quantity'],
                    'price' => $item['price'],
                    'options' => $item['options'],
                ]);
            }
        }

        // Preserve guest coupons
        $guestCoupons = Session::get('coupons', []);
        if (!empty($guestCoupons)) {
            Session::put('coupons', $guestCoupons);
        }

        Session::forget('guest_cart');
    }

    /**
     * Handle guest cart addition.
     *
     * @param Request $request
     * @param Course $course
     * @param int $quantity
     * @return JsonResponse|RedirectResponse
     */
    private function handleGuestCart(Request $request, Course $course, int $quantity): JsonResponse|RedirectResponse
    {
        $cartItems = Session::get('guest_cart', []);
        if (isset($cartItems[$course->id])) {
            return $this->respond($request, ['message' => 'Course already in cart'], 400, 'info');
        }

        $cartItems[$course->id] = $this->createCartItemArray($course, $quantity);
        Session::put('guest_cart', $cartItems);
        $subtotal = collect($cartItems)->sum(fn($item) => $item['price'] * $item['quantity']);

        return $this->respond($request, [
            'message' => 'Course added to cart. Please log in to proceed.',
            'cartCount' => count($cartItems),
            'cartSubTotal' => number_format($subtotal, 2),
            'redirect' => route('login', ['redirect' => route('cart')]),
        ], 200, 'success');
    }

    /**
     * Check if the user has already purchased the course.
     *
     * @param int $courseId
     * @return bool
     */
    private function userHasPurchasedCourse(int $courseId): bool
    {
        return Order::where('user_id', Auth::id())
            ->where('course_id', $courseId)
            ->where('payment_status', 'paid')
            ->exists();
    }

    /**
     * Check if the course is already in the cart.
     *
     * @param int $courseId
     * @return bool
     */
    private function isCourseInCart(int $courseId): bool
    {
        return CartItem::where('user_id', Auth::id())
            ->where('cartable_id', $courseId)
            ->where('cartable_type', Course::class)
            ->exists();
    }

    /**
     * Create a cart item for the authenticated user.
     *
     * @param Course $course
     * @param int $quantity
     * @return CartItem
     */
    private function createCartItem(Course $course, int $quantity): CartItem
    {
        return CartItem::create($this->createCartItemArray($course, $quantity));
    }

    /**
     * Create an array representation of a cart item.
     *
     * @param Course $course
     * @param int $quantity
     * @return array
     */
    private function createCartItemArray(Course $course, int $quantity): array
    {
        $effectivePrice = $course->discount_price && $course->discount_price > 0
            ? max(0, $course->selling_price - $course->discount_price)
            : $course->selling_price;

        $instructor = $course->courseable_type === Instructor::class && $course->courseable_id
            ? Instructor::find($course->courseable_id)
            : null;

        return [
            'cartable_id' => $course->id,
            'cartable_type' => Course::class,
            'user_id' => Auth::id(),
            'quantity' => $quantity,
            'price' => $effectivePrice,
            'options' => [
                'name' => $course->course_name,
                'instructor_name' => $instructor?->name ?? 'Unknown Instructor',
                'instructor_id' => $instructor?->id,
                'image' => $course->course_image,
                'selling_price' => $course->selling_price ?? 0,
                'discount_price' => $course->discount_price ?? 0,
            ],
        ];
    }

    /**
     * Format a cart item for JSON response.
     *
     * @param CartItem $item
     * @return array
     */
    private function formatCartItem(CartItem $item): array
    {
        return [
            'id' => $item->id,
            'course_id' => $item->cartable_id,
            'name' => $item->options['name'] ?? $item->cartable->course_name,
            'image' => $item->cartable->course_image
                ? asset('upload/course_images/thumbnail/' . $item->cartable->course_image)
                : asset('images/default-course.jpg'),
            'price' => $item->price,
            'quantity' => $item->quantity,
            'total' => $item->price * $item->quantity,
        ];
    }

    /**
     * Format a guest cart item for JSON response.
     *
     * @param array $item
     * @param Course $course
     * @return array
     */
    private function formatGuestCartItem(array $item, Course $course): array
    {
        return [
            'id' => $course->id,
            'course_id' => $course->id,
            'name' => $item['options']['name'] ?? $course->course_name,
            'image' => $course->course_image
                ? asset('upload/course_images/thumbnail/' . $course->course_image)
                : asset('images/default-course.jpg'),
            'price' => $item['price'],
            'quantity' => $item['quantity'],
            'total' => $item['price'] * $item['quantity'],
        ];
    }

    /**
     * Validate guest cart item.
     *
     * @param array $item
     * @return bool
     */
    private function isValidGuestCartItem(array $item): bool
    {
        return isset($item['cartable_id'], $item['cartable_type'], $item['quantity'], $item['price'], $item['options'])
            && $item['cartable_type'] === Course::class
            && is_numeric($item['quantity'])
            && is_numeric($item['price']);
    }

    /**
     * Helper method to handle AJAX and non-AJAX responses.
     *
     * @param Request $request
     * @param array $data
     * @param int $status
     * @param string $messageType
     * @param string|null $redirect
     * @return JsonResponse|RedirectResponse
     */
    private function respond(Request $request, array $data, int $status, string $messageType, ?string $redirect = null): JsonResponse|RedirectResponse
    {
        if ($request->expectsJson()) {
            return response()->json($data, $status);
        }

        return $redirect
            ? redirect()->to($redirect)->with($messageType, $data['message'])
            : redirect()->back()->with($messageType, $data['message']);
    }
}