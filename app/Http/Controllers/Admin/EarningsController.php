<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class EarningsController extends Controller
{
    /**
     * Display the total earnings and breakdown by instructor.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        // Calculate total earnings from paid orders
        $totalEarnings = Order::where('payment_status', 'paid')->sum('price');

        // Fetch earnings grouped by instructor
        $earningsByInstructor = Order::where('payment_status', 'paid')
            ->join('instructors', 'orders.instructor_id', '=', 'instructors.id')
            ->select(
                'instructors.id',
                'instructors.name',
                'instructors.email',
                DB::raw('SUM(orders.price) as total_earnings')
            )
            ->groupBy('instructors.id', 'instructors.name', 'instructors.email')
            ->get();

        return view('admin.earnings', compact('totalEarnings', 'earningsByInstructor'));
    }
}