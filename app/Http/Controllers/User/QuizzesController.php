<?php
namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\QuizAttempt;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class QuizzesController extends Controller
{
    public function index()
    {
        // Récupérer les tentatives de quiz de l'utilisateur connecté avec les relations quiz et course
        $quizAttempts = QuizAttempt::where('user_id', Auth::id())
            ->with(['quiz.course']) // Charger les relations quiz et course
            ->orderBy('completed_at', 'desc') // Trier par date de complétion
            ->get();

        return view('User.Quiz.index', compact('quizAttempts'));
    }
}