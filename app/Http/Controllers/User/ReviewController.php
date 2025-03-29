<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Review;
use Illuminate\Support\Facades\Auth;
use App\Notifications\ReviewSubmittedNotification;
use Carbon\Carbon;

class ReviewController extends Controller
{
    public function index()
    {
        $userId = Auth::guard('web')->id();
        $reviews = Review::where('user_id', $userId)
            ->with(['course', 'instructor'])
            ->orderBy('id', 'DESC')
            ->get();

        return view('User.reviews.index', compact('reviews'));
    }

    public function create()
    {
        return redirect()->route('user.reviews.index');
    }

  public function store(Request $request)
    {
        $request->validate([
            'course_id' => 'required|exists:courses,id',
            'instructor_id' => 'required|exists:instructors,id',
            'comment' => 'required|string|max:500',
            'rate' => 'required|integer|between:1,5',
        ]);

        $review = Review::create([
            'course_id' => $request->course_id,
            'user_id' => Auth::guard('web')->id(),
            'instructor_id' => $request->instructor_id,
            'comment' => $request->comment,
            'rating' => $request->rate,
            'status' => 0,
            'created_at' => Carbon::now(),
        ]);

        // Envoyer la notification à l'instructeur
        $instructor = $review->instructor;
        if ($instructor) {
            $instructor->notify(new ReviewSubmittedNotification($review));
            \Log::info("Notification envoyée à l'instructeur ID: {$instructor->id} pour l'avis ID: {$review->id}");
        } else {
            \Log::error("Instructeur non trouvé pour l'avis ID: {$review->id}");
        }

        return redirect()->back()->with([
            'message' => 'Review Submitted Successfully and Will Be Approved by Admin',
            'alert-type' => 'success'
        ]);}

    public function show(Review $review)
    {
        return redirect()->route('user.reviews.index');
    }

    public function edit(Review $review)
    {
        if ($review->user_id !== Auth::guard('web')->id()) {
            return redirect()->route('user.reviews.index')->with([
                'message' => 'Unauthorized access to edit this review.',
                'alert-type' => 'error'
            ]);
        }

        if ($review->status == 1) {
            return redirect()->route('user.reviews.index')->with([
                'message' => 'Approved reviews cannot be edited.',
                'alert-type' => 'error'
            ]);
        }

        return view('User.reviews.edit', compact('review'));
    }

    public function update(Request $request, Review $review)
    {
        if ($review->user_id !== Auth::guard('web')->id()) {
            return redirect()->route('user.reviews.index')->with([
                'message' => 'Unauthorized access to update this review.',
                'alert-type' => 'error'
            ]);
        }

        if ($review->status == 1) {
            return redirect()->route('user.reviews.index')->with([
                'message' => 'Approved reviews cannot be edited.',
                'alert-type' => 'error'
            ]);
        }

        $request->validate([
            'comment' => 'required|string|max:500',
            'rate' => 'required|integer|between:1,5',
        ]);

        $review->update([
            'comment' => $request->comment,
            'rating' => $request->rate,
            'updated_at' => Carbon::now(),
        ]);

        return redirect()->route('user.reviews.index')->with([
            'message' => 'Review Updated Successfully',
            'alert-type' => 'success'
        ]);
    }

    public function destroy(Review $review)
    {
        if ($review->user_id !== Auth::guard('web')->id()) {
            return redirect()->route('user.reviews.index')->with([
                'message' => 'Unauthorized access to delete this review.',
                'alert-type' => 'error'
            ]);
        }

        if ($review->status == 1) {
            return redirect()->route('user.reviews.index')->with([
                'message' => 'Approved reviews cannot be deleted.',
                'alert-type' => 'error'
            ]);
        }

        $review->delete();

        return redirect()->route('user.reviews.index')->with([
            'message' => 'Review Deleted Successfully',
            'alert-type' => 'success'
        ]);
    }
}