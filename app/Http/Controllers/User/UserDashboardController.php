<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Order;
use App\Models\UserCourseProgress;
use App\Models\QuizAttempt;
use App\Models\Wishlist;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class UserDashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        // Fetch enrolled courses with course and quiz_attempts relationships
        $enrolledCourses = Order::where('user_id', $user->id)
            ->with([
                'course' => function ($query) {
                    $query->select('id', 'course_title', 'course_image', 'course_name_slug')
                          ->with('quizAttempts');
                }
            ])
            ->get()
            ->map(function ($order) use ($user) {
                $course = $order->course;
                if (!$course) {
                    Log::warning("Order {$order->id} has no associated course for user {$user->id}");
                    return $order;
                }
                $totalLectures = \App\Models\CourseLecture::where('course_id', $course->id)->count();
                $completedLectures = UserCourseProgress::where('trackable_id', $user->id)
                    ->where('trackable_type', 'App\Models\User')
                    ->where('course_id', $course->id)
                    ->where('completed', 1)
                    ->count();
                $course->completion_percentage = $totalLectures > 0
                    ? round(($completedLectures / $totalLectures) * 100, 2)
                    : 0;
                Log::debug("Course {$course->id} for user {$user->id}: {$completedLectures}/{$totalLectures} lectures, {$course->completion_percentage}%");
                return $order;
            });

        // Calculate overall completion rate
        $completionResponse = $this->getCompletionData(new Request());
        $completionData = $completionResponse->getData(true);
        Log::debug("Completion data for user {$user->id}", ['completionData' => $completionData]);

        $overallCompletion = 0;
        if (!empty($completionData['data']) && is_array($completionData['data']) && count($completionData['data']) > 0) {
            $overallCompletion = round(array_sum($completionData['data']) / count($completionData['data']), 2);
        }
        Log::debug("Overall completion for user {$user->id}: {$overallCompletion}%");

        // Quiz performance summary
        $quizData = $this->getQuizPerformanceData(new Request())->getData(true);
        $totalAttempts = array_sum($quizData['data']);
        $passRate = $totalAttempts > 0 && !empty($quizData['data'][0])
            ? round(($quizData['data'][0] / $totalAttempts) * 100, 2)
            : 0;

        // Personalized message
        $message = $this->generateMotivationalMessage($overallCompletion, $passRate, $enrolledCourses->count());

        // Recommended courses based on preferences
        $recommendedCourses = $this->getRecommendedCourses($user) ?? collect([]);

        return view('User.index', compact('enrolledCourses', 'overallCompletion', 'passRate', 'message', 'recommendedCourses'));
    }

    public function getCompletionData(Request $request)
    {
        $user = Auth::user();
        $progress = Order::where('user_id', $user->id)
            ->join('courses', 'orders.course_id', '=', 'courses.id')
            ->select('courses.id', 'courses.course_title')
            ->get()
            ->map(function ($course) use ($user) {
                $totalLectures = \App\Models\CourseLecture::where('course_id', $course->id)->count();
                $completedLectures = UserCourseProgress::where('trackable_id', $user->id)
                    ->where('trackable_type', 'App\Models\User')
                    ->where('course_id', $course->id)
                    ->where('completed', 1)
                    ->count();
                $progressPercentage = $totalLectures > 0 ? round(($completedLectures / $totalLectures) * 100, 2) : 0;
                Log::debug("Completion for course {$course->id}: {$completedLectures}/{$totalLectures} lectures, {$progressPercentage}%");
                return [
                    'course_title' => $course->course_title,
                    'progress' => $progressPercentage,
                ];
            })->filter()->toArray();

        return response()->json([
            'labels' => array_column($progress, 'course_title'),
            'data' => array_column($progress, 'progress'),
        ]);
    }

    public function getQuizPerformanceData(Request $request)
    {
        $user = Auth::user();
        $quizResults = QuizAttempt::where('user_id', $user->id)
            ->selectRaw('passed as result, COUNT(*) as count')
            ->groupBy('result')
            ->get()
            ->mapWithKeys(function ($item) {
                return [$item->result ? 'Pass' : 'Fail' => $item->count];
            })->toArray();

        $results = [
            'Pass' => $quizResults['Pass'] ?? 0,
            'Fail' => $quizResults['Fail'] ?? 0,
        ];

        return response()->json([
            'labels' => array_keys($results),
            'data' => array_values($results),
        ]);
    }

    public function getEnrollmentTrendsData(Request $request)
    {
        $user = Auth::user();
        $enrollments = Order::where('user_id', $user->id)
            ->where('created_at', '>=', Carbon::now()->subMonths(6))
            ->selectRaw('DATE_FORMAT(created_at, "%Y-%m") as month, COUNT(*) as count')
            ->groupBy('month')
            ->orderBy('month')
            ->get()
            ->mapWithKeys(function ($item) {
                return [$item->month => $item->count];
            })->toArray();

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

    public function getWishlistData(Request $request)
    {
        $user = Auth::user();
        $wishlists = Wishlist::where('trackable_id', $user->id)
            ->where('trackable_type', 'App\Models\User')
            ->where('created_at', '>=', Carbon::now()->subMonths(6))
            ->selectRaw('DATE_FORMAT(created_at, "%Y-%m") as month, COUNT(*) as count')
            ->groupBy('month')
            ->orderBy('month')
            ->get()
            ->mapWithKeys(function ($item) {
                return [$item->month => $item->count];
            })->toArray();

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

    public function getCategoryEngagementData(Request $request)
    {
        $user = Auth::user();
        $categories = Order::where('user_id', $user->id)
            ->join('courses', 'orders.course_id', '=', 'courses.id')
            ->join('sub_categories', 'courses.subcategory_id', '=', 'sub_categories.id')
            ->join('categories', 'sub_categories.category_id', '=', 'categories.id')
            ->selectRaw('categories.category_name as category, COUNT(*) as count')
            ->groupBy('category')
            ->get()
            ->mapWithKeys(function ($item) {
                return [$item->category => $item->count];
            })->toArray();

        return response()->json([
            'labels' => array_keys($categories),
            'data' => array_values($categories),
        ]);
    }

    private function generateMotivationalMessage($completionRate, $passRate, $enrollmentCount)
    {
        if ($enrollmentCount === 0) {
            return "🌟 Welcome! Kick off your learning journey by enrolling in a course today! 🚀";
        }
        if ($completionRate >= 75) {
            return "🎉 You're crushing it with a $completionRate% completion rate! Keep shining, superstar! 🌟";
        } elseif ($completionRate >= 50) {
            return "👍 Awesome progress at $completionRate%! Spend 30 minutes daily to finish strong! 💪";
        } elseif ($completionRate > 0) {
            return "🚶 You're at $completionRate% completion. Every step counts—watch a lecture today! 📚";
        }
        if ($passRate >= 75) {
            return "🥳 Wow, a $passRate% quiz pass rate! You're acing it—keep up the great work! 🎯";
        } elseif ($passRate < 50 && $passRate > 0) {
            return "📝 Your quiz pass rate is $passRate%. Review course materials and try again—you've got this! 💡";
        }
        if ($enrollmentCount > 0) {
            return "🎓 You've enrolled in $enrollmentCount courses! Dive back in and continue your learning adventure! 🌍";
        }
        return "😊 Ready to learn? Explore new courses and make progress today! 📖";
    }

    private function getRecommendedCourses($user)
    {
        $preferences = is_string($user->preference) ? json_decode($user->preference, true) : [];
        $preferences = is_array($preferences) ? $preferences : [];

        if (empty($preferences)) {
            return Course::where('status', 1)
                ->where('featured', 1)
                ->select('id', 'course_title', 'course_name_slug', 'course_image', 'description')
                ->with('instructor')
                ->take(3)
                ->get() ?? collect([]);
        }

        return Course::where('status', 1)
            ->whereIn('subcategory_id', function ($query) use ($preferences) {
                $query->select('id')
                    ->from('sub_categories')
                    ->whereIn('category_id', $preferences);
            })
            ->select('id', 'course_title', 'course_name_slug', 'course_image', 'description')
            ->with('instructor')
            ->take(3)
            ->get() ?? collect([]);
    }
}