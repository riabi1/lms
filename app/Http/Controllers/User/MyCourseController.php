<?php

namespace App\Http\Controllers\User;

use App\Models\Order;
use Illuminate\Http\Request;
use App\Models\UserCourseProgress;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\Course;
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

public function learnCourse($id, $slug)
{
    $course = Course::with(['sections.lectures'])->where('id', $id)->where('course_name_slug', $slug)->firstOrFail();
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
   

}