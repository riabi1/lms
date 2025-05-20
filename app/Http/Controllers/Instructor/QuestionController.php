<?php

namespace App\Http\Controllers\Instructor;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\CourseQuestion;
use App\Models\Answer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Notifications\NewAnswerNotification;

class QuestionController extends Controller
{
    public function index()
    {
        $instructor = Auth::guard('instructor')->user();
        $courses = Course::with(['questions.user', 'questions.answers.instructor'])
            ->where('courseable_type', get_class($instructor))
            ->where('courseable_id', $instructor->id)
            ->get();

        // Prepare flattened questions for JavaScript
        $questions = $courses->flatMap(function ($course) {
            return $course->questions;
        })->values();

        return view('instructor.Question.index', compact('courses', 'questions'));
    }

    public function storeAnswer(Request $request)
    {
        $request->validate([
            'question_id' => 'required|exists:course_questions,id',
            'answer_text' => 'required|string|max:2000',
        ]);

        $instructor = Auth::guard('instructor')->user();
        $question = CourseQuestion::findOrFail($request->question_id);

        // Verify instructor owns the course
        $course = $question->course;
        if ($course->courseable_type !== get_class($instructor) || $course->courseable_id !== $instructor->id) {
            return response()->json(['success' => false, 'message' => 'Unauthorized action.'], 403);
        }

        // Create answer
        $answer = Answer::create([
            'course_question_id' => $question->id,
            'instructor_id' => $instructor->id,
            'answer_text' => $request->answer_text,
        ]);

        // Update question status
        $question->update(['status' => 'answered']);

        // Notify the student
        $question->user->notify(new NewAnswerNotification($question, $answer, $course, $instructor));

        return response()->json([
            'success' => true,
            'message' => 'Answer submitted successfully.',
            'answer' => [
                'id' => $answer->id,
                'question_id' => $question->id,
                'answer_text' => $answer->answer_text,
                'instructor_name' => $instructor->name,
                'created_at' => $answer->created_at->format('d M Y, H:i'),
            ],
        ]);
    }

    public function updateAnswer(Request $request)
    {
        $request->validate([
            'answer_id' => 'required|exists:answers,id',
            'answer_text' => 'required|string|max:2000',
        ]);

        $instructor = Auth::guard('instructor')->user();
        $answer = Answer::findOrFail($request->answer_id);

        // Verify instructor owns the answer
        if ($answer->instructor_id !== $instructor->id) {
            return response()->json(['success' => false, 'message' => 'Unauthorized action.'], 403);
        }

        // Update answer
        $answer->update([
            'answer_text' => $request->answer_text,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Answer updated successfully.',
            'answer' => [
                'id' => $answer->id,
                'question_id' => $answer->course_question_id,
                'answer_text' => $answer->answer_text,
                'instructor_name' => $instructor->name,
                'created_at' => $answer->created_at->format('d M Y, H:i'),
            ],
        ]);
    }

    public function destroyAnswer(Request $request)
    {
        $request->validate([
            'answer_id' => 'required|exists:answers,id',
        ]);

        $instructor = Auth::guard('instructor')->user();
        $answer = Answer::findOrFail($request->answer_id);

        // Verify instructor owns the answer
        if ($answer->instructor_id !== $instructor->id) {
            return response()->json(['success' => false, 'message' => 'Unauthorized action.'], 403);
        }

        $question = $answer->question;

        // Delete answer
        $answer->delete();

        // Update question status to pending if no answers remain
        if ($question->answers()->count() === 0) {
            $question->update(['status' => 'pending']);
        }

        return response()->json([
            'success' => true,
            'message' => 'Answer deleted successfully.',
        ]);
    }
}