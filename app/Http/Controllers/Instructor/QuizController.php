<?php
namespace App\Http\Controllers\Instructor;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Quiz;
use App\Models\QuizQuestion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class QuizController extends Controller
{
    public function index()
    {
        $quizzes = Quiz::whereHas('course', function ($query) {
            $query->where('instructor_id', Auth::id());
        })->with('course')->get();
        return view('instructor.Quiz.index', compact('quizzes'));
    }

    public function create()
    {
        $courses = Course::where('instructor_id', Auth::id())->get();
        return view('instructor.Quiz.create', compact('courses'));
    }

public function store(Request $request)
{
    $request->validate([
        'course_id' => 'required|exists:courses,id',
        'title' => 'required|string|max:255',
        'description' => 'nullable|string',
        'time_limit' => 'nullable|integer|min:1',
        'questions' => 'required|array|min:1',
        'questions.*.question_text' => 'required|string',
        'questions.*.options' => 'required|string', // Attendu comme chaîne
        'questions.*.correct_answer' => 'required|string',
    ]);

    $quiz = Quiz::create([
        'course_id' => $request->course_id,
        'title' => $request->title,
        'description' => $request->description,
        'time_limit' => $request->time_limit,
    ]);

    foreach ($request->questions as $question) {
        $options = array_map('trim', explode(',', $question['options'])); // Convertir la chaîne en tableau
        QuizQuestion::create([
            'quiz_id' => $quiz->id,
            'question_text' => $question['question_text'],
            'options' => $options,
            'correct_answer' => $question['correct_answer'],
        ]);
    }

    return redirect()->route('instructor.quiz.index')->with('success', 'Quiz created successfully!');
}

    public function show(Quiz $quiz)
    {
        if ($quiz->course->instructor_id !== Auth::id()) {
            abort(403);
        }
        $quiz->load('questions');
        return view('instructor.Quiz.show', compact('quiz'));
    }

    public function edit(Quiz $quiz)
    {
        if ($quiz->course->instructor_id !== Auth::id()) {
            abort(403);
        }
        $courses = Course::where('instructor_id', Auth::id())->get();
        $quiz->load('questions');
        return view('instructor.Quiz.edit', compact('quiz', 'courses'));
    }

   public function update(Request $request, Quiz $quiz)
{
    if ($quiz->course->instructor_id !== Auth::id()) {
        abort(403);
    }

    $request->validate([
        'course_id' => 'required|exists:courses,id',
        'title' => 'required|string|max:255',
        'description' => 'nullable|string',
        'time_limit' => 'nullable|integer|min:1',
        'questions' => 'required|array|min:1',
        'questions.*.question_text' => 'required|string',
        'questions.*.options' => 'required|string', // Attendu comme chaîne
        'questions.*.correct_answer' => 'required|string',
    ]);

    $quiz->update([
        'course_id' => $request->course_id,
        'title' => $request->title,
        'description' => $request->description,
        'time_limit' => $request->time_limit,
    ]);

    // Supprimer les anciennes questions
    $quiz->questions()->delete();

    // Ajouter les nouvelles questions
    foreach ($request->questions as $question) {
        $options = array_map('trim', explode(',', $question['options'])); // Convertir la chaîne en tableau
        QuizQuestion::create([
            'quiz_id' => $quiz->id,
            'question_text' => $question['question_text'],
            'options' => $options,
            'correct_answer' => $question['correct_answer'],
        ]);
    }

    return redirect()->route('instructor.quiz.index')->with('success', 'Quiz updated successfully!');
}

    public function destroy(Quiz $quiz)
    {
        if ($quiz->course->instructor_id !== Auth::id()) {
            abort(403);
        }
        $quiz->delete();
        return redirect()->route('instructor.quiz.index')->with('success', 'Quiz deleted successfully!');
    }
}