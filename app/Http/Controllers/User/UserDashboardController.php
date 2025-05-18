<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Order;
use App\Models\UserCourseProgress;
use App\Models\QuizAttempt;
use App\Models\Wishlist;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class UserDashboardController extends Controller
{
  /**
   * Display the user dashboard.
   *
   * @return \Illuminate\View\View
   */
  public function index()
  {
    return view('User.index');
  }

  /**
   * Get course enrollment trends data for the authenticated user.
   *
   * @return \Illuminate\Http\JsonResponse
   */
  public function getEnrollmentTrendsData(Request $request)
  {
    $user = Auth::user();

    // Fetch enrollments for the last 6 months
    $enrollments = Order::where('user_id', $user->id)
      ->where('created_at', '>=', Carbon::now()->subMonths(6))
      ->selectRaw('DATE_FORMAT(created_at, "%Y-%m") as month, COUNT(*) as count')
      ->groupBy('month')
      ->orderBy('month')
      ->get()
      ->mapWithKeys(function ($item) {
        return [$item->month => $item->count];
      })->toArray();

    // Ensure all months in the last 6 months are included
    $months = [];
    for ($i = 5; $i >= 0; $i--) {
      $month = Carbon::now()->subMonths($i)->format('Y-m');
      $months[$month] = $enrollments[$month] ?? 0;
    }

    return response()->json([
      'labels' => array_keys($months),
      'data' => array_values($months),
    ]);
  }

  /**
   * Get course completion progress data for the authenticated user.
   *
   * @return \Illuminate\Http\JsonResponse
   */
  public function getCompletionData(Request $request)
  {
    $user = Auth::user();

    // Fetch completion progress for each course
    $progress = UserCourseProgress::where('trackable_id', $user->id)
      ->where('trackable_type', 'App\\Models\\User')
      ->select('course_id')
      ->with(['course' => function ($query) {
        $query->select('id', 'course_title');
      }])
      ->groupBy('course_id')
      ->get()
      ->map(function ($item) {
        $totalLectures = \App\Models\CourseLecture::where('course_id', $item->course_id)->count();
        $completedLectures = \App\Models\UserCourseProgress::where('trackable_id', Auth::user()->id)
          ->where('trackable_type', 'App\\Models\\User')
          ->where('course_id', $item->course_id)
          ->where('completed', 1)
          ->count();
        $progressPercentage = $totalLectures > 0 ? round(($completedLectures / $totalLectures) * 100, 2) : 0;
        return [
          'course_title' => $item->course->course_title,
          'progress' => $progressPercentage,
        ];
      })->toArray();

    return response()->json([
      'labels' => array_column($progress, 'course_title'),
      'data' => array_column($progress, 'progress'),
    ]);
  }

  /**
   * Get quiz performance data for the authenticated user.
   *
   * @return \Illuminate\Http\JsonResponse
   */
  public function getQuizPerformanceData(Request $request)
  {
    $user = Auth::user();

    // Fetch quiz pass/fail distribution
    $quizResults = QuizAttempt::where('user_id', $user->id)
      ->selectRaw('passed as result, COUNT(*) as count')
      ->groupBy('result')
      ->get()
      ->mapWithKeys(function ($item) {
        return [$item->result ? 'Pass' : 'Fail' => $item->count];
      })->toArray();

    // Ensure both Pass and Fail are included
    $results = [
      'Pass' => $quizResults['Pass'] ?? 0,
      'Fail' => $quizResults['Fail'] ?? 0,
    ];

    return response()->json([
      'labels' => array_keys($results),
      'data' => array_values($results),
    ]);
  }

  /**
   * Get wishlist trends data for the authenticated user.
   *
   * @return \Illuminate\Http\JsonResponse
   */
  public function getWishlistData(Request $request)
  {
    $user = Auth::user();

    // Fetch wishlist additions for the last 6 months
    $wishlists = Wishlist::where('user_id', $user->id)
      ->where('created_at', '>=', Carbon::now()->subMonths(6))
      ->selectRaw('DATE_FORMAT(created_at, "%Y-%m") as month, COUNT(*) as count')
      ->groupBy('month')
      ->orderBy('month')
      ->get()
      ->mapWithKeys(function ($item) {
        return [$item->month => $item->count];
      })->toArray();

    // Ensure all months in the last 6 months are included
    $months = [];
    for ($i = 5; $i >= 0; $i--) {
      $month = Carbon::now()->subMonths($i)->format('Y-m');
      $months[$month] = $wishlists[$month] ?? 0;
    }

    return response()->json([
      'labels' => array_keys($months),
      'data' => array_values($months),
    ]);
  }
}
