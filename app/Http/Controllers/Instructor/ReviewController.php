<?php

namespace App\Http\Controllers\Instructor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Review;
use Illuminate\Support\Facades\Auth;

class ReviewController extends Controller
{
    public function index()
    {
        $instructorId = Auth::guard('instructor')->id();
        $reviews = Review::where('instructor_id', $instructorId)
            ->where('status', 1)
            ->with(['course', 'user'])
            ->orderBy('id', 'DESC')
            ->get();

        return view('instructor.reviews.index', compact('reviews'));
    }
}