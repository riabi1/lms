<?php
namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Darryldecode\Cart\Facades\CartFacade as Cart;
use Srmklive\PayPal\Services\PayPal as PayPalClient;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class PaypalPaymentController extends Controller
{
    public function payWithPaypal(Request $request, CartController $cartController)
    {
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Please log in.');
        }

        if (Cart::isEmpty()) {
            return redirect()->route('checkout.create')->with('error', 'Your cart is empty.');
        }

        $cartItems = Cart::getContent();
        $subtotal = Cart::getSubTotal();
        $coupons = session('coupons', []);
        $couponDiscount = array_sum(array_column($coupons, 'discount_amount'));
        $total = max(0, $subtotal - $couponDiscount);

        if ($total <= 0) {
            return redirect()->route('checkout.create')->with('error', 'Total amount must be greater than zero.');
        }

        try {
            $provider = new PayPalClient;
            $provider->setApiCredentials(config('paypal'));
            $accessToken = $provider->getAccessToken();

            if (!$accessToken || !isset($accessToken['access_token'])) {
                Log::error('PayPal Token Error', ['message' => 'Failed to retrieve access token', 'response' => $accessToken]);
                return redirect()->route('checkout.create')->with('error', 'PayPal Error: Authentication failed');
            }

            $items = [];
            foreach ($cartItems as $item) {
                $itemPrice = $couponDiscount > 0 && $subtotal > 0
                    ? max(0, $item->price - ($item->price / $subtotal) * $couponDiscount)
                    : $item->price;

                $items[] = [
                    'name' => $item->name,
                    'quantity' => 1,
                    'unit_amount' => [
                        'currency_code' => 'USD',
                        'value' => number_format($itemPrice, 2, '.', ''),
                    ],
                    'description' => 'Course by ' . $item->attributes['instructor_name'],
                ];
            }

            $itemTotal = array_sum(array_map(fn($item) => floatval($item['unit_amount']['value']), $items));

            $orderData = [
                'intent' => 'CAPTURE',
                'purchase_units' => [
                    [
                        'amount' => [
                            'currency_code' => 'USD',
                            'value' => number_format($total, 2, '.', ''),
                            'breakdown' => [
                                'item_total' => [
                                    'currency_code' => 'USD',
                                    'value' => number_format($itemTotal, 2, '.', ''),
                                ],
                            ],
                        ],
                        'items' => $items,
                        'description' => 'Purchase from Easy Learning (Sandbox)',
                    ],
                ],
                'application_context' => [
                    'return_url' => route('paypal.success'),
                    'cancel_url' => route('paypal.cancel'),
                    'brand_name' => 'Easy Learning',
                    'shipping_preference' => 'NO_SHIPPING',
                    'user_action' => 'PAY_NOW',
                ],
            ];

            Log::info('PayPal Order Creation Request', $orderData);

            $response = $provider->createOrder($orderData);

            Log::info('PayPal Order Creation Response', ['response' => $response]);

            if (isset($response['id']) && $response['status'] === 'CREATED') {
                foreach ($response['links'] as $link) {
                    if ($link['rel'] === 'approve') {
                        return redirect()->away($link['href']);
                    }
                }
            }

            Log::error('PayPal Order Creation Failed', ['response' => $response]);
            return redirect()->route('checkout.create')->with('error', 'PayPal Error: Unable to create order - ' . ($response['message'] ?? 'Unknown error'));
        } catch (\Exception $e) {
            Log::error('PayPal Exception', ['error' => $e->getMessage()]);
            return redirect()->route('checkout.create')->with('error', 'PayPal Error: ' . $e->getMessage());
        }
    }

    public function paypalSuccess(Request $request, CartController $cartController)
    {
        try {
            $provider = new PayPalClient;
            $provider->setApiCredentials(config('paypal'));
            $provider->getAccessToken();

            $orderId = $request->input('token');
            Log::info('PayPal Success Callback', ['order_id' => $orderId, 'request' => $request->all()]);

            $orderDetails = $provider->showOrderDetails($orderId);
            Log::info('PayPal Order Details', ['order' => $orderDetails]);

            if (isset($orderDetails['status']) && $orderDetails['status'] === 'COMPLETED') {
                $transactionId = $orderDetails['id'];
                $cartController->processOrder($transactionId, 'PayPal');
                return redirect()->route('order.success')->with('success', 'Payment successful! Transaction ID: ' . $transactionId);
            } elseif (isset($orderDetails['status']) && $orderDetails['status'] === 'APPROVED') {
                $response = $provider->capturePaymentOrder($orderId);
                Log::info('PayPal Capture Response', ['response' => $response]);

                if (isset($response['status']) && $response['status'] === 'COMPLETED') {
                    $transactionId = $response['id'];
                    $cartController->processOrder($transactionId, 'PayPal');
                    return redirect()->route('order.success')->with('success', 'Payment successful! Transaction ID: ' . $transactionId);
                } else {
                    Log::error('PayPal Capture Failed', ['response' => $response]);
                    return redirect()->route('checkout.create')->with('error', 'PayPal Error: Payment capture failed');
                }
            } else {
                Log::error('PayPal Unexpected Order Status', ['order' => $orderDetails]);
                return redirect()->route('checkout.create')->with('error', 'PayPal Error: Unexpected order status');
            }
        } catch (\Exception $e) {
            Log::error('PayPal Success Exception', ['error' => $e->getMessage()]);
            return redirect()->route('checkout.create')->with('error', 'PayPal Error: ' . $e->getMessage());
        }
    }

    public function paypalCancel()
    {
        return redirect()->route('checkout.create')->with('error', 'Payment was cancelled.');
    }
}