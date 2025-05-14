<?php
namespace App\Http\Controllers;

use App\Models\Course;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    public function search(Request $request)
    {
        $query = $request->query('query', '');
        if (empty($query)) {
            return response()->json([]);
        }

        $courses = Course::query()
            ->where('status', 1)
            ->where(function ($q) use ($query) {
                $q->where('course_title', 'LIKE', "%{$query}%")
                  ->orWhere('course_name', 'LIKE', "%{$query}%")
                  ->orWhere('description', 'LIKE', "%{$query}%");
            })
            ->select('id', 'course_title', 'course_name', 'course_name_slug', 'course_image', 'selling_price', 'discount_price')
            ->take(10)
            ->get()
            ->map(function ($course) {
                return [
                    'id' => $course->id,
                    'title' => $course->course_title,
                    'name' => $course->course_name,
                    'slug' => $course->course_name_slug,
                    'image' => $course->course_image ? asset('upload/course_images/thumbnail/' . $course->course_image) : asset('upload/no_image.jpg'),
                    'price' => $course->discount_price ?? $course->selling_price,
                    'url' => route('course.details', ['id' => $course->id, 'slug' => $course->course_name_slug]),
                ];
            });

        return response()->json($courses);
    }
}