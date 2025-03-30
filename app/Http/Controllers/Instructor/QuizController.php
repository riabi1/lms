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
        $instructor = Auth::guard('instructor')->user();
        $quizzes = Quiz::whereHas('course', function ($query) use ($instructor) {
            $query->where('courseable_type', get_class($instructor))
                  ->where('courseable_id', $instructor->id);
        })->with('course')->latest()->get();

        return view('instructor.quiz.index', compact('quizzes'));
    }

    public function create(Course $course = null)
    {
        $instructor = Auth::guard('instructor')->user();
        $courses = Course::where('courseable_type', get_class($instructor))
                         ->where('courseable_id', $instructor->id)
                         ->get();
        return view('instructor.quiz.create', compact('courses', 'course'));
    }

    public function store(Request $request)
    {
        $instructor = Auth::guard('instructor')->user();

        $request->validate([
            'course_id' => 'required|exists:courses,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'time_limit' => 'nullable|integer|min:1|max:1440', // Limite raisonnable (24h)
            'questions' => 'required|array|min:1',
            'questions.*.question_text' => 'required|string|max:500',
            'questions.*.options' => 'required|string|regex:/^[^,]+(,[^,]+)*$/', // Au moins une option, séparée par des virgules
            'questions.*.correct_answer' => 'required|string',
        ], [
            'questions.*.options.regex' => 'Options must be a comma-separated list with at least one option.',
        ]);

        // Vérifier que le cours appartient à l'instructeur
        $course = Course::where('id', $request->course_id)
                        ->where('courseable_type', get_class($instructor))
                        ->where('courseable_id', $instructor->id)
                        ->firstOrFail();

        $quiz = Quiz::create([
            'course_id' => $course->id,
            'title' => $request->title,
            'description' => $request->description,
            'time_limit' => $request->time_limit,
        ]);

        foreach ($request->questions as $question) {
            $options = array_map('trim', explode(',', $question['options']));
            // Vérifier que la réponse correcte est dans les options
            if (!in_array($question['correct_answer'], $options)) {
                $quiz->questions()->delete();
                $quiz->delete();
                return redirect()->back()->withInput()->withErrors(['questions' => 'Correct answer must be one of the provided options for all questions.']);
            }

            QuizQuestion::create([
                'quiz_id' => $quiz->id,
                'question_text' => $question['question_text'],
                'options' => json_encode($options), // Stocker en JSON pour plus de flexibilité
                'correct_answer' => $question['correct_answer'],
            ]);
        }

        return redirect()->route('instructor.quiz.index')
            ->with('message', 'Quiz created successfully!')
            ->with('alert-type', 'success');
    }

    public function show(Quiz $quiz)
    {
        $this->authorizeQuiz($quiz);
        $quiz->load('questions', 'course');
        return view('instructor.quiz.show', compact('quiz'));
    }

    public function edit(Quiz $quiz)
    {
        $this->authorizeQuiz($quiz);
        $instructor = Auth::guard('instructor')->user();
        $courses = Course::where('courseable_type', get_class($instructor))
                         ->where('courseable_id', $instructor->id)
                         ->get();
        $quiz->load('questions');
        return view('instructor.quiz.edit', compact('quiz', 'courses'));
    }

    public function update(Request $request, Quiz $quiz)
    {
        $this->authorizeQuiz($quiz);

        $request->validate([
            'course_id' => 'required|exists:courses,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'time_limit' => 'nullable|integer|min:1|max:1440',
            'questions' => 'required|array|min:1',
            'questions.*.question_text' => 'required|string|max:500',
            'questions.*.options' => 'required|string|regex:/^[^,]+(,[^,]+)*$/',
            'questions.*.correct_answer' => 'required|string',
        ], [
            'questions.*.options.regex' => 'Options must be a comma-separated list with at least one option.',
        ]);

        // Vérifier que le cours appartient à l'instructeur
        $instructor = Auth::guard('instructor')->user();
        $course = Course::where('id', $request->course_id)
                        ->where('courseable_type', get_class($instructor))
                        ->where('courseable_id', $instructor->id)
                        ->firstOrFail();

        $quiz->update([
            'course_id' => $course->id,
            'title' => $request->title,
            'description' => $request->description,
            'time_limit' => $request->time_limit,
        ]);

        // Supprimer les anciennes questions
        $quiz->questions()->delete();

        // Ajouter les nouvelles questions
        foreach ($request->questions as $question) {
            $options = array_map('trim', explode(',', $question['options']));
            if (!in_array($question['correct_answer'], $options)) {
                $quiz->questions()->delete();
                return redirect()->back()->withInput()->withErrors(['questions' => 'Correct answer must be one of the provided options for all questions.']);
            }

            QuizQuestion::create([
                'quiz_id' => $quiz->id,
                'question_text' => $question['question_text'],
                'options' => json_encode($options),
                'correct_answer' => $question['correct_answer'],
            ]);
        }

        return redirect()->route('instructor.quiz.index')
            ->with('message', 'Quiz updated successfully!')
            ->with('alert-type', 'success');
    }

    public function destroy(Quiz $quiz)
    {
        $this->authorizeQuiz($quiz);
        $quiz->questions()->delete(); // Supprimer les questions associées
        $quiz->delete();
        return redirect()->route('instructor.quiz.index')
            ->with('message', 'Quiz deleted successfully!')
            ->with('alert-type', 'success');
    }

    /**
     * Vérifie si l'instructeur est autorisé à accéder au quiz via le cours polymorphique.
     */
    private function authorizeQuiz(Quiz $quiz)
    {
        $instructor = Auth::guard('instructor')->user();
        $course = $quiz->course;
        if (!$course || $course->courseable_type !== get_class($instructor) || $course->courseable_id !== $instructor->id) {
            abort(403, 'Unauthorized action.');
        }
    }
}