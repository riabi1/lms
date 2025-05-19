<?php

namespace App\Http\Controllers\Instructor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Coupon;
use App\Models\Course;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Illuminate\Support\Str;

class CouponController extends Controller
{
    public function index()
    {
        $instructorId = Auth::guard('instructor')->id();
        $coupons = Coupon::where('couponable_type', 'App\\Models\\Course')
            ->whereIn('couponable_id', Course::where('courseable_type', 'App\\Models\\Instructor')
                ->where('courseable_id', $instructorId)
                ->pluck('id'))
            ->with('couponable')
            ->latest()
            ->get();

        return view('instructor.coupon.index', compact('coupons'));
    }

    public function create()
    {
        $instructorId = Auth::guard('instructor')->id();
        $courses = Course::where('courseable_type', 'App\\Models\\Instructor')
            ->where('courseable_id', $instructorId)
            ->get();

        return view('instructor.coupon.create', compact('courses'));
    }

    public function store(Request $request)
    {
        $instructorId = Auth::guard('instructor')->id();
        $course = Course::findOrFail($request->course_id);

        $request->validate([
            'coupon_discount' => [
                'required',
                'numeric',
                'min:0',
                function ($attribute, $value, $fail) use ($request, $course) {
                    if ($request->discount_type === 'fixed' && $value > $course->selling_price) {
                        $fail('Fixed discount cannot exceed course price of ' . $course->selling_price . '.');
                    }
                    if ($request->discount_type === 'percentage' && $value > 100) {
                        $fail('Percentage discount cannot exceed 100%.');
                    }
                },
            ],
            'discount_type' => 'required|in:fixed,percentage',
            'max_uses' => 'nullable|integer|min:1',
            'coupon_validity' => 'required|date|after_or_equal:today',
            'course_id' => [
                'required',
                'exists:courses,id',
                function ($attribute, $value, $fail) use ($instructorId) {
                    $course = Course::find($value);
                    if (!$course || $course->courseable_type !== 'App\Models\Instructor' || $course->courseable_id !== $instructorId) {
                        $fail('The selected course does not belong to you.');
                    }
                },
            ],
            'status' => 'required|in:0,1',
        ]);

        // Generate unique coupon code
        $code = $this->generateCouponCode($course);

        Coupon::create([
            'code' => $code,
            'coupon_discount' => $request->coupon_discount,
            'discount_type' => $request->discount_type,
            'max_uses' => $request->max_uses,
            'uses' => 0,
            'coupon_validity' => $request->coupon_validity,
            'status' => $request->status,
            'couponable_id' => $request->course_id,
            'couponable_type' => 'App\\Models\\Course',
            'created_at' => Carbon::now(),
        ]);

        return redirect()->route('instructor.coupon.index')->with('success', 'Coupon created successfully');
    }

    public function edit(Coupon $coupon)
    {
        $this->authorizeInstructor($coupon);

        $instructorId = Auth::guard('instructor')->id();
        $courses = Course::where('courseable_type', 'App\\Models\\Instructor')
            ->where('courseable_id', $instructorId)
            ->get();

        return view('instructor.coupon.edit', compact('coupon', 'courses'));
    }

    public function update(Request $request, Coupon $coupon)
    {
        $this->authorizeInstructor($coupon);
        $instructorId = Auth::guard('instructor')->id();
        $course = Course::findOrFail($request->course_id);

        $request->validate([
            'coupon_discount' => [
                'required',
                'numeric',
                'min:0',
                function ($attribute, $value, $fail) use ($request, $course) {
                    if ($request->discount_type === 'fixed' && $value > $course->selling_price) {
                        $fail('Fixed discount cannot exceed course price of ' . $course->selling_price . '.');
                    }
                    if ($request->discount_type === 'percentage' && $value > 100) {
                        $fail('Percentage discount cannot exceed 100%.');
                    }
                },
            ],
            'discount_type' => 'required|in:fixed,percentage',
            'max_uses' => 'nullable|integer|min:1',
            'coupon_validity' => 'required|date|after_or_equal:today',
            'course_id' => [
                'required',
                'exists:courses,id',
                function ($attribute, $value, $fail) use ($instructorId) {
                    $course = Course::find($value);
                    if (!$course || $course->courseable_type !== 'App\Models\Instructor' || $course->courseable_id !== $instructorId) {
                        $fail('The selected course does not belong to you.');
                    }
                },
            ],
            'status' => 'required|in:0,1',
        ]);

        $coupon->update([
            'coupon_discount' => $request->coupon_discount,
            'discount_type' => $request->discount_type,
            'max_uses' => $request->max_uses,
            'coupon_validity' => $request->coupon_validity,
            'couponable_id' => $request->course_id,
            'couponable_type' => 'App\\Models\\Course',
            'status' => $request->status,
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
        $instructorId = Auth::guard('instructor')->id();
        $course = Course::find($coupon->couponable_id);
        if (!$course || $course->courseable_type !== 'App\Models\Instructor' || $course->courseable_id !== $instructorId) {
            abort(403, 'Unauthorized action.');
        }
    }

    private function generateCouponCode($course)
    {
        // Generate a prefix from the course name (first 4 letters, uppercase, no spaces/dashes)
        $prefix = strtoupper(preg_replace('/[^A-Za-z0-9]/', '', substr($course->course_name, 0, 4)));

        do {
            $randomString = Str::random(6);
            $year = Carbon::now()->format('Y');
            $code = "{$prefix}-{$randomString}-{$year}";
        } while (Coupon::where('code', $code)->exists());

        return strtoupper($code);
    }

    public function toggleStatus(Coupon $coupon)
    {
        $this->authorizeInstructor($coupon);

        $coupon->status = $coupon->status == 1 ? 0 : 1;
        $coupon->updated_at = Carbon::now();
        $coupon->save();

        return redirect()->route('instructor.coupon.index')->with('success', 'Coupon status updated successfully');
    }
}