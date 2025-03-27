<?php

namespace App\Http\Controllers\User;

use App\Models\Course;
use App\Models\CourseNote;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class NotesController extends Controller
{
    public function index()
    {
        $orders = Auth::user()->orders()->with(['course.notes'])->get();
        return view('User.mycourses.my_courses', compact('orders'));
    }

    public function store(Request $request, $courseId)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string|max:1000',
            'due_date' => 'nullable|date',
            'favorite' => 'nullable|boolean',
            'color' => 'nullable|string|max:50',
        ]);

        $note = CourseNote::create([
            'user_id' => Auth::id(),
            'course_id' => $courseId,
            'title' => $validated['title'],
            'content' => $validated['content'],
            'due_date' => $validated['due_date'] ?? null,
            'favorite' => $validated['favorite'] ?? false,
            'color' => $validated['color'] ?? 'bg-light-blue',
        ]);

        return redirect()->back()->with('success', 'Note added successfully!');
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string|max:1000',
            'due_date' => 'nullable|date',
            'favorite' => 'nullable|boolean',
            'color' => 'nullable|string|max:50',
        ]);

        $note = CourseNote::where('user_id', Auth::id())->findOrFail($id);
        $note->update([
            'title' => $validated['title'],
            'content' => $validated['content'],
            'due_date' => $validated['due_date'] ?? null,
            'favorite' => $validated['favorite'] ?? false,
            'color' => $validated['color'] ?? 'bg-light-blue',
        ]);

        return redirect()->back()->with('success', 'Note updated successfully!');
    }

    public function destroy($id)
    {
        $note = CourseNote::where('user_id', Auth::id())->findOrFail($id);
        $note->delete();
        return redirect()->back()->with('success', 'Note deleted successfully!');
    }
}