<?php

namespace App\Http\Controllers\Instructor;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class InstructorEarningsController extends Controller
{
    /**
     * Display the instructor's total earnings.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $instructor = Auth::guard('instructor')->user();
        
        // Calculate total earnings for the authenticated instructor
        $totalEarnings = Order::where('payment_status', 'paid')
            ->where('instructor_id', $instructor->id)
            ->sum('price');

        return view('instructor.earnings', compact('totalEarnings'));
    }
}