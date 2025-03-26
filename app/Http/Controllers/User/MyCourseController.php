<?php

namespace App\Http\Controllers\User;

use App\Models\Order;
use Illuminate\Http\Request;
use App\Models\UserCourseProgress;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\Course;
use App\Models\Review;
use Illuminate\Support\Facades\Storage;
use App\Models\UserLectureProgress;

class MyCourseController extends Controller
{
    public function myCourses()
    {
        $orders = Order::where('user_id', Auth::id())
                       ->where('payment_status', 'paid')
                       ->with(['course.category', 'course.instructor'])
                       ->latest()
                       ->get();

        foreach ($orders as $order) {
            $progress = UserCourseProgress::where('user_id', Auth::id())
                                          ->where('course_id', $order->course_id)
                                          ->first();
            $order->progress = $progress ? $progress->progress : 0;
        }

        return view('User.mycourses.my_courses', compact('orders'));
    }

    public function startLearning($id, $slug)
{
    $hasPurchased = Order::where('user_id', Auth::id())
                        ->where('course_id', $id)
                        ->where('payment_status', 'paid')
                        ->exists();

    if (!$hasPurchased) {
        return redirect()->route('course.details', [$id, $slug])
                        ->with('error', 'You need to purchase this course to start learning.');
    }

    $course = Course::with(['sections.lectures', 'instructor'])
                    ->where('id', $id)
                    ->where('course_name_slug', $slug)
                    ->firstOrFail();

    $sections = $course->sections;
    foreach ($sections as $section) {
        $section->total_duration = $section->lectures->sum('duration') ?? 0;
    }

    return view('User.mycourses.learn_course', compact('course', 'sections'));
}

    public function markLectureCompleted(Request $request)
    {
        $request->validate([
            'lecture_id' => 'required|exists:course_lectures,id',
            'course_id' => 'required|exists:courses,id',
            'completed' => 'required|boolean',
        ]);

        $userId = Auth::id();
        $lectureId = $request->lecture_id;
        $courseId = $request->course_id;

        // Vérifier que l'utilisateur a acheté le cours avant de marquer une leçon
        $hasPurchased = Order::where('user_id', $userId)
                            ->where('course_id', $courseId)
                            ->where('payment_status', 'paid')
                            ->exists();

        if (!$hasPurchased) {
            return response()->json(['success' => false, 'message' => 'You need to purchase this course first.'], 403);
        }

        $progress = UserCourseProgress::firstOrCreate(
            ['user_id' => $userId, 'course_id' => $courseId],
            ['completed_lectures' => []]
        );

        $completedLectures = $progress->completed_lectures ?? [];
        if ($request->completed) {
            if (!in_array($lectureId, $completedLectures)) {
                $completedLectures[] = $lectureId;
            }
        } else {
            $completedLectures = array_diff($completedLectures, [$lectureId]);
        }

        $progress->completed_lectures = array_values($completedLectures); // Réindexer le tableau
        $totalLectures = Course::find($courseId)->sections->pluck('lectures')->flatten()->count();
        $progress->progress = $totalLectures > 0 ? (count($completedLectures) / $totalLectures) * 100 : 0;
        $progress->save();

        return response()->json(['success' => true, 'progress' => $progress->progress]);
    }

    public function submitRating(Request $request, $courseId)
{
    $request->validate([
        'rating' => 'required|integer|min:1|max:5',
    ]);

    $userId = Auth::id();
    $course = Course::findOrFail($courseId);

    // Vérifier si l'utilisateur a acheté le cours
    $hasPurchased = Order::where('user_id', $userId)
                        ->where('course_id', $courseId)
                        ->where('payment_status', 'paid')
                        ->exists();

    if (!$hasPurchased) {
        return response()->json(['message' => 'You must purchase this course to rate it.'], 403);
    }

    // Enregistrer ou mettre à jour la note dans la table reviews
    Review::updateOrCreate(
        ['user_id' => $userId, 'course_id' => $courseId],
        [
            'instructor_id' => $course->instructor_id, // Associer l'instructeur du cours
            'comment' => $request->comment ?? '',      // Commentaire vide par défaut
            'rating' => $request->rating,
            'status' => 0                              // Statut par défaut à 0 (non publié)
        ]
    );

    return response()->json(['success' => true, 'message' => 'Rating submitted successfully']);
}
}