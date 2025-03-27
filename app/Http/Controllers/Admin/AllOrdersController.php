<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;

class AllOrdersController extends Controller
{
    public function index()
    {
        $orders = Order::with(['user', 'course', 'instructor'])
            ->latest()
            ->paginate(10);

        return view('admin.Order.index', compact('orders'));
    }

    public function show($id)
    {
        $order = Order::with(['user', 'course',  'instructor'])
            ->findOrFail($id);

        return view('admin.Order.show', compact('order'));
    }
}