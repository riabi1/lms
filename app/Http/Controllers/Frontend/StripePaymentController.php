<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\CartItem;
use App\Models\Course;
use App\Models\Invoice;
use App\Models\Order;
use Illuminate\Http\Request;
use Omnipay\Omnipay;
use Illuminate\Support\Facades\Auth;

class StripePaymentController extends Controller
{
    private $stripeGateway;

    public function __construct()
    {
        $this->stripeGateway = Omnipay::create('Stripe');
        $this->stripeGateway->setApiKey(env('STRIPE_SECRET'));
    }

    public function payWithStripe(Request $request, CartController $cartController)
    {
        $request->validate([
            'stripeToken' => 'required',
        ]);

        // Fetch cart items
        $cartItems = CartItem::with(['cartable' => function ($query) {
            $query->select('id', 'course_name', 'selling_price', 'discount_price');
        }])
            ->where('user_id', Auth::id())
            ->where('cartable_type', 'App\Models\Course')
            ->get()
            ->filter(function ($item) {
                return $item->cartable && Course::where('id', $item->cartable_id)->exists();
            })
            ->map(function ($item) {
                return (object) [
                    'id' => $item->cartable_id,
                    'name' => $item->cartable->course_name ?? ($item->options['course_name'] ?? 'Unknown Course'),
                    'price' => $item->price,
                    'quantity' => 1,
                ];
            });

        if ($cartItems->isEmpty()) {
            return redirect()->route('checkout.create')->with('error', 'Your cart is empty.');
        }

        // Calculate prices
        $subtotal = $cartItems->sum(fn($item) => $item->price);
        $coupons = session('coupons', []);
        $couponDiscount = array_sum(array_column($coupons, 'discount_amount'));
        $total = max(0, $subtotal - $couponDiscount);

        // Handle zero total
        if ($total <= 0) {
            $transactionId = 'FREE-' . uniqid();
            $invoiceId = $cartController->processOrder($transactionId, 'Free');
            $invoice = Invoice::findOrFail($invoiceId);
            return redirect()->route('checkout.success')
                            ->with('success', 'Order processed successfully! No payment required.')
                            ->with('invoice', $invoice);
        }

        try {
            $response = $this->stripeGateway->purchase([
                'amount' => number_format($total, 2, '.', ''),
                'currency' => 'USD',
                'description' => 'Payment for courses by ' . Auth::user()->name,
                'source' => $request->stripeToken,
            ])->send();

            if ($response->isSuccessful()) {
                $transactionId = $response->getTransactionReference();
                $existingOrder = Order::where('payment_id', $transactionId)->first();
                if ($existingOrder) {
                    $invoice = Invoice::where('payment_id', $transactionId)->first();
                    return redirect()->route('checkout.success')
                                    ->with('success', 'Order already processed! Transaction ID: ' . $transactionId)
                                    ->with('invoice', $invoice);
                }

                $invoiceId = $cartController->processOrder($transactionId, 'Stripe');
                $invoice = Invoice::findOrFail($invoiceId);

                return redirect()->route('checkout.success')
                                ->with('success', 'Payment successful! Transaction ID: ' . $transactionId)
                                ->with('invoice', $invoice);
            } else {
                return redirect()->route('checkout.create')->with('error', $response->getMessage());
            }
        } catch (\Exception $e) {
            return redirect()->route('checkout.create')->with('error', 'Payment failed: ' . $e->getMessage());
        }
    }
}