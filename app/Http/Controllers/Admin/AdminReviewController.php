<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Review;

class AdminReviewController extends Controller
{
    public function pending()
    {
        $reviews = Review::where('status', 0)->orderBy('id', 'DESC')->get();
        return view('admin.backend.review.pending_reviews', compact('reviews'));
    }

    public function updateStatus(Request $request)
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

    public function active()
    {
        $reviews = Review::where('status', 1)->orderBy('id', 'DESC')->get();
        return view('admin.backend.review.active_reviews', compact('reviews'));
    }
}