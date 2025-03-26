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

      public function startLearning($id, $slug)
    {
        $userId = Auth::id();

        // Vérifier si l'utilisateur a acheté le cours
        $hasPurchased = Order::where('user_id', $userId)
                            ->where('course_id', $id)
                            ->where('payment_status', 'paid')
                            ->exists();

        if (!$hasPurchased) {
            return redirect()->route('course.details', [$id, $slug])
                            ->with('error', 'You need to purchase this course to start learning.');
        }

        // Charger le cours avec ses sections et leçons
        $course = Course::with(['sections.lectures' => function ($query) {
            $query->from('course_lectures'); // Spécifier la table correcte
        }, 'instructor'])
            ->where('id', $id)
            ->where('course_name_slug', $slug)
            ->firstOrFail();

        $sections = $course->sections;
        foreach ($sections as $section) {
            $section->total_duration = $section->lectures->sum('duration') ?? 0;
        }

        // Charger la progression de l'utilisateur pour ce cours
        $progress = UserCourseProgress::where('user_id', $userId)
                                      ->where('course_id', $id)
                                      ->pluck('completed', 'lecture_id')
                                      ->toArray();

        return view('User.mycourses.learn_course', compact('course', 'sections', 'progress'));
    }

    public function markLectureCompleted(Request $request, $courseId)
    {
        $request->validate([
            'lecture_id' => 'required|exists:course_lectures,id', // Vérifier dans la bonne table
        ]);

        $userId = Auth::id();
        $course = Course::findOrFail($courseId);

        // Vérifier si l'utilisateur a acheté le cours
        $hasPurchased = Order::where('user_id', $userId)
                            ->where('course_id', $courseId)
                            ->where('payment_status', 'paid')
                            ->exists();

        if (!$hasPurchased) {
            return response()->json(['success' => false, 'message' => 'You must purchase this course to mark progress.'], 403);
        }

        // Marquer la leçon comme terminée
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

        // Calculer la progression totale
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

}