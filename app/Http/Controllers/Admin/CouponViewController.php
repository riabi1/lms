<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Coupon;
use App\Models\Course;
use Illuminate\Http\Request;

class CouponViewController extends Controller
{
    public function index()
    {
        $coupons = Coupon::where('couponable_type', 'App\\Models\\Course')
            ->with(['couponable' => function ($query) {
                $query->with('courseable'); // Load the instructor through the course
            }])
            ->orderBy('id', 'desc')
            ->get();

        return view('admin.coupon.index', compact('coupons'));
    }

    public function show(Coupon $coupon)
    {
        $coupon->load(['couponable' => function ($query) {
            $query->with('courseable'); // Load the instructor through the course
        }]);
        return view('admin.coupon.show', compact('coupon'));
    }
}