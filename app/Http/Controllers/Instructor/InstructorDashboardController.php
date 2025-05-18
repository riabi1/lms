<?php

namespace App\Http\Controllers\Instructor;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\Course;
use App\Models\Order;
use Carbon\Carbon;

class InstructorDashboardController extends Controller
{
  public function index()
  {
    $instructor = Auth::guard('instructor')->user();

    // Fetch courses created by the instructor
    $courses = Course::where('courseable_type', 'App\\Models\\Instructor')
      ->where('courseable_id', $instructor->id)
      ->get();

    // Total number of courses
    $courseCount = $courses->count();

    // Total students enrolled (via paid orders)
    $studentCount = Order::whereIn('course_id', $courses->pluck('id'))
      ->where('payment_status', 'paid')
      ->distinct('user_id')
      ->count('user_id');

    // Total revenue from paid orders
    $totalRevenue = Order::whereIn('course_id', $courses->pluck('id'))
      ->where('payment_status', 'paid')
      ->sum('price');

    // Monthly enrollments for the last 6 months
    $monthlyEnrollments = Order::selectRaw("DATE_FORMAT(created_at, '%Y-%m') as month, COUNT(*) as count")
      ->whereIn('course_id', $courses->pluck('id'))
      ->where('payment_status', 'paid')
      ->where('created_at', '>=', Carbon::now()->subMonths(6))
      ->groupBy('month')
      ->orderBy('month')
      ->get();

    // Prepare labels and data for the chart
    $enrollmentLabels = $monthlyEnrollments->pluck('month');
    $enrollmentData = $monthlyEnrollments->pluck('count');

    // Ensure 6 months of data for consistency in the chart
    $labels = collect(range(5, -1, -1))->map(function ($i) {
      return Carbon::now()->subMonths($i)->format('Y-m');
    });

    $data = $labels->map(function ($month) use ($monthlyEnrollments) {
      $enrollment = $monthlyEnrollments->where('month', $month)->first();
      return $enrollment ? $enrollment->count : 0;
    });

    return view('Instructor.index', [
      'courseCount' => $courseCount,
      'studentCount' => $studentCount,
      'totalRevenue' => $totalRevenue,
      'enrollmentLabels' => $labels,
      'enrollmentData' => $data,
    ]);
  }
}
