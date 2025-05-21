<?php

namespace App\Http\Controllers\User;

use App\Models\Order;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class PurchaseHistoryController extends Controller
{
    public function index()
    {
        // Get the authenticated user's ID
        $userId = Auth::id();

        // Fetch the user's purchased courses from the orders table with related data
        $purchases = Order::where('orders.user_id', $userId)
            ->join('courses', 'orders.course_id', '=', 'courses.id')
            ->leftJoin('instructors', 'orders.instructor_id', '=', 'instructors.id')
            ->leftJoin('sub_categories', 'courses.subcategory_id', '=', 'sub_categories.id')
            ->leftJoin('categories', 'sub_categories.category_id', '=', 'categories.id')
            ->leftJoin('invoices', function ($join) {
                $join->on('orders.payment_id', '=', 'invoices.payment_id')
                     ->where('invoices.user_id', Auth::id());
            })
            ->select(
                'orders.id',
                'orders.course_id',
                'orders.course_title',
                'orders.price',
                'orders.discount_amount',
                'courses.selling_price as original_price',
                'courses.course_image',
                'instructors.name as instructor_name',
                'categories.category_name',
                'orders.created_at as purchase_date',
                'invoices.id as invoice_id',
                'invoices.invoice_number',
                'invoices.subtotal',
                'invoices.discount',
                'invoices.total',
                'invoices.payment_method',
                'invoices.items',
                'invoices.issued_at'
            )
            ->where('orders.payment_status', 'paid')
            ->get();

        // Transform purchases to include invoice data as an object
        $purchases = $purchases->map(function ($purchase) {
            if (is_string($purchase->purchase_date)) {
                $purchase->purchase_date = \Carbon\Carbon::parse($purchase->purchase_date);
            }
            $purchase->invoice = $purchase->invoice_number ? [
                'id' => $purchase->invoice_id,
                'invoice_number' => $purchase->invoice_number,
                'subtotal' => $purchase->subtotal,
                'discount' => $purchase->discount,
                'total' => $purchase->total,
                'payment_method' => $purchase->payment_method,
                'items' => $purchase->items,
                'issued_at' => $purchase->issued_at,
            ] : null;
            return $purchase;
        });

        // Pass the data to the Blade view
        return view('User.purchasehistory', compact('purchases'));
    }
}