<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\SubCategory;
use App\Models\Course;
use App\Models\Course_goal;
use App\Models\CourseSection;
use App\Models\CourseLecture;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Gloudemans\Shoppingcart\Facades\Cart;
use App\Models\Coupon;
use Illuminate\Support\Facades\Session;
use App\Models\Payment;
use App\Models\Order;
use Illuminate\Support\Facades\Mail;
use App\Mail\Orderconfirm;
use Stripe;
use App\Models\User;
use App\Notifications\OrderComplete;
use Illuminate\Support\Facades\Notification;

class CartController extends Controller
{
    public function AddToCart(Request $request, $id)
    {
        $course = Course::find($id);
        if (!$course) {
            return response()->json(['error' => 'Course not found']);
        }

        if (Session::has('coupon')) {
            Session::forget('coupon');
        }

        $cartItem = Cart::search(function ($cartItem, $rowId) use ($id) {
            return $cartItem->id === $id;
        });

        if ($cartItem->isNotEmpty()) {
            return response()->json(['error' => 'Course is already in your cart']);
        }

        $price = $course->discount_price ?? $course->selling_price;

        Cart::add([
            'id' => $id,
            'name' => $request->course_name,
            'qty' => 1,
            'price' => $price,
            'weight' => 1,
            'options' => [
                'image' => $course->course_image,
                'slug' => $request->course_name_slug,
                'instructor' => $request->instructor,
            ],
        ]);

        return response()->json(['success' => 'Successfully Added on Your Cart']);
    }

    public function CartData()
    {
        $carts = Cart::content();
        $cartTotal = Cart::total();
        $cartQty = Cart::count();

        return response()->json([
            'carts' => $carts,
            'cartTotal' => $cartTotal,
            'cartQty' => $cartQty,
        ]);
    }

    public function AddMiniCart()
    {
        $carts = Cart::content();
        $cartTotal = Cart::total();
        $cartQty = Cart::count();

        return response()->json([
            'carts' => $carts,
            'cartTotal' => $cartTotal,
            'cartQty' => $cartQty,
        ]);
    }

    public function RemoveMiniCart($rowId)
    {
        Cart::remove($rowId);
        return response()->json(['success' => 'Course Removed From Cart']);
    }

    public function MyCart()
    {
        return view('User.mycart.view_mycart');
    }

    public function GetCartCourse()
    {
        $carts = Cart::content();
        $cartTotal = Cart::total();
        $cartQty = Cart::count();

        return response()->json([
            'carts' => $carts,
            'cartTotal' => $cartTotal,
            'cartQty' => $cartQty,
        ]);
    }

    public function CartRemove($rowId)
    {
        Cart::remove($rowId);

        if (Session::has('coupon')) {
            $coupon_name = Session::get('coupon')['coupon_name'];
            $coupon = Coupon::where('coupon_name', $coupon_name)->first();

            if ($coupon) {
                Session::put('coupon', [
                    'coupon_name' => $coupon->coupon_name,
                    'coupon_discount' => $coupon->coupon_discount,
                    'discount_amount' => round(Cart::total() * $coupon->coupon_discount / 100),
                    'total_amount' => round(Cart::total() - Cart::total() * $coupon->coupon_discount / 100)
                ]);
            }
        }
        return response()->json(['success' => 'Course Removed From Cart']);
    }

    public function CouponApply(Request $request)
    {
        $coupon = Coupon::where('coupon_name', $request->coupon_name)
            ->where('coupon_validity', '>=', Carbon::now()->format('Y-m-d'))
            ->first();

        if ($coupon) {
            Session::put('coupon', [
                'coupon_name' => $coupon->coupon_name,
                'coupon_discount' => $coupon->coupon_discount,
                'discount_amount' => round(Cart::total() * $coupon->coupon_discount / 100),
                'total_amount' => round(Cart::total() - Cart::total() * $coupon->coupon_discount / 100)
            ]);

            return response()->json([
                'validity' => true,
                'success' => 'Coupon Applied Successfully'
            ]);
        } else {
            return response()->json(['error' => 'Invalid Coupon']);
        }
    }

