<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Order;
use App\Models\User;
use App\Models\Review;
use App\Models\Report;
use App\Models\BlogPost;
use App\Models\Comment;
use App\Models\QuizAttempt;
use App\Models\Coupon;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AdmindashboardController extends Controller
{
    public function index()
    {
        // Get authenticated admin
        $admin = Auth::guard('admin')->user();

        // Redirect if not authenticated
        if (!$admin) {
            Log::warning('Admin dashboard accessed without authentication', [
                'guard' => 'admin',
                'session' => session()->all(),
            ]);
            return redirect()->route('login')->with('error', 'Please log in to access the dashboard.');
        }

        // Log successful access
        Log::info('Admin dashboard accessed', ['admin_id' => $admin->id, 'admin_name' => $admin->name]);

        // User Metrics
        $totalUsers = User::count();
        $activeUsers = User::whereHas('courseProgress', function ($query) {
            $query->where('updated_at', '>=', Carbon::now()->subDays(30));
        })->count();
        $userGrowth = User::where('created_at', '>=', Carbon::now()->subDays(30))->count();

        // Course Metrics
        $totalCourses = Course::count();
        $totalEnrollments = Order::count();
        $averageCompletionRate = DB::table('user_course_progress')
            ->selectRaw('AVG(CASE WHEN completed = 1 THEN 100 ELSE 0 END) as completion_rate')
            ->whereIn('course_id', Course::pluck('id'))
            ->value('completion_rate') ?? 0;
        $averageCourseRating = Review::where('reviewable_type', 'App\\Models\\Course')
            ->avg('rating') ?? 0;

        // Course Completion Rates by Course
        $courseCompletionRates = Course::select(
            'courses.id',
            'courses.course_title',
            DB::raw('AVG(CASE WHEN user_course_progress.completed = 1 THEN 100 ELSE 0 END) as completion_rate')
        )
            ->leftJoin('user_course_progress', 'courses.id', '=', 'user_course_progress.course_id')
            ->groupBy('courses.id', 'courses.course_title')
            ->get();

        // Coupon Metrics
        $totalCoupons = Coupon::count();
        $usedCoupons = Coupon::where('uses', '>', 0)->count();
        $couponUsageRate = $totalCoupons > 0 ? ($usedCoupons / $totalCoupons) * 100 : 0;

        // Revenue Metrics
        $totalRevenue = Order::where('payment_status', 'paid')
            ->where('currency', 'USD')
            ->sum(DB::raw('price - COALESCE(discount_amount, 0)'));
        $revenueLast30Days = Order::where('payment_status', 'paid')
            ->where('currency', 'USD')
            ->where('created_at', '>=', Carbon::now()->subDays(30))
            ->sum(DB::raw('price - COALESCE(discount_amount, 0)'));

        // Log revenue for debugging
        Log::debug('Revenue Metrics', [
            'totalRevenue' => $totalRevenue,
            'revenueLast30Days' => $revenueLast30Days,
        ]);

        // Date range for trends
        $startDate = Carbon::now()->subMonths(6)->startOfMonth();
        $endDate = Carbon::now()->endOfMonth();

        // Enrollment Trend
        $enrollmentTrend = Order::whereBetween('created_at', [$startDate, $endDate])
            ->selectRaw('DATE_FORMAT(created_at, "%Y-%m") as month, COUNT(*) as enrollments')
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        $enrollmentLabels = [];
        $enrollmentData = [];
        $currentDate = $startDate->copy();
        while ($currentDate <= $endDate) {
            $monthKey = $currentDate->format('Y-m');
            $enrollmentLabels[] = $currentDate->format('M Y');
            $enrollmentData[] = $enrollmentTrend->firstWhere('month', $monthKey)->enrollments ?? 0;
            $currentDate->addMonth();
        }

        // Revenue Trend
        $revenueTrend = Order::where('payment_status', 'paid')
            ->where('currency', 'USD')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->selectRaw('DATE_FORMAT(created_at, "%Y-%m") as month, SUM(price - COALESCE(discount_amount, 0)) as revenue')
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        $revenueLabels = [];
        $revenueData = [];
        $currentDate = $startDate->copy();
        while ($currentDate <= $endDate) {
            $monthKey = $currentDate->format('Y-m');
            $revenueLabels[] = $currentDate->format('M Y');
            $revenueData[] = $revenueTrend->firstWhere('month', $monthKey)->revenue ?? 0;
            $currentDate->addMonth();
        }

        // Blog Engagement by Post
        $blogEngagementByPost = BlogPost::select(
            'blog_posts.id',
            'blog_posts.title',
            DB::raw('COUNT(comments.id) as comment_count')
        )
            ->leftJoin('comments', 'blog_posts.id', '=', 'comments.blog_post_id')
            ->groupBy('blog_posts.id', 'blog_posts.title')
            ->orderByDesc('comment_count')
            ->get();

        // Platform Health
        $pendingReports = Report::where('status', 'pending')->count();
        $commonReportCategories = Report::select('report_category_id', DB::raw('COUNT(*) as report_count'))
            ->groupBy('report_category_id')
            ->orderByDesc('report_count')
            ->take(3)
            ->with('reportCategory')
            ->get();

        // Engagement Metrics
        $blogEngagement = Comment::whereIn('blog_post_id', BlogPost::pluck('id'))
            ->count();
        $quizParticipation = QuizAttempt::count();

        // Top Performing Courses
        $topCourses = Course::select(
            'courses.id',
            'courses.course_title',
            DB::raw('COUNT(orders.id) as enrollments'),
            DB::raw('AVG(reviews.rating) as average_rating'),
            DB::raw('SUM(CASE WHEN orders.payment_status = "paid" AND orders.currency = "USD" THEN orders.price - COALESCE(orders.discount_amount, 0) ELSE 0 END) as revenue')
        )
            ->leftJoin('orders', 'courses.id', '=', 'orders.course_id')
            ->leftJoin('reviews', function ($join) {
                $join->on('courses.id', '=', 'reviews.reviewable_id')
                     ->where('reviews.reviewable_type', 'App\\Models\\Course');
            })
            ->groupBy('courses.id', 'courses.course_title')
            ->orderByDesc('enrollments')
            ->take(5)
            ->get();

        // Log data for debugging
        Log::debug('Top Courses', $topCourses->toArray());
        Log::debug('Course Completion Rates', $courseCompletionRates->toArray());
        Log::debug('Blog Engagement by Post', $blogEngagementByPost->toArray());
        Log::debug('Coupon Metrics', [
            'totalCoupons' => $totalCoupons,
            'usedCoupons' => $usedCoupons,
            'couponUsageRate' => $couponUsageRate,
        ]);
        Log::info('Enrollment Trend', $enrollmentTrend->toArray());
        Log::info('Revenue Trend', $revenueTrend->toArray());

        // Actionable Recommendations
        $recommendations = [];
        if ($averageCompletionRate < 50) {
            $recommendations[] = 'Low course completion rates detected. Consider adding more interactive elements like quizzes or live sessions.';
        }
        if ($pendingReports > 0) {
            $recommendations[] = 'Pending reports detected (e.g., Course 10 issue). Prioritize resolving to improve user satisfaction.';
        }
        if ($blogEngagement < 5) {
            $recommendations[] = 'Blog engagement is low (e.g., no comments on AI post). Promote Programming and Design posts on social media.';
        }
        if ($totalCourses > 0 && $totalEnrollments == 0) {
            $recommendations[] = 'No enrollments yet. Offer discounts or free trials to attract students.';
        }
        if ($couponUsageRate < 10) {
            $recommendations[] = 'Coupon usage is low (0% usage). Promote coupons via email campaigns or homepage banners.';
        }
        if ($courseCompletionRates->contains('course_title', 'Blockchain') && $courseCompletionRates->firstWhere('course_title', 'Blockchain')->completion_rate < 50) {
            $recommendations[] = 'Blockchain course has low completion (33%). Simplify content or add interactive elements.';
        }

        // Pass all variables to the view
        return view('admin.index', compact(
            'admin',
            'totalUsers',
            'activeUsers',
            'userGrowth',
            'totalCourses',
            'totalEnrollments',
            'averageCompletionRate',
            'averageCourseRating',
            'totalRevenue',
            'revenueLast30Days',
            'revenueLabels',
            'revenueData',
            'enrollmentLabels',
            'enrollmentData',
            'pendingReports',
            'commonReportCategories',
            'blogEngagement',
            'quizParticipation',
            'topCourses',
            'courseCompletionRates',
            'blogEngagementByPost',
            'totalCoupons',
            'usedCoupons',
            'couponUsageRate',
            'recommendations'
        ));
    }
}