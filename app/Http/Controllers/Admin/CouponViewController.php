<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Coupon;
use Illuminate\Http\Request;

class CouponViewController extends Controller
{
    public function index()
    {
        $coupons = Coupon::with(['course', 'instructor'])->orderBy('id', 'desc')->get();
        return view('admin.coupon.index', compact('coupons'));
    }

    public function show(Coupon $coupon)
    {
        $coupon->load(['course', 'instructor']);
        return view('admin.coupon.show', compact('coupon'));
    }
}