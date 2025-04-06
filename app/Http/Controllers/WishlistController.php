<?php
namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Wishlist;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WishlistController extends Controller
{
    public function index()
    {
        $wishlistCourses = Wishlist::where('trackable_type', 'App\Models\User')
            ->where('trackable_id', Auth::id())
            ->with('course')
            ->paginate(10); // Matches DataTables default page length

        $wishlistCourses->getCollection()->transform(function ($item) {
            return $item->course;
        })->filter();

        return view('User.wishlist.index', compact('wishlistCourses'));
    }

 public function add($course_id)
{
    $user = Auth::user();
    $course = Course::findOrFail($course_id);
    $exists = Wishlist::where('trackable_type', 'App\Models\User')
        ->where('trackable_id', $user->id)
        ->where('course_id', $course->id)
        ->exists();
    if ($exists) {
        return response()->json(['status' => 'error', 'message' => 'This course is already in your wishlist.'], 400);
    }
    Wishlist::create([
        'trackable_type' => 'App\Models\User',
        'trackable_id' => $user->id,
        'course_id' => $course->id,
    ]);
    return response()->json(['status' => 'success', 'message' => 'Course added to your wishlist!']);
}

public function remove($course_id)
{
    $user = Auth::user();
    $wishlist = Wishlist::where('trackable_type', 'App\Models\User')
        ->where('trackable_id', $user->id)
        ->where('course_id', $course_id)
        ->first();
    if (!$wishlist) {
        return response()->json(['status' => 'error', 'message' => 'This course is not in your wishlist.'], 400);
    }
    $wishlist->delete();
    return response()->json(['status' => 'success', 'message' => 'Course removed from your wishlist!']);
}
}