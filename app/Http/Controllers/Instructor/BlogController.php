<?php

namespace App\Http\Controllers\Instructor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Models\BlogPost;
use App\Models\Comment;
use App\Models\CommentReply;

class BlogController extends Controller
{
    public function index(Request $request)
    {
        // Mark notification as read if provided
        if ($request->query('notification_id')) {
            $notification = Auth::guard('instructor')->user()
                ->notifications()
                ->find($request->query('notification_id'));
            
            if ($notification) {
                $notification->markAsRead();
            }
        }

        $posts = BlogPost::where('instructor_id', Auth::guard('instructor')->id())
            ->with([
                'category' => function ($query) {
                    $query->select('id', 'name'); // Optimize by selecting only needed columns
                },
                'comments' => function ($query) {
                    $query->with(['user', 'replies' => function ($replyQuery) {
                        $replyQuery->where('approved', true)->with('user');
                    }]);
                }
            ])
            ->latest()
            ->get();
        
        return view('instructor.blog.index', compact('posts'));
    }

    public function create()
    {
        $categories = \App\Models\BlogCategory::select('id', 'name')->get();
        return view('instructor.blog.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string|max:500',
            'blog_category_id' => 'required|exists:blog_categories,id',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $post = new BlogPost();
        $post->instructor_id = Auth::guard('instructor')->id();
        $post->blog_category_id = $request->blog_category_id;
        $post->title = $request->title;
        $post->slug = Str::slug($request->title) . '-' . time();
        $post->content = $request->content;
        $post->status = 'active';

        if ($request->hasFile('image')) {
            $fileName = time() . '.' . $request->image->extension();
            $path = $request->image->storeAs('upload/blog-posts', $fileName, 'public');
            $post->image = $path;
        }

        $post->save();

        return redirect()->route('instructor.blog.index')
            ->with('success', 'Blog post created successfully');
    }

    public function edit($id)
    {
        $post = BlogPost::where('instructor_id', Auth::guard('instructor')->id())->findOrFail($id);
        $categories = \App\Models\BlogCategory::select('id', 'name')->get();
        return view('instructor.blog.edit', compact('post', 'categories'));
    }

    public function update(Request $request, $id)
    {
        $post = BlogPost::where('instructor_id', Auth::guard('instructor')->id())->findOrFail($id);

        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string|max:500',
            'blog_category_id' => 'required|exists:blog_categories,id',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $post->title = $request->title;
        $post->slug = Str::slug($request->title) . '-' . time();
        $post->content = $request->content;
        $post->blog_category_id = $request->blog_category_id;

        if ($request->hasFile('image')) {
            if ($post->image && Storage::disk('public')->exists($post->image)) {
                Storage::disk('public')->delete($post->image);
            }
            $fileName = time() . '.' . $request->image->extension();
            $path = $request->image->storeAs('upload/blog-posts', $fileName, 'public');
            $post->image = $path;
        }

        $post->save();

        return redirect()->route('instructor.blog.index')
            ->with('success', 'Blog post updated successfully');
    }

    public function destroy($id)
    {
        $post = BlogPost::where('instructor_id', Auth::guard('instructor')->id())->findOrFail($id);
        if ($post->image && Storage::disk('public')->exists($post->image)) {
            Storage::disk('public')->delete($post->image);
        }
        $post->delete();

        return redirect()->route('instructor.blog.index')
            ->with('success', 'Blog post deleted successfully');
    }

    public function replyComment(Request $request, $commentId)
    {
        $comment = Comment::findOrFail($commentId);
        $post = BlogPost::where('id', $comment->blog_post_id)
            ->where('instructor_id', Auth::guard('instructor')->id())
            ->firstOrFail();

        $request->validate([
            'message' => 'required|string|max:1000',
        ]);

        try {
            CommentReply::create([
                'comment_id' => $commentId,
                'user_id' => Auth::guard('instructor')->id(),
                'user_type' => \App\Models\Instructor::class,
                'message' => $request->input('message'),
                'approved' => true,
            ]);
        } catch (\Exception $e) {
            return redirect()->route('instructor.blog.index')
                ->with('error', 'Failed to post reply: ' . $e->getMessage());
        }

        return redirect()->route('instructor.blog.index')
            ->with('success', 'Your reply has been posted.');
    }
}