<?php

namespace App\Http\Controllers\Instructor;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\Course;
use App\Models\Order;
use App\Models\Review;
use App\Models\UserCourseProgress;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class InstructorDashboardController extends Controller
{
    public function index()
    {
        $instructor = Auth::guard('instructor')->user();

        // Fetch courses created by the instructor
        $courses = Course::where('courseable_type', 'App\\Models\\Instructor')
            ->where('courseable_id', $instructor->id)
            ->with(['reviews', 'orders'])
            ->get();

        // Total number of courses
        $courseCount = $courses->count();

        // Total students enrolled (distinct users with paid orders)
        $studentCount = Order::whereIn('course_id', $courses->pluck('id'))
            ->where('payment_status', 'paid')
            ->where('currency', 'USD')
            ->distinct('user_id')
            ->count('user_id');

        // Total revenue from paid orders
        $totalRevenue = Order::whereIn('course_id', $courses->pluck('id'))
            ->where('payment_status', 'paid')
            ->where('currency', 'USD')
            ->sum(DB::raw('price - COALESCE(discount_amount, 0)'));

        // Log revenue for debugging
        Log::debug('Instructor Revenue', [
            'instructor_id' => $instructor->id,
            'totalRevenue' => $totalRevenue,
        ]);

        // Monthly enrollments for the last 6 months
        $monthlyEnrollments = Order::selectRaw("DATE_FORMAT(created_at, '%Y-%m') as month, COUNT(*) as count")
            ->whereIn('course_id', $courses->pluck('id'))
            ->where('payment_status', 'paid')
            ->where('currency', 'USD')
            ->where('created_at', '>=', Carbon::now()->subMonths(6))
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        // Prepare enrollment chart data
        $startDate = Carbon::now()->subMonths(6)->startOfMonth();
        $endDate = Carbon::now()->endOfMonth();
        $labels = [];
        $enrollmentData = [];
        $currentDate = $startDate->copy();
        while ($currentDate <= $endDate) {
            $monthKey = $currentDate->format('Y-m');
            $labels[] = $currentDate->format('M Y');
            $enrollment = $monthlyEnrollments->where('month', $monthKey)->first();
            $enrollmentData[] = $enrollment ? $enrollment->count : 0;
            $currentDate->addMonth();
        }

        // Course performance: completion rates and average ratings
        $coursePerformance = $courses->map(function ($course) {
            $totalLectures = $course->lectures()->count();
            $completedLectures = UserCourseProgress::where('course_id', $course->id)
                ->where('completed', 1)
                ->count();
            $completionRate = $totalLectures > 0 ? round(($completedLectures / $totalLectures) * 100, 1) : 0;

            $averageRating = $course->reviews->avg('rating') ?? 0;
            $enrollments = $course->orders()->where('payment_status', 'paid')->where('currency', 'USD')->count();
            $revenue = $course->orders()->where('payment_status', 'paid')->where('currency', 'USD')
                ->sum(DB::raw('price - COALESCE(discount_amount, 0)'));

            return [
                'id' => $course->id,
                'title' => $course->course_title,
                'completion_rate' => $completionRate,
                'average_rating' => round($averageRating, 1),
                'enrollments' => $enrollments,
                'revenue' => $revenue,
            ];
        });

        // Top-performing courses (by enrollments)
        $topCourses = $coursePerformance->sortByDesc('enrollments')->take(3);

        // Revenue breakdown by course for chart
        $revenueLabels = $coursePerformance->pluck('title');
        $revenueData = $coursePerformance->pluck('revenue');

        // Engagement insights: average quiz scores and comments
        $averageQuizScore = \App\Models\QuizAttempt::whereIn('quiz_id', \App\Models\Quiz::whereIn('course_id', $courses->pluck('id'))->pluck('id'))
            ->avg('score') ?? 0;

        $commentCount = \App\Models\Comment::whereIn('blog_post_id', \App\Models\BlogPost::where('instructor_id', $instructor->id)->pluck('id'))
            ->count();

        // Average course rating (average of all reviews across all courses)
        $averageCourseRating = $courseCount > 0 ? round($coursePerformance->avg('average_rating'), 1) : 0;

        // Personalized greeting
        if ($courseCount > 5) {
            $greeting = "Wow, {$instructor->name}! You're a teaching superstar with {$courseCount} courses! 🌟";
        } elseif ($courseCount > 0) {
            $greeting = "Great job, {$instructor->name}! Your {$courseCount} courses are inspiring {$studentCount} learners! 🚀";
        } else {
            $greeting = "Hey {$instructor->name}, let's kickstart your teaching journey! 🎉";
        }

        // Engagement trend: Average students per course
        $engagementTrend = $courseCount > 0 ? round($studentCount / $courseCount, 1) : 0;
        if ($engagementTrend > 50) {
            $trendMessage = "Your courses are thriving with {$engagementTrend} students per course on average! Keep shining! 📈";
        } elseif ($engagementTrend > 0) {
            $trendMessage = "You're averaging {$engagementTrend} students per course. Add interactive elements to boost engagement! 💡";
        } else {
            $trendMessage = "Create your first course to start building your student community! 🌍";
        }

        // Actionable recommendations
        $recommendations = [];
        if ($courseCount == 0) {
            $recommendations[] = "Launch your first course to share your expertise with the world! 🎓";
        }
        if ($averageQuizScore < 70 && $averageQuizScore > 0) {
            $recommendations[] = "Simplify quiz questions or add practice materials to help students score higher. 🧠";
        }
        if ($commentCount < 5 && $courseCount > 0) {
            $recommendations[] = "Write a blog post to engage your audience and drive course interest. ✍️";
        }
        if ($averageCourseRating < 4.0 && $courseCount > 0) {
            $recommendations[] = "Improve your {$averageCourseRating}/5 average course rating by addressing student feedback. ⭐";
        }

        return view('Instructor.index', [
            'instructor' => $instructor,
            'courseCount' => $courseCount,
            'studentCount' => $studentCount,
            'totalRevenue' => $totalRevenue,
            'averageQuizScore' => round($averageQuizScore, 1),
            'commentCount' => $commentCount,
            'averageCourseRating' => $averageCourseRating,
            'greeting' => $greeting,
            'trendMessage' => $trendMessage,
            'enrollmentLabels' => $labels,
            'enrollmentData' => $enrollmentData,
            'coursePerformance' => $coursePerformance,
            'topCourses' => $topCourses,
            'revenueLabels' => $revenueLabels,
            'revenueData' => $revenueData,
            'recommendations' => $recommendations,
        ]);
    }
}