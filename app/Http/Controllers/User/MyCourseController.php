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
use App\Models\CourseQuestion;
use App\Models\Answer;
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
    'questions.user',
    'questions.answers.instructor',
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

            if (!$lastAttempt || !$lastAttempt->completed_at) {
                \Log::warning('Invalid last attempt or completed_at', [
                    'user_id' => $user->id,
                    'quiz_id' => $quizId,
                    'last_attempt' => $lastAttempt ? $lastAttempt->toArray() : null
                ]);
                return redirect()->route('course.start', [
                    'courseId' => $courseId,
                    'slug' => Str::slug($course->course_name)
                ])->with('error', 'Unable to process quiz attempts. Please contact support.');
            }

            $waitUntil = Carbon::parse($lastAttempt->completed_at)->addMinute();
            \Log::info('Quiz Retry Check', [
                'user_id' => $user->id,
                'quiz_id' => $quizId,
                'completed_at' => $lastAttempt->completed_at->toDateTimeString(),
                'wait_until' => $waitUntil->toDateTimeString(),
                'current_time' => Carbon::now()->toDateTimeString(),
                'attempt_count' => $attemptCount
            ]);

            if (Carbon::now()->lessThan($waitUntil)) {
                $secondsLeft = Carbon::now()->diffInSeconds($waitUntil);
                $retryTimeDisplay = $waitUntil->setTimezone('Africa/Lagos')->toDateTimeString();
                return redirect()->route('course.start', [
                    'courseId' => $courseId,
                    'slug' => Str::slug($course->course_name)
                ])->with('error', "You have reached the maximum of 3 attempts. Please wait until $retryTimeDisplay to try again.");
            } else {
                try {
                    \Log::info('Attempting to reset quiz attempts', ['user_id' => $user->id, 'quiz_id' => $quizId]);
                    QuizAttempt::where('user_id', $user->id)
                        ->where('quiz_id', $quizId)
                        ->delete();
                    $attemptCount = 0;
                    \Log::info('Quiz attempts reset successfully', ['user_id' => $user->id, 'quiz_id' => $quizId]);
                } catch (QueryException $e) {
                    \Log::error('Failed to reset quiz attempts', [
                        'user_id' => $user->id,
                        'quiz_id' => $quizId,
                        'error' => $e->getMessage()
                    ]);
                    return redirect()->route('course.start', [
                        'courseId' => $courseId,
                        'slug' => Str::slug($course->course_name)
                    ])->with('error', 'Failed to reset quiz attempts. Please contact support.');
                }
            }
        }

        $answers = $request->input('answers', []);
        $correctAnswers = 0;
        $totalQuestions = $quiz->questions->count();

        foreach ($quiz->questions as $question) {
            $userAnswerKey = $answers[$question->id] ?? null;
            $options = is_string($question->options) ? json_decode($question->options, true) : $question->options;
            $userAnswerValue = ($userAnswerKey !== null && isset($options[$userAnswerKey])) ? $options[$userAnswerKey] : null;
            \Log::debug('Answer Validation', [
                'question_id' => $question->id,
                'user_answer_key' => $userAnswerKey,
                'user_answer_value' => $userAnswerValue,
                'correct_answer' => $question->correct_answer
            ]);
            if ($userAnswerValue && $userAnswerValue === $question->correct_answer) {
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
            'completed_at' => Carbon::now(),
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
    public function submitQuestion(Request $request, $courseId)
    {
        $request->validate([
            'question_text' => 'required|string|max:1000',
        ]);

        $user = Auth::user();
        $course = Course::findOrFail($courseId);

        // Check if user has purchased the course
        $hasPurchased = Order::where('user_id', $user->id)
            ->where('course_id', $courseId)
            ->where('payment_status', 'paid')
            ->exists();

        if (!$hasPurchased) {
            return response()->json(['success' => false, 'message' => 'You must purchase this course to ask a question.'], 403);
        }

        // Create the question
        $question = CourseQuestion::create([
            'course_id' => $courseId,
            'user_id' => $user->id,
            'question_text' => $request->question_text,
            'status' => 'pending',
        ]);

        // Notify the instructor
        $instructor = $course->courseable;
        if ($instructor) {
            $instructor->notify(new NewQuestionNotification($question, $course, $user));
        }

        return response()->json([
            'success' => true,
            'message' => 'Question submitted successfully.',
            'question' => [
                'id' => $question->id,
                'question_text' => $question->question_text,
                'user_name' => $user->name,
                'created_at' => $question->created_at->format('F j, Y, H:i'),
                'answers' => [],
            ],
        ]);
    }


    public function updateQuestion(Request $request, $courseId)
    {
        $request->validate([
            'question_id' => 'required|exists:course_questions,id',
            'question_text' => 'required|string|max:1000',
        ]);

        $user = Auth::user();
        $question = CourseQuestion::findOrFail($request->question_id);

        // Verify user owns the question
        if ($question->user_id !== $user->id) {
            return response()->json(['success' => false, 'message' => 'Unauthorized action.'], 403);
        }

        // Check if the question belongs to the course
        if ($question->course_id !== (int)$courseId) {
            return response()->json(['success' => false, 'message' => 'Invalid course.'], 403);
        }

        // Check if user has purchased the course
        $hasPurchased = Order::where('user_id', $user->id)
            ->where('course_id', $courseId)
            ->where('payment_status', 'paid')
            ->exists();

        if (!$hasPurchased) {
            return response()->json(['success' => false, 'message' => 'You must purchase this course to modify a question.'], 403);
        }

        // Prevent editing if already answered
        if ($question->status === 'answered') {
            return response()->json(['success' => false, 'message' => 'Cannot edit a question that has been answered.'], 403);
        }

        // Update question
        $question->update([
            'question_text' => $request->question_text,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Question updated successfully.',
            'question' => [
                'id' => $question->id,
                'question_text' => $question->question_text,
                'user_name' => $user->name,
                'created_at' => $question->created_at->format('F j, Y, H:i'),
                'answers' => $question->answers->map(function ($answer) {
                    return [
                        'answer_text' => $answer->answer_text,
                        'instructor_name' => $answer->instructor->name,
                        'created_at' => $answer->created_at->format('F j, Y, H:i'),
                    ];
                })->toArray(),
            ],
        ]);
    }
    public function destroyQuestion(Request $request, $courseId)
    {
        $request->validate([
            'question_id' => 'required|exists:course_questions,id',
        ]);

        $user = Auth::user();
        $question = CourseQuestion::findOrFail($request->question_id);

        // Verify user owns the question
        if ($question->user_id !== $user->id) {
            return response()->json(['success' => false, 'message' => 'Unauthorized action.'], 403);
        }

        // Check if the question belongs to the course
        if ($question->course_id !== (int)$courseId) {
            return response()->json(['success' => false, 'message' => 'Invalid course.'], 403);
        }

        // Check if user has purchased the course
        $hasPurchased = Order::where('user_id', $user->id)
            ->where('course_id', $courseId)
            ->where('payment_status', 'paid')
            ->exists();

        if (!$hasPurchased) {
            return response()->json(['success' => false, 'message' => 'You must purchase this course to delete a question.'], 403);
        }

        // Prevent deletion if already answered
        if ($question->status === 'answered') {
            return response()->json(['success' => false, 'message' => 'Cannot delete a question that has been answered.'], 403);
        }

        // Delete the question
        $question->delete();

        return response()->json([
            'success' => true,
            'message' => 'Question deleted successfully.',
        ]);
    }


  }