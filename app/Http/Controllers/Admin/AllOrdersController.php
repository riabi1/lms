<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;

class AllOrdersController extends Controller
{
    public function index()
    {
        $orders = Order::with(['user', 'course', 'payment', 'instructor'])
            ->latest() // Trier par date de création
            ->paginate(10); // Pagination

        return view('admin.Order.index', compact('orders'));
    }

    public function show($id)
    {
        $order = Order::with(['user', 'course', 'payment', 'instructor'])
            ->findOrFail($id); // Récupérer la commande spécifique

        return view('admin.Order.show', compact('order'));
    }
}