<?php
namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Course;
use App\Models\Coupon;
use App\Models\Order;
use App\Models\Instructor;
use App\Models\CartItem;
use Illuminate\Support\Facades\Auth;
use App\Notifications\OrderPlacedNotification;
use App\Models\Invoice;
use Carbon\Carbon;

class CartController extends Controller
{
  public function AddToCart(Request $request, $id)
  {
      $course = Course::with('courseable')->find($id);
      if (!$course) {
          return response()->json(['error' => 'Course not found'], 404);
      }
  
      if (!Auth::check()) {
          return response()->json([
              'redirect' => route('login') . '?redirect=' . urlencode(route('cart')) . '&course_id=' . $course->id,
              'message' => 'Please log in to add this course to your cart.'
          ], 401);
      }
  
      $existingOrder = Order::where('user_id', Auth::id())
          ->where('course_id', $id)
          ->where('payment_status', 'paid')
          ->exists();
  
      if ($existingOrder) {
          return response()->json(['info' => 'You have already purchased this course. Start learning now!'], 200);
      }
  
      $existingCartItem = CartItem::where('user_id', Auth::id())
          ->where('cartable_type', Course::class)
          ->where('cartable_id', $course->id)
          ->exists();
  
      if ($existingCartItem) {
          return response()->json(['info' => 'Course is already in your cart.'], 200);
      }
  
      $effectivePrice = $course->discount_price !== null && $course->discount_price > 0 
          ? max(0, $course->selling_price - $course->discount_price) 
          : $course->selling_price;
  
      $instructor = $course->courseable_type === 'App\Models\Instructor' && $course->courseable_id
          ? Instructor::find($course->courseable_id)
          : null;
  
      CartItem::create([
          'cartable_type' => Course::class,
          'cartable_id' => $course->id,
          'user_id' => Auth::id(),
          'price' => $effectivePrice,
          'options' => [
              'instructor_name' => $instructor ? $instructor->name : 'Unknown Instructor',
              'selling_price' => $course->selling_price ?? 0,
              'discount_price' => $course->discount_price ?? 0,
              'image' => $course->course_image,
              'instructor_id' => $instructor ? $instructor->id : null,
          ],
      ]);
  
      $cartItems = CartItem::where('user_id', Auth::id())->get();
      $cartCount = $cartItems->count();
      $cartSubTotal = $cartItems->sum('price');
  
      return response()->json([
          'success' => true,
          'message' => 'Course added to the cart successfully!',
          'cartCount' => $cartCount,
          'cartSubTotal' => number_format($cartSubTotal, 2),
          'price' => $effectivePrice,
          'course_name' => $course->course_name,
          'image' => $course->course_image,
          'instructor_id' => $instructor ? $instructor->id : null,
          'instructor_name' => $instructor ? $instructor->name : 'Unknown Instructor',
          'selling_price' => $course->selling_price ?? 0,
          'discount_price' => $course->discount_price ?? 0,
      ], 200);
  }

