<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Order;
use App\Models\UserCourseProgress;
use App\Models\QuizAttempt;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ChatController extends Controller
{
    public function handleChat(Request $request)
    {
        // Get user and their message
        $user = Auth::user();
        $message = strtolower(trim($request->input('message')));

        // Get user stats
        $courses = Order::where('user_id', $user->id)->with('course')->get();
        $completion = $this->getCompletion();
        $passRate = $this->getQuizPassRate();
        $recommended = $this->getRecommendedCourses($user);

        // Check message for keywords and respond
        if (str_contains($message, 'hours') && str_contains($message, 'complete') && str_contains($message, 'course')) {
            $totalHours = 0;
            foreach ($courses as $course) {
                if (!$course->course) continue;
                $totalLectures = \App\Models\CourseLecture::where('course_id', $course->course->id)->count();
                $doneLectures = UserCourseProgress::where('trackable_id', $user->id)
                    ->where('trackable_type', 'App\Models\User')
                    ->where('course_id', $course->course->id)
                    ->where('completed', 1)
                    ->count();
                $remaining = $totalLectures - $doneLectures;
                $totalHours += $remaining * 0.5; // 30 mins per lecture
            }
            $courseCount = $courses->count();
            if ($courseCount > 0) {
                $avgHours = round($totalHours / $courseCount, 1);
                return response()->json([
                    'response' => "You need about $totalHours hours to finish your $courseCount course(s), around $avgHours hours each. 📚 Study 1-2 hours daily to keep going!"
                ]);
            }
            return response()->json([
                'response' => "You’re not enrolled in any courses yet. 🚀 Check out some courses on your dashboard!"
            ]);
        }

        if (str_contains($message, 'course') || str_contains($message, 'enroll')) {
            $count = $courses->count();
            if ($count > 0) {
                return response()->json([
                    'response' => "You’re enrolled in $count courses! 📚 Want to see them? Check the 'My Courses' page."
                ]);
            }
            return response()->json([
                'response' => "No courses yet. 🚀 Browse recommended courses on your dashboard!"
            ]);
        }

        if (str_contains($message, 'progress') || str_contains($message, 'completion')) {
            return response()->json([
                'response' => "Your completion rate is $completion%. 🌟 Try a lecture today to boost it!"
            ]);
        }

        if (str_contains($message, 'quiz') || str_contains($message, 'test')) {
            if ($passRate > 0) {
                return response()->json([
                    'response' => "Your quiz pass rate is $passRate%. 🎯 Want tips to improve?"
                ]);
            }
            return response()->json([
                'response' => "No quizzes taken yet. 🎯 Try one from your courses!"
            ]);
        }

        if (str_contains($message, 'study tip') || str_contains($message, 'advice')) {
            return response()->json([
                'response' => "Try studying for 25 minutes, then take a 5-minute break (Pomodoro). 📖 Need more tips?"
            ]);
        }

        if (str_contains($message, 'recommend') || str_contains($message, 'course suggestion')) {
            if ($recommended->isNotEmpty()) {
                $titles = $recommended->pluck('course_title')->implode(', ');
                return response()->json([
                    'response' => "I recommend: $titles. 🌟 Check them on your dashboard!"
                ]);
            }
            return response()->json([
                'response' => "No recommendations yet. 😊 Browse the course catalog!"
            ]);
        }

        if (str_contains($message, 'how to improve') || str_contains($message, 'better grades')) {
            return response()->json([
                'response' => "Review course materials, take notes, and practice quizzes. 📝 Need help with a course?"
            ]);
        }

        if (str_contains($message, 'deadline') || str_contains($message, 'due date')) {
            return response()->json([
                'response' => "Courses are self-paced, but check your dashboard for quiz schedules. 📅 Need course-specific help?"
            ]);
        }

        // Fallback for unknown questions
        return response()->json([
            'response' => "Not sure what you mean. 😊 Ask about courses, progress, quizzes, or study tips!"
        ]);
    }

    private function getCompletion()
    {
        $user = Auth::user();
        $progress = Order::where('user_id', $user->id)
            ->join('courses', 'orders.course_id', '=', 'courses.id')
            ->get()
            ->map(function ($course) use ($user) {
                $total = \App\Models\CourseLecture::where('course_id', $course->id)->count();
                $done = UserCourseProgress::where('trackable_id', $user->id)
                    ->where('trackable_type', 'App\Models\User')
                    ->where('course_id', $course->id)
                    ->where('completed', 1)
                    ->count();
                return $total > 0 ? round(($done / $total) * 100, 2) : 0;
            })->toArray();

        return !empty($progress) ? round(array_sum($progress) / count($progress), 2) : 0;
    }

    private function getQuizPassRate()
    {
        $user = Auth::user();
        $results = QuizAttempt::where('user_id', $user->id)
            ->selectRaw('passed, COUNT(*) as count')
            ->groupBy('passed')
            ->get()
            ->mapWithKeys(function ($item) {
                return [$item->passed ? 'Pass' : 'Fail' => $item->count];
            })->toArray();

        $total = array_sum($results);
        return $total > 0 && !empty($results['Pass']) ? round(($results['Pass'] / $total) * 100, 2) : 0;
    }

    private function getRecommendedCourses($user)
    {
        $prefs = is_string($user->preference) ? json_decode($user->preference, true) : [];
        $prefs = is_array($prefs) ? $prefs : [];

        if (empty($prefs)) {
            return Course::where('status', 1)
                ->where('featured', 1)
                ->select('id', 'course_title')
                ->take(3)
                ->get();
        }

        return Course::where('status', 1)
            ->whereIn('subcategory_id', function ($query) use ($prefs) {
                $query->select('id')->from('sub_categories')->whereIn('category_id', $prefs);
            })
            ->select('id', 'course_title')
            ->take(3)
            ->get();
    }
}