<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Review;
use App\Models\Course;
use App\Models\Instructor;
use Illuminate\Support\Facades\Auth;
use App\Notifications\ReviewSubmittedNotification;
use Carbon\Carbon;

class ReviewController extends Controller
{
public function index()
{
    $userId = Auth::guard('web')->id();
    $reviews = Review::where('user_id', $userId)
        ->with(['reviewable' => function ($query) {
            $query->with('courseable');
        }, 'user'])
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
            'comment' => 'required|string|max:500',
            'rate' => 'required|integer|between:1,5',
        ]);

        // Vérifier si l'utilisateur a déjà soumis un avis pour ce cours
        $existingReview = Review::where('user_id', Auth::guard('web')->id())
            ->where('reviewable_type', 'App\Models\Course')
            ->where('reviewable_id', $request->course_id)
            ->exists();

        if ($existingReview) {
            return redirect()->back()->with([
                'message' => 'You have already submitted a review for this course.',
                'alert-type' => 'error'
            ]);
        }

        // Créer l'avis avec la relation polymorphique
        $review = Review::create([
            'reviewable_type' => 'App\Models\Course',
            'reviewable_id' => $request->course_id,
            'user_id' => Auth::guard('web')->id(),
            'comment' => $request->comment,
            'rating' => $request->rate,
            'status' => 0,
            'created_at' => Carbon::now(),
        ]);

        // Récupérer le cours et son instructeur
        $course = Course::find($request->course_id);
        $instructor = $course->courseable_type === 'App\Models\Instructor' && $course->courseable_id
            ? Instructor::find($course->courseable_id)
            : null;

        // Envoyer la notification à l'instructeur
        if ($instructor) {
            $instructor->notify(new ReviewSubmittedNotification($review));
            \Log::info("Notification envoyée à l'instructeur ID: {$instructor->id} pour l'avis ID: {$review->id}");
        } else {
            \Log::warning("Aucun instructeur trouvé pour le cours ID: {$request->course_id}");
        }

        return redirect()->back()->with([
            'message' => 'Review Submitted Successfully and Will Be Approved by Admin',
            'alert-type' => 'success'
        ]);
    }

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

    if (!$review->is_course) {
        return redirect()->route('user.reviews.index')->with([
            'message' => 'This review is not associated with a course.',
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