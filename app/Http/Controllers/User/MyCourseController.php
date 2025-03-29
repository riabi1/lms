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
use Illuminate\Support\Facades\Storage;
use App\Models\UserLectureProgress;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;

class MyCourseController extends Controller
{
    public function myCourses()
    {
        $orders = auth()->user()->orders()->with('course.sections.lectures')->get();
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

        Review::updateOrCreate(
            ['user_id' => $userId, 'course_id' => $courseId],
            [
                'instructor_id' => $course->instructor_id,
                'comment' => $request->comment ?? '',
                'rating' => $request->rating,
                'status' => 0
            ]
        );

        return response()->json(['success' => true, 'message' => 'Rating submitted successfully']);
    }

    public function markLectureCompleted(Request $request, $courseId)
    {
        $request->validate([
            'lecture_id' => 'required|exists:course_lectures,id',
        ]);

        $userId = Auth::id();
        $course = Course::findOrFail($courseId);

        $hasPurchased = Order::where('user_id', $userId)
                            ->where('course_id', $courseId)
                            ->where('payment_status', 'paid')
                            ->exists();

        if (!$hasPurchased) {
            return response()->json(['success' => false, 'message' => 'You must purchase this course to mark progress.'], 403);
        }

        UserCourseProgress::updateOrCreate(
            [
                'user_id' => $userId,
                'course_id' => $courseId,
                'lecture_id' => $request->lecture_id,
            ],
            [
                'completed' => 1,
                'completed_at' => now(),
            ]
        );

        $totalLectures = $course->sections->flatMap->lectures->count();
        $completedLectures = UserCourseProgress::where('user_id', $userId)
                                              ->where('course_id', $courseId)
                                              ->where('completed', 1)
                                              ->count();
        $progressPercentage = $totalLectures > 0 ? round(($completedLectures / $totalLectures) * 100) : 0;

        return response()->json([
            'success' => true,
            'message' => 'Lecture marked as completed.',
            'progress' => $progressPercentage,
        ]);
    }

    public function startLearning($courseId, $slug)
    {
        $course = Course::with(['sections.lectures', 'quizzes.questions'])->findOrFail($courseId);

        if (\Str::slug($course->course_name) !== $slug) {
            return redirect()->route('course.start', ['courseId' => $courseId, 'slug' => \Str::slug($course->course_name)]);
        }

        $user = Auth::user();

        // Vérifier si l'utilisateur a acheté le cours
        $hasPurchased = Order::where('user_id', $user->id)
                            ->where('course_id', $courseId)
                            ->where('payment_status', 'paid')
                            ->exists();

        if (!$hasPurchased) {
            abort(403, 'You do not have access to this course.');
        }

        // Calculer la progression
        $progress = $user->courseProgress()
            ->where('course_id', $courseId)
            ->pluck('completed', 'lecture_id')
            ->toArray();
        $totalLectures = $course->sections->flatMap->lectures->count();
        $completedLectures = array_filter($progress, fn($completed) => $completed == 1);
        $progressPercentage = $totalLectures > 0 ? round((count($completedLectures) / $totalLectures) * 100) : 0;

        // Récupérer les tentatives de quiz
        $quizAttempts = QuizAttempt::where('user_id', $user->id)
            ->whereIn('quiz_id', $course->quizzes->pluck('id'))
            ->get();

        return view('User.mycourses.learn_course', compact('course', 'progress', 'progressPercentage', 'quizAttempts'));
    }

    public function submitQuiz(Request $request, $courseId, $quizId)
    {
        $user = Auth::user();
        $quiz = Quiz::with('questions')->findOrFail($quizId);

        if ($quiz->course_id != $courseId) {
            abort(403, 'This quiz does not belong to the course.');
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
                return redirect()->route('course.start', ['courseId' => $courseId, 'slug' => \Str::slug($quiz->course->course_name)])
                    ->with('error', "You have reached the maximum of 3 attempts. Please wait $secondsLeft seconds before trying again.");
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
            ? 'Quiz passed! Your score: ' . $score . '%. You can now download your certificate.'
            : 'Quiz failed. Your score: ' . $score . '%. You have ' . (2 - $attemptCount) . ' attempts remaining.';

        return redirect()->route('course.start', ['courseId' => $courseId, 'slug' => \Str::slug($quiz->course->course_name)])
            ->with('success', $message);
    }

    public function downloadCertificate($courseId)
    {
        $course = Course::with('quizzes')->findOrFail($courseId);
        $user = Auth::user();

        if ($course->certificate !== 'yes') {
            return redirect()->route('course.start', ['courseId' => $courseId, 'slug' => \Str::slug($course->course_name)])
                ->with('error', 'This course does not offer a certificate.');
        }

        $totalLectures = $course->sections->flatMap->lectures->count();
        $completedLectures = $user->courseProgress()->where('course_id', $courseId)->where('completed', 1)->count();
        $progressPercentage = $totalLectures > 0 ? round(($completedLectures / $totalLectures) * 100) : 0;

        if ($progressPercentage < 100) {
            return redirect()->route('course.start', ['courseId' => $courseId, 'slug' => \Str::slug($course->course_name)])
                ->with('error', 'You must complete all lectures to download the certificate.');
        }

        if ($course->quizzes->isNotEmpty()) {
            $quizAttempts = QuizAttempt::where('user_id', $user->id)
                ->whereIn('quiz_id', $course->quizzes->pluck('id'))
                ->get();

            $allQuizzesPassed = $course->quizzes->every(function ($quiz) use ($quizAttempts) {
                return $quizAttempts->where('quiz_id', $quiz->id)->where('passed', true)->isNotEmpty();
            });

            if (!$allQuizzesPassed) {
                return redirect()->route('course.start', ['courseId' => $courseId, 'slug' => \Str::slug($course->course_name)])
                    ->with('error', 'You must pass all quizzes to download the certificate.');
            }
        }

        $data = [
            'course_name' => $course->course_name,
            'user_name' => $user->name,
            'completion_date' => Carbon::now()->format('F d, Y'),
            'certificate_number' => 'EDAA-' . str_pad($course->id, 4, '0', STR_PAD_LEFT) . '-' . str_pad($user->id, 4, '0', STR_PAD_LEFT),
        ];

        $pdf = PDF::loadView('User.mycourses.certificate', $data)->setPaper('a4', 'portrait');
        return $pdf->download('certificate_' . $course->id . '_' . $user->id . '.pdf');
    }
}