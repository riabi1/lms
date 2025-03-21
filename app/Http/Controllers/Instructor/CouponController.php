<?php

namespace App\Http\Controllers\Instructor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Coupon;
use App\Models\Course;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class CouponController extends Controller
{
    public function index()
    {
        $id = Auth::guard('instructor')->user()->id;
        $coupons = Coupon::where('instructor_id', $id)->latest()->get();
        return view('instructor.coupon.index', compact('coupons'));
    }

    public function create()
    {
        $id = Auth::guard('instructor')->user()->id;
        $courses = Course::where('instructor_id', $id)->get();
        return view('instructor.coupon.create', compact('courses'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'coupon_name' => 'required|string|max:255|unique:coupons,coupon_name',
            'coupon_discount' => 'required|numeric|min:0|max:100',
            'coupon_validity' => 'required|date|after_or_equal:today',
            'course_id' => 'required|exists:courses,id',
        ]);

        Coupon::create([
            'coupon_name' => strtoupper($request->coupon_name),
            'coupon_discount' => $request->coupon_discount,
            'coupon_validity' => $request->coupon_validity,
            'instructor_id' => Auth::guard('instructor')->user()->id,
            'course_id' => $request->course_id,
            'created_at' => Carbon::now(),
        ]);

        return redirect()->route('instructor.coupon.index')->with('success', 'Coupon created successfully');
    }

    public function edit(Coupon $coupon)
    {
        $this->authorizeInstructor($coupon);
        $courses = Course::where('instructor_id', Auth::guard('instructor')->user()->id)->get();
        return view('instructor.coupon.edit', compact('coupon', 'courses'));
    }

    public function update(Request $request, Coupon $coupon)
    {
        $this->authorizeInstructor($coupon);

        $request->validate([
            'coupon_name' => 'required|string|max:255|unique:coupons,coupon_name,' . $coupon->id,
            'coupon_discount' => 'required|numeric|min:0|max:100',
            'coupon_validity' => 'required|date|after_or_equal:today',
            'course_id' => 'required|exists:courses,id',
        ]);

        $coupon->update([
            'coupon_name' => strtoupper($request->coupon_name),
            'coupon_discount' => $request->coupon_discount,
            'coupon_validity' => $request->coupon_validity,
            'instructor_id' => Auth::guard('instructor')->user()->id,
            'course_id' => $request->course_id,
            'updated_at' => Carbon::now(),
        ]);

        return redirect()->route('instructor.coupon.index')->with('success', 'Coupon updated successfully');
    }

    public function destroy(Coupon $coupon)
    {
        $this->authorizeInstructor($coupon);
        $coupon->delete();

        return redirect()->route('instructor.coupon.index')->with('success', 'Coupon deleted successfully');
    }

    private function authorizeInstructor(Coupon $coupon)
    {
        if ($coupon->instructor_id !== Auth::guard('instructor')->user()->id) {
            abort(403, 'Unauthorized action.');
        }
    }
}