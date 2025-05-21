<?php

namespace App\Http\Controllers\Instructor;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class InstructorChatController extends Controller
{
    public function handleChat(Request $request)
    {
        try {
            // Get message
            $message = strtolower(trim($request->input('message')));

            // Handle static response first
            if (str_contains($message, 'tip')) {
                return response()->json([
                    'response' => "Add videos or quizzes to make your courses fun! 📖 Need more ideas?"
                ]);
            }

            // Get instructor
            $instructor = Auth::guard('instructor')->user();
            if (!$instructor) {
                return response()->json([
                    'response' => "Please log in to ask about your teaching! 😊"
                ], 401);
            }

            // Get courses with fallback
            try {
                $courses = Course::where('courseable_type', 'App\\Models\\Instructor')
                    ->where('courseable_id', $instructor->id)
                    ->get();
            } catch (\Exception $e) {
                Log::error('Course query error: ' . $e->getMessage());
                $courses = collect([]);
            }
            $courseCount = $courses->count();
            $courseIds = $courses->pluck('id');

            // Get stats with fallback
            $studentCount = 0;
            $totalRevenue = 0;
            if (!$courseIds->isEmpty()) {
                try {
                    $studentCount = Order::whereIn('course_id', $courseIds)
                        ->where('payment_status', 'paid')
                        ->where('currency', 'USD')
                        ->distinct('user_id')
                        ->count('user_id');
                    $totalRevenue = Order::whereIn('course_id', $courseIds)
                        ->where('payment_status', 'paid')
                        ->where('currency', 'USD')
                        ->sum(DB::raw('price - COALESCE(discount_amount, 0)'));
                } catch (\Exception $e) {
                    Log::error('Order query error: ' . $e->getMessage());
                }
            }

            // Log stats for debugging
            Log::debug('Chat stats', [
                'instructor_id' => $instructor->id,
                'course_count' => $courseCount,
                'student_count' => $studentCount,
                'total_revenue' => $totalRevenue,
            ]);

            // Question-response pairs
            if (str_contains($message, 'course')) {
                return response()->json([
                    'response' => $courseCount > 0 
                        ? "You have $courseCount courses! 📚 Want to create another?"
                        : "No courses yet. 🚀 Create one to start teaching!"
                ]);
            }

            if (str_contains($message, 'student')) {
                return response()->json([
                    'response' => "You have $studentCount students! 👥 Share your courses to get more."
                ]);
            }

            if (str_contains($message, 'revenue') || str_contains($message, 'earning')) {
                return response()->json([
                    'response' => "You’ve earned $" . number_format($totalRevenue, 2) . ". 💸 Add more courses to earn more!"
                ]);
            }

            // Fallback
            return response()->json([
                'response' => "Not sure? 🙂 Ask about courses, students, revenue, or tips!"
            ]);
        } catch (\Exception $e) {
            Log::error('InstructorChatController error: ' . $e->getMessage());
            return response()->json([
                'response' => 'Oops, something went wrong. Please try again!'
            ], 500);
        }
    }
}