  public function syncTempCart(Request $request)
  {
      if (!Auth::check()) {
          return response()->json(['error' => 'Unauthorized'], 401);
      }
  
      $request->validate([
          'tempCart' => 'array',
          'tempCart.*.courseId' => 'required|integer|exists:courses,id',
      ]);
  
      $tempCart = $request->input('tempCart', []);
      $response = ['success' => true, 'added' => 0, 'skipped' => []];
  
      foreach ($tempCart as $item) {
          $course = Course::with('courseable')->find($item['courseId']);
          if (!$course) {
              $response['skipped'][] = ['courseId' => $item['courseId'], 'reason' => 'Course not found'];
              \Illuminate\Support\Facades\Log::warning("Course not found during cart sync: {$item['courseId']}");
              continue;
          }
  
          $existingOrder = Order::where('user_id', Auth::id())
              ->where('course_id', $course->id)
              ->where('payment_status', 'paid')
              ->exists();
          if ($existingOrder) {
              $response['skipped'][] = ['courseId' => $item['courseId'], 'reason' => 'Already purchased'];
              continue;
          }
  
          $existingCartItem = CartItem::where('user_id', Auth::id())
              ->where('cartable_type', Course::class)
              ->where('cartable_id', $course->id)
              ->exists();
          if ($existingCartItem) {
              $response['skipped'][] = ['courseId' => $item['courseId'], 'reason' => 'Already in cart'];
              continue;
          }
  
          $effectivePrice = $course->discount_price !== null && $course->discount_price > 0 
              ? max(0, $course->selling_price - $course->discount_price) 
              : $course->selling_price;
  
          $instructor = $course->courseable_type === 'App\Models\Instructor' && $course->courseable_id
              ? Instructor::find($course->courseable_id)
              : null;
  
          try {
              CartItem::create([
                  'cartable_type' => Course::class,
                  'cartable_id' => $course->id,
                  'user_id' => Auth::id(),
                  'price' => $effectivePrice,
                  'options' => [
                      'instructor_name' => $instructor ? $instructor->name : 'Unknown Instructor',
                      'selling_price' => $course->selling_price ?? 0,
                      'discount_price' => $course->discount_price ?? 0,
                      'image' => $course->course_image,
                      'instructor_id' => $instructor ? $instructor->id : null,
                  ],
              ]);
              $response['added']++;
          } catch (\Exception $e) {
              $response['skipped'][] = ['courseId' => $item['courseId'], 'reason' => 'Failed to add to cart'];
              \Illuminate\Support\Facades\Log::error("Failed to add course to cart during sync: {$item['courseId']}, Error: {$e->getMessage()}");
          }
      }
  
      $cartItems = CartItem::where('user_id', Auth::id())->get();
      $cartCount = $cartItems->count();
      $cartSubTotal = $cartItems->sum('price');
  
      if ($response['added'] > 0) {
          session()->flash('cart_added_message', $response['added'] . ' course(s) added to your cart successfully!');
      }
  
      return response()->json([
          'success' => true,
          'message' => $response['added'] . ' course(s) added to cart' . (count($response['skipped']) > 0 ? ', some courses skipped' : ''),
          'cartCount' => $cartCount,
          'cartSubTotal' => number_format($cartSubTotal, 2),
          'clearTempCart' => true,
          'skipped' => $response['skipped']
      ], 200);
  }

    public function MyCart()
    {
        if (!Auth::check()) {
            return response()->json([
                'success' => false,
                'message' => 'Please log in to view your cart.',
                'redirect' => route('login') . '?redirect=' . urlencode(route('cart'))
            ], 401);
        }
    
        $cartItems = CartItem::where('user_id', Auth::id())->with('cartable')->get();
        $subtotal = $cartItems->sum('price');
        $coupons = session('coupons', []);
        $couponDiscount = array_sum(array_column($coupons, 'discount_amount'));
        $total = max(0, $subtotal - $couponDiscount);
    
        // Check for applicable coupons
        $courseIds = $cartItems->pluck('cartable_id')->toArray();
        $hasCoupons = Coupon::where('couponable_type', 'App\\Models\\Course')
            ->whereIn('couponable_id', $courseIds)
            ->where('coupon_validity', '>=', Carbon::today()->format('Y-m-d'))
            ->where('status', 1)
            ->exists();
    
        $cartItems = $cartItems->map(function ($item) {
            return (object) [
                'id' => $item->cartable_id,
                'name' => $item->cartable->course_name,
                'price' => $item->price,
                'attributes' => $item->options,
            ];
        });
    
        return view('User.mycart.view_mycart', compact('cartItems', 'subtotal', 'couponDiscount', 'total', 'coupons', 'hasCoupons'));
    }

