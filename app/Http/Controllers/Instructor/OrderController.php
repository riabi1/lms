<?php

namespace App\Http\Controllers\Instructor;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OrderController extends Controller
{
    public function index()
    {
        $instructorId = Auth::id(); // ID de l’instructeur connecté
        $orders = Order::where('instructor_id', $instructorId)
            ->with(['user', 'course']) // Charger les relations
            ->latest() // Trier par date de création
            ->paginate(10); // Pagination

        return view('instructor.orders.index', compact('orders'));
    }

    public function show($id)
    {
        $instructorId = Auth::id();
        $order = Order::where('instructor_id', $instructorId)
            ->with(['user', 'course'])
            ->findOrFail($id); // Récupérer la commande spécifique

        // Marquer les notifications liées à cette commande comme lues
        $instructor = Auth::guard('instructor')->user();
        $notifications = $instructor->unreadNotifications()
            ->where('data->order_id', $order->id)
            ->get();

        foreach ($notifications as $notification) {
            $notification->markAsRead();
        }

        return view('instructor.orders.show', compact('order'));
    }
}