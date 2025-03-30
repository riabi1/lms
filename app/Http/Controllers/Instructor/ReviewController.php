<?php

namespace App\Http\Controllers\Instructor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Review;
use App\Models\Course;
use Illuminate\Support\Facades\Auth;

class ReviewController extends Controller
{
    public function index()
    {
        // Récupérer l'ID de l'instructeur connecté
        $instructorId = Auth::guard('instructor')->id();

        // Récupérer les IDs des cours appartenant à cet instructeur
        $courseIds = Course::where('courseable_type', 'App\Models\Instructor')
            ->where('courseable_id', $instructorId)
            ->pluck('id');

        // Charger les avis actifs liés à ces cours
        $reviews = Review::where('status', 1)
            ->where('reviewable_type', 'App\Models\Course')
            ->whereIn('reviewable_id', $courseIds)
            ->with(['reviewable', 'user']) // Charger les relations polymorphique et utilisateur
            ->orderBy('id', 'DESC')
            ->get();

        return view('instructor.reviews.index', compact('reviews'));
    }
}