    public function CartRemove($id)
    {
        if (!Auth::check()) {
            return response()->json([
                'success' => false,
                'message' => 'Please log in to manage your cart.',
                'redirect' => route('login') . '?redirect=' . urlencode(route('cart'))
            ], 401);
        }

        $cartItem = CartItem::where('user_id', Auth::id())
            ->where('cartable_type', Course::class)
            ->where('cartable_id', $id)
            ->first();

        if (!$cartItem) {
            return response()->json([
                'success' => false,
                'message' => 'Item not found in cart'
            ], 404);
        }

        $cartItem->delete();

        $cartItems = CartItem::where('user_id', Auth::id())->get();
        $subtotal = $cartItems->sum('price');
        $coupons = session('coupons', []);
        $couponDiscount = 0;
        $updatedCoupons = [];

        if (!empty($coupons) && $cartItems->isNotEmpty()) {
            $cartArray = $cartItems->map(function ($item) {
                return [
                    'cartable_id' => $item->cartable_id,
                    'options' => $item->options ?? [],
                ];
            })->toArray();

            foreach ($coupons as $couponData) {
                $coupon = Coupon::where('code', $couponData['code'])->first();
                if ($coupon && $this->isCouponApplicable($coupon, $cartArray)) {
                    $discount = $coupon->discount_type === 'percentage'
                        ? round($subtotal * $coupon->coupon_discount / 100, 2)
                        : min($subtotal, $coupon->coupon_discount);
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
            'message' => 'Item removed from cart!'
        ], 200);
    }

    private function isCouponApplicable($coupon, $cart)
    {
        foreach ($cart as $item) {
            if ($coupon->couponable_type === 'App\\Models\\Course' &&
                $coupon->couponable_id == $item['cartable_id']) {
                $couponInstructorId = $coupon->instructor_id ?? null;
                $cartInstructorId = $item['options']['instructor_id'] ?? null;
                if ($couponInstructorId === null || $couponInstructorId == $cartInstructorId) {
                    return true;
                }
            }
        }
        return false;
    }

    public function CouponApply(Request $request)
    {
        $request->validate([
            'code' => 'required|string|max:255',
        ]);

        $couponCode = strtoupper($request->code);

        $coupon = Coupon::where('code', $couponCode)
            ->where('coupon_validity', '>=', Carbon::now()->format('Y-m-d'))
            ->where('status', 1)
            ->first();

        if (!$coupon) {
            return $request->ajax()
                ? response()->json(['success' => false, 'message' => 'Invalid or expired coupon'], 400)
                : redirect()->route('cart')->with('error', 'Invalid or expired coupon');
        }

        if ($coupon->max_uses !== null && $coupon->uses >= $coupon->max_uses) {
            return $request->ajax()
                ? response()->json(['success' => false, 'message' => 'Coupon has reached its usage limit'], 400)
                : redirect()->route('cart')->with('error', 'Coupon has reached its usage limit');
        }

        $cartItems = CartItem::where('user_id', Auth::id())->get();
        if ($cartItems->isEmpty()) {
            return $request->ajax()
                ? response()->json(['success' => false, 'message' => 'Cart is empty'], 400)
                : redirect()->route('cart')->with('error', 'Cart is empty');
        }

        $cartArray = $cartItems->map(function ($item) {
            return [
                'cartable_id' => $item->cartable_id,
                'options' => $item->options ?? [],
            ];
        })->toArray();

        if (!$this->isCouponApplicable($coupon, $cartArray)) {
            $course = Course::find($coupon->couponable_id);
            $errorMessage = $course
                ? "This coupon is only applicable to the course: {$course->course_name}. Please add it to your cart."
                : 'Coupon not applicable to any course in cart';
            return $request->ajax()
                ? response()->json(['success' => false, 'message' => $errorMessage], 400)
                : redirect()->route('cart')->with('error', $errorMessage);
        }

        $coupons = session('coupons', []);
        if (isset($coupons[$coupon->code])) {
            return $request->ajax()
                ? response()->json(['success' => false, 'message' => 'Coupon already applied'], 400)
                : redirect()->route('cart')->with('info', 'Coupon already applied');
        }

        $subtotal = $cartItems->sum('price');
        $discount = $coupon->discount_type === 'percentage'
            ? round($subtotal * $coupon->coupon_discount / 100, 2)
            : min($subtotal, $coupon->coupon_discount);

        $coupons[$coupon->code] = [
            'code' => $coupon->code,
            'discount_amount' => $discount,
        ];
        session(['coupons' => $coupons]);

        $coupon->increment('uses');

        $couponDiscount = array_sum(array_column($coupons, 'discount_amount'));
        $total = max(0, $subtotal - $couponDiscount);

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Coupon applied successfully!',
                'subtotal' => number_format($subtotal, 2),
                'couponDiscount' => number_format($couponDiscount, 2),
                'totalPrice' => number_format($total, 2),
                'coupons' => array_values($coupons),
            ], 200);
        }

        return redirect()->route('cart')->with('success', 'Coupon applied!');
    }

    public function CouponRemove($couponCode)
    {
        $coupons = session('coupons', []);
        if (!isset($coupons[$couponCode])) {
            if (request()->ajax()) {
                return response()->json(['success' => false, 'message' => 'No such coupon was applied.'], 400);
            }
            return redirect()->route('cart')->with('info', 'No such coupon was applied.');
        }
    
        unset($coupons[$couponCode]);
        session(['coupons' => $coupons]);
    
        $cartItems = CartItem::where('user_id', Auth::id())->get();
        $subtotal = $cartItems->sum('price');
        $couponDiscount = array_sum(array_column($coupons, 'discount_amount'));
        $total = max(0, $subtotal - $couponDiscount);
    
        if (request()->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Coupon removed successfully!',
                'subtotal' => number_format($subtotal, 2),
                'couponDiscount' => number_format($couponDiscount, 2),
                'totalPrice' => number_format($total, 2),
                'coupons' => array_values($coupons),
            ], 200);
        }
    
        return redirect()->route('cart')->with('success', 'Coupon removed!');
    }

    public function CheckoutCreate()
    {
        if (!Auth::check()) {
            return response()->json([
                'success' => false,
                'message' => 'Please log in to proceed to checkout.',
                'redirect' => route('login') . '?redirect=' . urlencode(route('checkout'))
            ], 401);
        }

        $cartItems = CartItem::where('user_id', Auth::id())->with(['cartable' => function ($query) {
            $query->with(['courseable' => function ($q) {
                $q->select('id', 'name');
            }]);
        }])->get();

        if ($cartItems->isEmpty()) {
            return redirect()->route('cart')->with('error', 'Your cart is empty. Add at least one course.');
        }

        $validCartItems = $cartItems->filter(function ($item) {
            return $item->cartable &&
                   $item->cartable->exists &&
                   !empty($item->cartable->course_name) &&
                   $item->cartable->courseable_type === 'App\Models\Instructor' &&
                   $item->cartable->courseable_id &&
                   Instructor::where('id', $item->cartable->courseable_id)->exists();
        });

        if ($validCartItems->isEmpty()) {
            return redirect()->route('cart')->with('error', 'No valid courses in your cart. Please add valid courses with active instructors and valid titles.');
        }

        $subtotal = $validCartItems->sum('price');
        $coupons = session('coupons', []);
        $couponDiscount = array_sum(array_column($coupons, 'discount_amount'));
        $total = max(0, $subtotal - $couponDiscount);

        $adjustedPrices = [];
        if ($couponDiscount > 0 && $subtotal > 0) {
            foreach ($validCartItems as $item) {
                $proportion = $item->price / $subtotal;
                $discountForItem = $couponDiscount * $proportion;
                $adjustedPrices[$item->cartable_id] = max(0, $item->price - $discountForItem);
            }
        } else {
            foreach ($validCartItems as $item) {
                $adjustedPrices[$item->cartable_id] = $item->price;
            }
        }

        $cartSnapshot = $validCartItems->map(function ($item) {
            return [
                'cartable_id' => $item->cartable_id,
                'course_name' => $item->cartable->course_name ?? 'Untitled Course',
                'price' => $item->price,
                'instructor_id' => $item->cartable->courseable_id,
                'instructor_name' => $item->cartable->courseable ? $item->cartable->courseable->name : 'Unknown',
                'image' => $item->options['image'] ?? $item->cartable->course_image ?? null,
                'selling_price' => $item->options['selling_price'] ?? $item->cartable->selling_price ?? 0,
                'discount_price' => $item->options['discount_price'] >> $item->cartable->discount_price ?? 0,
            ];
        })->keyBy('cartable_id')->toArray();

        session(['checkout_cart_snapshot' => [
            'items' => $cartSnapshot,
            'subtotal' => $subtotal,
            'couponDiscount' => $couponDiscount,
            'total' => $total,
            'coupons' => $coupons,
            'created_at' => now()->timestamp,
        ]]);

        return view('User.checkout.checkout', compact('cartItems', 'subtotal', 'couponDiscount', 'total', 'coupons', 'adjustedPrices'));
    }

    public function processOrder($transactionId, $paymentMethod)
    {
        if (!Auth::check()) {
            return response()->json([
                'success' => false,
                'message' => 'Please log in to complete your purchase.',
                'redirect' => route('login') . '?redirect=' . urlencode(route('checkout'))
            ], 401);
        }

        $cartItems = CartItem::where('user_id', Auth::id())->with(['cartable' => function ($query) {
            $query->with(['courseable' => function ($q) {
                $q->select('id', 'name');
            }]);
        }])->get();

        $cartSnapshot = session('checkout_cart_snapshot');

        // Check snapshot validity
        if (!$cartSnapshot || !isset($cartSnapshot['items']) || !isset($cartSnapshot['created_at']) || now()->timestamp - $cartSnapshot['created_at'] > 3600) {
            return response()->json([
                'success' => false,
                'message' => 'Checkout session expired or invalid. Please start a new checkout.',
                'redirect' => route('cart')
            ], 400);
        }

        // If cart is empty, restore from snapshot
        if ($cartItems->isEmpty()) {
            $cartItems = collect($cartSnapshot['items'])->map(function ($item) use ($transactionId) {
                $course = Course::find($item['cartable_id']);
                if (!$course) {
                    return null;
                }
                $instructor = Instructor::find($item['instructor_id']);
                if (!$instructor) {
                    return null;
                }
                $courseName = $item['course_name'] ?? $course->course_name ?? 'Untitled Course';
                if (empty($courseName)) {
                    return null;
                }
                return (object) [
                    'cartable_id' => $item['cartable_id'],
                    'name' => $courseName,
                    'price' => $item['price'],
                    'attributes' => [
                        'instructor_id' => $item['instructor_id'],
                        'instructor_name' => $item['instructor_name'],
                        'image' => $item['image'],
                        'selling_price' => $item['selling_price'],
                        'discount_price' => $item['discount_price'],
                    ],
                ];
            })->filter()->values();
        } else {
            // Validate existing cart items
            $cartItems = $cartItems->filter(function ($item) use ($transactionId) {
                if (!$item->cartable || !$item->cartable->exists) {
                    return false;
                }
                $instructorId = $item->options['instructor_id'] ?? $item->cartable->courseable_id;
                if (!Instructor::where('id', $instructorId)->exists()) {
                    return false;
                }
                if (empty($item->cartable->course_name)) {
                    return false;
                }
                return true;
            });
        }

        if ($cartItems->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'No valid courses could be processed. Please verify that all courses and instructors are still available.',
                'redirect' => route('cart')
            ], 400);
        }

        $subtotal = $cartSnapshot['subtotal'] ?? $cartItems->sum('price');
        $coupons = $cartSnapshot['coupons'] ?? session('coupons', []);
        $couponDiscount = $cartSnapshot['couponDiscount'] ?? array_sum(array_column($coupons, 'discount_amount'));
        $totalDiscount = $couponDiscount > 0 && $subtotal > 0 ? $couponDiscount : 0;
        $total = max(0, $subtotal - $totalDiscount);

        $orders = [];
        foreach ($cartItems as $item) {
            $course = Course::find($item->cartable_id);
            if (!$course) {
                continue;
            }

            $instructorId = $item->attributes['instructor_id'] ?? $course->courseable_id;
            $instructor = Instructor::find($instructorId);
            if (!$instructor) {
                continue;
            }

            $courseTitle = $item->name ?? $course->course_name ?? 'Untitled Course';
            if (empty($courseTitle)) {
                continue;
            }

            // Check for existing order to prevent duplicates
            $existingOrder = Order::where('user_id', Auth::id())
                ->where('course_id', $item->cartable_id)
                ->where('payment_id', $transactionId)
                ->exists();
            if ($existingOrder) {
                continue; // Skip if this course is already ordered in this transaction
            }

            $itemDiscount = $totalDiscount > 0 && $subtotal > 0 ? ($item->price / $subtotal) * $totalDiscount : 0;
            $order = Order::create([
                'user_id' => Auth::id(),
                'course_id' => $item->cartable_id,
                'instructor_id' => $instructorId,
                'course_title' => $courseTitle,
                'price' => $item->price,
                'discount_amount' => round($itemDiscount, 2),
                'currency' => 'USD',
                'payment_status' => 'paid',
                'payment_id' => $transactionId,
                'payment_method' => $paymentMethod,
            ]);

            $instructor->notify(new OrderPlacedNotification($order));

            $orders[] = [
                'course_id' => $item->cartable_id,
                'course_title' => $courseTitle,
                'price' => $item->price,
                'discount' => round($itemDiscount, 2),
            ];
        }

        if (empty($orders)) {
            return response()->json([
                'success' => false,
                'message' => 'No valid courses could be processed. Please verify that all courses and instructors are still available.',
                'redirect' => route('cart')
            ], 400);
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
            'issued_at' => now(),
        ]);

        CartItem::where('user_id', Auth::id())->delete();
        session()->forget(['coupons', 'checkout_cart_snapshot']);

        return $invoice->id;
    }
    
}