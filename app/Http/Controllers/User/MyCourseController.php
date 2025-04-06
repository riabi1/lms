<?php

namespace App\Http\Controllers\User;

use App\Models\Order;
use Illuminate\Http\Request;
use App\Models\UserCourseProgress;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\Course;
use App\Models\Review;
use App\Models\QuizAttempt;
use App\Models\Quiz;
use App\Models\Question;
use App\Notifications\NewQuestionNotification;
use Illuminate\Support\Facades\Storage;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class MyCourseController extends Controller
{
    public function myCourses()
    {
        $orders = auth()->user()->orders()
            ->with(['course.sections.lectures', 'course.courseable'])
            ->where('payment_status', 'paid')
            ->get();

        foreach ($orders as $order) {
            $order->progress = auth()->user()->courseProgress()
                ->where('course_id', $order->course_id)
                ->pluck('completed', 'lecture_id')
                ->toArray();
        }

        return view('User.mycourses.my_courses', compact('orders'));
    }

    public function submitRating(Request $request, $courseId)
    {
        $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string|max:500',
        ]);

        $userId = Auth::id();
        $course = Course::findOrFail($courseId);

        $hasPurchased = Order::where('user_id', $userId)
            ->where('course_id', $courseId)
            ->where('payment_status', 'paid')
            ->exists();

        if (!$hasPurchased) {
            return response()->json(['message' => 'You must purchase this course to rate it.'], 403);
        }

        $existingReview = Review::where('user_id', $userId)
            ->where('reviewable_type', 'App\\Models\\Course')
            ->where('reviewable_id', $courseId)
            ->first();

        if ($existingReview) {
            return response()->json(['message' => 'You have already rated this course.'], 422);
        }

        Review::create([
            'reviewable_type' => 'App\\Models\\Course',
            'reviewable_id' => $courseId,
            'user_id' => $userId,
            'rating' => $request->rating,
            'comment' => $request->comment ?? '',
            'status' => 0,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Rating submitted successfully'
        ]);
    }

    public function markLectureCompleted(Request $request, $courseId)
    {
        $request->validate([
            'lecture_id' => 'required|exists:course_lectures,id',
            'completed' => 'required|boolean',
        ]);

        $user = Auth::user();
        $course = Course::findOrFail($courseId);

        $hasPurchased = Order::where('user_id', $user->id)
            ->where('course_id', $courseId)
            ->where('payment_status', 'paid')
            ->exists();

        if (!$hasPurchased) {
            return response()->json(['success' => false, 'message' => 'You must purchase this course to mark progress.'], 403);
        }

        $lectureExistsInCourse = $course->sections->flatMap->lectures->contains('id', $request->lecture_id);
        if (!$lectureExistsInCourse) {
            return response()->json(['success' => false, 'message' => 'This lecture does not belong to the course.'], 422);
        }

        UserCourseProgress::updateOrCreate(
            [
                'trackable_type' => 'App\\Models\\User',
                'trackable_id' => $user->id,
                'course_id' => $courseId,
                'lecture_id' => $request->lecture_id,
            ],
            [
                'completed' => $request->completed,
                'completed_at' => $request->completed ? now() : null,
            ]
        );

        $totalLectures = $course->sections->flatMap->lectures->count();
        $completedLectures = UserCourseProgress::where('trackable_type', 'App\\Models\\User')
            ->where('trackable_id', $user->id)
            ->where('course_id', $courseId)
            ->where('completed', 1)
            ->count();
        $progressPercentage = $totalLectures > 0 ? round(($completedLectures / $totalLectures) * 100) : 0;

        return response()->json([
            'success' => true,
            'message' => $request->completed ? 'Lecture marked as completed.' : 'Lecture unmarked.',
            'progress' => $progressPercentage,
        ]);
    }

    public function startLearning($courseId, $slug)
{
    $course = Course::with([
        'sections.lectures',
        'quizzes.questions',
        'courseable',
        'questions.user'
    ])->findOrFail($courseId);

    // Debug: Check if instructor_id exists
    if (!$course->instructor_id) {
        \Log::warning("Course ID $courseId has no instructor_id");
    }

    if (Str::slug($course->course_name) !== $slug) {
        return redirect()->route('course.start', [
            'courseId' => $courseId,
            'slug' => Str::slug($course->course_name)
        ]);
    }

    $user = Auth::user();

    $hasPurchased = Order::where('user_id', $user->id)
        ->where('course_id', $courseId)
        ->where('payment_status', 'paid')
        ->exists();

    if (!$hasPurchased) {
        abort(403, 'You do not have access to this course.');
    }

    $progress = $user->courseProgress()
        ->where('course_id', $courseId)
        ->pluck('completed', 'lecture_id')
        ->toArray();

    $totalLectures = $course->sections->flatMap->lectures->count();
    $completedLectures = array_filter($progress, fn($completed) => $completed == 1);
    $progressPercentage = $totalLectures > 0 ? round((count($completedLectures) / $totalLectures) * 100) : 0;

    $quizAttempts = QuizAttempt::where('user_id', $user->id)
        ->whereIn('quiz_id', $course->quizzes->pluck('id'))
        ->get();

    return view('User.mycourses.learn_course', compact(
        'course',
        'progress',
        'progressPercentage',
        'quizAttempts'
    ));
}

    public function submitQuiz(Request $request, $courseId, $quizId)
    {
        $user = Auth::user();
        $quiz = Quiz::with('questions')->findOrFail($quizId);
        $course = Course::findOrFail($courseId);

        if ($quiz->course_id != $courseId) {
            abort(403, 'This quiz does not belong to the course.');
        }

        $hasPurchased = Order::where('user_id', $user->id)
            ->where('course_id', $courseId)
            ->where('payment_status', 'paid')
            ->exists();

        if (!$hasPurchased) {
            abort(403, 'You do not have access to this course.');
        }

        $attemptCount = QuizAttempt::where('user_id', $user->id)
            ->where('quiz_id', $quizId)
            ->count();

        if ($attemptCount >= 3) {
            $lastAttempt = QuizAttempt::where('user_id', $user->id)
                ->where('quiz_id', $quizId)
                ->orderBy('completed_at', 'desc')
                ->first();

            $waitUntil = $lastAttempt->completed_at->addMinute();
            if (Carbon::now()->lessThan($waitUntil)) {
                $secondsLeft = Carbon::now()->diffInSeconds($waitUntil);
                return redirect()->route('course.start', [
                    'courseId' => $courseId,
                    'slug' => Str::slug($course->course_name)
                ])->with('error', "You have reached the maximum of 3 attempts. Please wait $secondsLeft seconds before trying again.");
            }
        }

        $answers = $request->input('answers', []);
        $correctAnswers = 0;
        $totalQuestions = $quiz->questions->count();

        foreach ($quiz->questions as $question) {
            $userAnswer = $answers[$question->id] ?? null;
            if ($userAnswer && $userAnswer === $question->correct_answer) {
                $correctAnswers++;
            }
        }

        $score = $totalQuestions > 0 ? round(($correctAnswers / $totalQuestions) * 100) : 0;
        $passed = $score >= 70;

        QuizAttempt::create([
            'user_id' => $user->id,
            'quiz_id' => $quiz->id,
            'score' => $score,
            'passed' => $passed,
            'completed_at' => now(),
        ]);

        $message = $passed 
            ? "Quiz passed! Your score: $score%. You can now download your certificate if all requirements are met."
            : "Quiz failed. Your score: $score%. You have " . (2 - $attemptCount) . " attempts remaining.";

        return redirect()->route('course.start', [
            'courseId' => $courseId,
            'slug' => Str::slug($course->course_name)
        ])->with('success', $message);
    }

    public function downloadCertificate($courseId)
    {
        $course = Course::with(['quizzes', 'sections.lectures'])->findOrFail($courseId);
        $user = Auth::user();

        $hasPurchased = Order::where('user_id', $user->id)
            ->where('course_id', $courseId)
            ->where('payment_status', 'paid')
            ->exists();

        if (!$hasPurchased) {
            return redirect()->route('course.start', [
                'courseId' => $courseId,
                'slug' => Str::slug($course->course_name)
            ])->with('error', 'You must purchase this course to download a certificate.');
        }

        if ($course->certificate !== 'yes') {
            return redirect()->route('course.start', [
                'courseId' => $courseId,
                'slug' => Str::slug($course->course_name)
            ])->with('error', 'This course does not offer a certificate.');
        }

        $totalLectures = $course->sections->flatMap->lectures->count();
        $completedLectures = $user->courseProgress()
            ->where('course_id', $courseId)
            ->where('completed', 1)
            ->count();
        $progressPercentage = $totalLectures > 0 ? round(($completedLectures / $totalLectures) * 100) : 0;

        if ($progressPercentage < 100) {
            return redirect()->route('course.start', [
                'courseId' => $courseId,
                'slug' => Str::slug($course->course_name)
            ])->with('error', 'You must complete all lectures to download the certificate.');
        }

        if ($course->quizzes->isNotEmpty()) {
            $quizAttempts = QuizAttempt::where('user_id', $user->id)
                ->whereIn('quiz_id', $course->quizzes->pluck('id'))
                ->get();

            $allQuizzesPassed = $course->quizzes->every(function ($quiz) use ($quizAttempts) {
                return $quizAttempts->where('quiz_id', $quiz->id)->where('passed', true)->isNotEmpty();
            });

            if (!$allQuizzesPassed) {
                return redirect()->route('course.start', [
                    'courseId' => $courseId,
                    'slug' => Str::slug($course->course_name)
                ])->with('error', 'You must pass all quizzes to download the certificate.');
            }
        }

        $certificateNumber = 'EDAA-' . strtoupper(uniqid()) . '-' . $courseId . '-' . $user->id;
        $data = [
            'course_name' => $course->course_name,
            'user_name' => $user->name,
            'completion_date' => Carbon::now()->format('F d, Y'),
            'certificate_number' => $certificateNumber,
        ];

        $pdf = Pdf::loadView('User.mycourses.certificate', $data)->setPaper('a4', 'portrait');
        return $pdf->download("certificate_{$courseId}_{$user->id}.pdf");
    }



  }