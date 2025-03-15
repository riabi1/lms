<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Review;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class ReviewController extends Controller
{
    /**
     * Store a new review from a user (student).
     */
    public function StoreReview(Request $request)
    {
        $request->validate([
            'course_id' => 'required|exists:courses,id',
            'instructor_id' => 'required|exists:instructors,id',
            'comment' => 'required|string|max:500',
            'rate' => 'required|integer|between:1,5', // Assuming rating is 1-5
        ]);

        Review::create([
            'course_id' => $request->course_id,
            'user_id' => Auth::guard('web')->id(), // Explicitly use 'web' guard for users
            'instructor_id' => $request->instructor_id,
            'comment' => $request->comment,
            'rating' => $request->rate,
            'status' => 0, // Default pending status
            'created_at' => Carbon::now(),
        ]);

        $notification = [
            'message' => 'Review Submitted Successfully and Will Be Approved by Admin',
            'alert-type' => 'success'
        ];

        return redirect()->back()->with($notification);
    }

    /**
     * Display pending reviews for admin.
     */
    public function AdminPendingReview()
    {
        $reviews = Review::where('status', 0)->orderBy('id', 'DESC')->get();
        return view('admin.backend.review.pending_review', compact('reviews'));
    }

    /**
     * Update review status (approve/reject) via AJAX.
     */
    public function UpdateReviewStatus(Request $request)
    {
        $request->validate([
            'review_id' => 'required|exists:reviews,id',
            'is_checked' => 'required|boolean',
        ]);

        $review = Review::findOrFail($request->review_id);
        $review->status = $request->is_checked;
        $review->save();

        return response()->json(['message' => 'Review Status Updated Successfully']);
    }

    /**
     * Display active (approved) reviews for admin.
     */
    public function AdminActiveReview()
    {
        $reviews = Review::where('status', 1)->orderBy('id', 'DESC')->get();
        return view('admin.backend.review.active_review', compact('reviews'));
    }

    /**
     * Display active reviews for the authenticated instructor.
     */
    public function InstructorAllReview()
    {
        $instructorId = Auth::guard('instructor')->id(); // Use 'instructor' guard
        $reviews = Review::where('instructor_id', $instructorId)
                         ->where('status', 1)
                         ->orderBy('id', 'DESC')
                         ->get();

        return view('instructor.review.active_review', compact('reviews'));
    }
}