    public function InsCouponApply(Request $request)
    {
        $coupon = Coupon::where('coupon_name', $request->coupon_name)
            ->where('coupon_validity', '>=', Carbon::now()->format('Y-m-d'))
            ->first();

        if ($coupon) {
            if ($coupon->course_id == $request->course_id && $coupon->instructor_id == $request->instructor_id) {
                Session::put('coupon', [
                    'coupon_name' => $coupon->coupon_name,
                    'coupon_discount' => $coupon->coupon_discount,
                    'discount_amount' => round(Cart::total() * $coupon->coupon_discount / 100),
                    'total_amount' => round(Cart::total() - Cart::total() * $coupon->coupon_discount / 100)
                ]);

                return response()->json([
                    'validity' => true,
                    'success' => 'Coupon Applied Successfully'
                ]);
            } else {
                return response()->json(['error' => 'Coupon Criteria Not Met for this course and instructor']);
            }
        } else {
            return response()->json(['error' => 'Invalid Coupon']);
        }
    }

    public function CouponCalculation()
    {
        if (Session::has('coupon')) {
            return response()->json([
                'subtotal' => Cart::total(),
                'coupon_name' => session()->get('coupon')['coupon_name'],
                'coupon_discount' => session()->get('coupon')['coupon_discount'],
                'discount_amount' => session()->get('coupon')['discount_amount'],
                'total_amount' => session()->get('coupon')['total_amount'],
            ]);
        } else {
            return response()->json([
                'total' => Cart::total(),
            ]);
        }
    }

    public function CouponRemove()
    {
        Session::forget('coupon');
        return response()->json(['success' => 'Coupon Removed Successfully']);
    }

    public function CheckoutCreate()
    {
        if (Auth::check()) {
            if (Cart::total() > 0) {
                $carts = Cart::content();
                $cartTotal = Cart::total();
                $cartQty = Cart::count();

                return view('User.checkout.checkout_view', compact('carts', 'cartTotal', 'cartQty'));
            } else {
                $notification = [
                    'message' => 'Add At Least One Course',
                    'alert-type' => 'error'
                ];
                return redirect()->to('/')->with($notification);
            }
        } else {
            $notification = [
                'message' => 'You Need to Login First',
                'alert-type' => 'error'
            ];
            return redirect()->route('login')->with($notification);
        }
    }

   public function Payment(Request $request)
{
    $user = User::where('role', 'instructor')->get();
    $total_amount = Session::has('coupon') ? Session::get('coupon')['total_amount'] : round(Cart::total());

    $data = [
        'name' => $request->name,
        'email' => $request->email,
        'phone' => $request->phone,
        'address' => $request->address,
        'course_title' => $request->course_title,
    ];
    $cartTotal = Cart::total();
    $carts = Cart::content();

    if ($request->cash_delivery == 'stripe') {
        return view('User.payment.stripe', compact('data', 'cartTotal', 'carts'));
    } elseif ($request->cash_delivery == 'handcash') {
        $payment = new Payment();
        $payment->name = $request->name;
        $payment->email = $request->email;
        $payment->phone = $request->phone;
        $payment->address = $request->address;
        $payment->cash_delivery = $request->cash_delivery;
        $payment->total_amount = $total_amount;
        $payment->payment_type = 'Direct Payment';
        $payment->invoice_no = 'EOS' . mt_rand(10000000, 99999999);
        $payment->order_date = Carbon::now()->format('d F Y');
        $payment->order_month = Carbon::now()->format('F');
        $payment->order_year = Carbon::now()->format('Y');
        $payment->status = 'pending';
        $payment->created_at = Carbon::now();
        $payment->save();

        foreach ($request->course_title as $key => $course_title) {
            $existingOrder = Order::where('user_id', Auth::user()->id)
                ->where('course_id', $request->course_id[$key])
                ->first();

            if ($existingOrder) {
                $notification = [
                    'message' => 'You Have already enrolled in this course',
                    'alert-type' => 'error'
                ];
                return redirect()->back()->with($notification);
            }

            $order = new Order();
            $order->payment_id = $payment->id;
            $order->user_id = Auth::user()->id;
            $order->course_id = $request->course_id[$key];
            $order->instructor_id = $request->instructor_id[$key];
            $order->course_title = $course_title;
            $order->price = $request->price[$key];
            $order->save();
        }

        $request->session()->forget('cart');
        $paymentId = $payment->id;

        $sendmail = Payment::find($paymentId);
        $data = [
            'invoice_no' => $sendmail->invoice_no,
            'amount' => $total_amount,
            'name' => $sendmail->name,
            'email' => $sendmail->email,
        ];

        try {
            Mail::to($request->email)->send(new Orderconfirm($data));
        } catch (\Exception $e) {
            \Log::error('Email sending failed: ' . $e->getMessage());
            // Optionally notify the user that email failed but order was processed
        }

        Notification::send($user, new OrderComplete($request->name));

        $notification = [
            'message' => 'Cash Payment Submitted Successfully',
            'alert-type' => 'success'
        ];
        return redirect()->route('index')->with($notification);
    }
}

