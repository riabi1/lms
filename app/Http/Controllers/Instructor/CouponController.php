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
        $instructorId = Auth::guard('instructor')->id();
        $coupons = Coupon::where('instructor_id', $instructorId)
            ->latest()
            ->get();

        return view('instructor.coupon.index', compact('coupons'));
    }

    public function create()
    {
        $instructorId = Auth::guard('instructor')->id();
        $courses = Course::where('courseable_type', 'App\Models\Instructor')
            ->where('courseable_id', $instructorId)
            ->get();

        return view('instructor.coupon.create', compact('courses'));
    }

    public function store(Request $request)
    {
        $instructorId = Auth::guard('instructor')->id();

        $request->validate([
            'coupon_name' => 'required|string|max:255|unique:coupons,coupon_name',
            'coupon_discount' => 'required|numeric|min:0|max:100',
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
        ]);

        Coupon::create([
            'coupon_name' => strtoupper($request->coupon_name),
            'coupon_discount' => $request->coupon_discount,
            'coupon_validity' => $request->coupon_validity,
            'instructor_id' => $instructorId,
            'course_id' => $request->course_id,
            'status' => 1, // Actif par défaut
            'created_at' => Carbon::now(),
        ]);

        return redirect()->route('instructor.coupon.index')->with('success', 'Coupon created successfully');
    }

    public function edit(Coupon $coupon)
    {
        $this->authorizeInstructor($coupon);

        $instructorId = Auth::guard('instructor')->id();
        $courses = Course::where('courseable_type', 'App\Models\Instructor')
            ->where('courseable_id', $instructorId)
            ->get();

        return view('instructor.coupon.edit', compact('coupon', 'courses'));
    }

   public function update(Request $request, Coupon $coupon)
{
    $this->authorizeInstructor($coupon);
    $instructorId = Auth::guard('instructor')->id();

    $request->validate([
        'coupon_name' => 'required|string|max:255|unique:coupons,coupon_name,' . $coupon->id,
        'coupon_discount' => 'required|numeric|min:0|max:100',
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
        'status' => 'required|in:0,1', // Ajout de la validation pour status
    ]);

    $coupon->update([
        'coupon_name' => strtoupper($request->coupon_name),
        'coupon_discount' => $request->coupon_discount,
        'coupon_validity' => $request->coupon_validity,
        'course_id' => $request->course_id,
        'status' => $request->status, // Mise à jour du statut
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
        if ($coupon->instructor_id !== Auth::guard('instructor')->id()) {
            abort(403, 'Unauthorized action.');
        }
    }
public function toggleStatus(Coupon $coupon)
    {
        $this->authorizeInstructor($coupon);

        $coupon->status = $coupon->status == 1 ? 0 : 1; // Basculer entre 1 (actif) et 0 (inactif)
        $coupon->updated_at = Carbon::now();
        $coupon->save();

        return redirect()->route('instructor.coupon.index')->with('success', 'Coupon status updated successfully');
    }

}