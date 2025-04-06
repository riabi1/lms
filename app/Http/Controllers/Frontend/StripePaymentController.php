<?php
namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Darryldecode\Cart\Facades\CartFacade as Cart;
use Omnipay\Omnipay;
use Illuminate\Support\Facades\Auth;
use App\Models\Invoice;

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

        $cartItems = Cart::getContent();
        $subtotal = Cart::getSubTotal();
        $coupons = session('coupons', []);
        $couponDiscount = array_sum(array_column($coupons, 'discount_amount'));
        $total = max(0, $subtotal - $couponDiscount);

        $items = [];
        foreach ($cartItems as $item) {
            $items[] = [
                'name' => $item->name,
                'quantity' => 1,
                'price' => number_format($item->price, 2, '.', ''),
                'currency' => 'USD',
            ];
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