    public function StripeOrder(Request $request)
    {
        $total_amount = Session::has('coupon') ? Session::get('coupon')['total_amount'] : round(Cart::total());

        \Stripe\Stripe::setApiKey(env('STRIPE_SECRET'));

        $token = $request->input('stripeToken');

        $charge = \Stripe\Charge::create([
            'amount' => $total_amount * 100,
            'currency' => 'usd',
            'description' => 'Lms',
            'source' => $token,
            'metadata' => ['order_id' => '3434'],
        ]);

        $order_id = Payment::insertGetId([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'address' => $request->address,
            'total_amount' => $total_amount,
            'payment_type' => 'Stripe',
            'invoice_no' => 'EOS' . mt_rand(10000000, 99999999),
            'order_date' => Carbon::now()->format('d F Y'),
            'order_month' => Carbon::now()->format('F'),
            'order_year' => Carbon::now()->format('Y'),
            'status' => 'pending',
            'created_at' => Carbon::now(),
        ]);

        $carts = Cart::content();
        foreach ($carts as $cart) {
            Order::insert([
                'payment_id' => $order_id,
                'user_id' => Auth::user()->id,
                'course_id' => $cart->id,
                'instructor_id' => $cart->options->instructor,
                'course_title' => $cart->name, // Changed from options->name to name
                'price' => $cart->price,
            ]);
        }

        if (Session::has('coupon')) {
            Session::forget('coupon');
        }
        Cart::destroy();

        $notification = [
            'message' => 'Stripe Payment Submitted Successfully',
            'alert-type' => 'success'
        ];
        return redirect()->route('index')->with($notification);
    }

    public function BuyToCart(Request $request, $id)
    {
        $course = Course::find($id);
        if (!$course) {
            return response()->json(['error' => 'Course not found']);
        }

        $cartItem = Cart::search(function ($cartItem, $rowId) use ($id) {
            return $cartItem->id === $id;
        });

        if ($cartItem->isNotEmpty()) {
            return response()->json(['error' => 'Course is already in your cart']);
        }

        $price = $course->discount_price ?? $course->selling_price;

        Cart::add([
            'id' => $id,
            'name' => $request->course_name,
            'qty' => 1,
            'price' => $price,
            'weight' => 1,
            'options' => [
                'image' => $course->course_image,
                'slug' => $request->course_name_slug,
                'instructor' => $request->instructor,
            ],
        ]);

        return response()->json(['success' => 'Successfully Added on Your Cart']);
    }

    public function MarkAsRead(Request $request, $notificationId)
    {
        $user = Auth::user();
        $notification = $user->notifications()->where('id', $notificationId)->first();

        if ($notification) {
            $notification->markAsRead();
        }
        return response()->json(['count' => $user->unreadNotifications()->count()]);
    }
}