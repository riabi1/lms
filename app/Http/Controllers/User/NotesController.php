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
        $orders = Auth::user()->orders()->with(['course.notes' => function ($query) {
            $query->where('user_id', Auth::id())->orderBy('favorite', 'desc')->orderBy('due_date', 'asc');
        }])->get();
        return view('User.mycourses.my_courses', compact('orders'));
    }

    public function favorites()
    {
        $favoriteNotes = CourseNote::where('user_id', Auth::id())
            ->where('favorite', true)
            ->with('course')
            ->orderBy('created_at', 'desc')
            ->get();
        return view('User.mycourses.favorite_notes', compact('favoriteNotes'));
    }

    public function store(Request $request, $courseId)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string|max:1000',
            'due_date' => 'nullable|date|after_or_equal:today',
            'favorite' => 'boolean',
            'color' => 'nullable|string|in:bg-light-blue,bg-light-green,bg-light-yellow,bg-light-pink',
            'tags' => 'nullable|string|max:255',
        ]);

        $note = CourseNote::create([
            'user_id' => Auth::id(),
            'course_id' => $courseId,
            'title' => $validated['title'],
            'content' => $validated['content'],
            'due_date' => $validated['due_date'] ?? null,
            'favorite' => $request->has('favorite') ? true : false,
            'color' => $validated['color'] ?? 'bg-light-blue',
            'tags' => $validated['tags'] ?? null,
        ]);

        return response()->json(['success' => 'Note added successfully!', 'note' => $note], 200);
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string|max:1000',
            'due_date' => 'nullable|date|after_or_equal:today',
            'favorite' => 'boolean',
            'color' => 'nullable|string|in:bg-light-blue,bg-light-green,bg-light-yellow,bg-light-pink',
            'tags' => 'nullable|string|max:255',
        ]);

        $note = CourseNote::where('user_id', Auth::id())->findOrFail($id);
        $note->update([
            'title' => $validated['title'],
            'content' => $validated['content'],
            'due_date' => $validated['due_date'] ?? null,
            'favorite' => $request->has('favorite') ? true : false,
            'color' => $validated['color'] ?? 'bg-light-blue',
            'tags' => $validated['tags'] ?? null,
        ]);

        return response()->json(['success' => 'Note updated successfully!', 'note' => $note], 200);
    }

    public function toggleFavorite($id)
    {
        $note = CourseNote::where('user_id', Auth::id())->findOrFail($id);
        $note->favorite = !$note->favorite;
        $note->save();
        return response()->json(['success' => 'Favorite status updated!', 'favorite' => $note->favorite], 200);
    }

    public function destroy($id)
    {
        $note = CourseNote::where('user_id', Auth::id())->findOrFail($id);
        $note->delete();
        return response()->json(['success' => 'Note deleted successfully!'], 200);
    }
}