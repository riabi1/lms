<?php

namespace App\Http\Controllers\Instructor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use App\Models\BlogPost;
use App\Models\Comment;
use App\Models\CommentReply;

class BlogController extends Controller
{
    public function index(Request $request)
    {
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
                    $query->select('id', 'name');
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
            'content' => 'required|string',
            'blog_category_id' => 'required|exists:blog_categories,id',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'video' => 'nullable|mimes:mp4,webm|max:10240',
        ]);

        $post = new BlogPost();
        $post->instructor_id = Auth::guard('instructor')->id();
        $post->blog_category_id = $request->blog_category_id;
        $post->title = $request->title;
        $post->slug = Str::slug($request->title) . '-' . time();
        $post->content = $request->content;
        $post->status = 'active';

        if ($request->hasFile('image')) {
            $post->image = $this->uploadFile($request->file('image'), 'upload/blog-posts');
        }

        if ($request->hasFile('video')) {
            $post->video = $this->uploadFile($request->file('video'), 'upload/blog-posts');
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
            'content' => 'required|string',
            'blog_category_id' => 'required|exists:blog_categories,id',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'video' => 'nullable|mimes:mp4,webm|max:10240',
        ]);

        $post->title = $request->title;
        $post->slug = Str::slug($request->title) . '-' . time();
        $post->content = $request->content;
        $post->blog_category_id = $request->blog_category_id;

        if ($request->hasFile('image')) {
            if ($post->image) {
                $this->deleteFile($post->image, 'upload/blog-posts');
            }
            $post->image = $this->uploadFile($request->file('image'), 'upload/blog-posts');
        }

        if ($request->hasFile('video')) {
            if ($post->video) {
                $this->deleteFile($post->video, 'upload/blog-posts');
            }
            $post->video = $this->uploadFile($request->file('video'), 'upload/blog-posts');
        }

        $post->save();

        return redirect()->route('instructor.blog.index')
            ->with('success', 'Blog post updated successfully');
    }

    public function destroy($id)
    {
        $post = BlogPost::where('instructor_id', Auth::guard('instructor')->id())->findOrFail($id);
        if ($post->image) {
            $this->deleteFile($post->image, 'upload/blog-posts');
        }
        if ($post->video) {
            $this->deleteFile($post->video, 'upload/blog-posts');
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

    private function uploadFile($file, $path)
    {
        if (!$file) {
            return null;
        }

        $fileName = time() . '_' . $file->getClientOriginalName();
        $publicPath = public_path($path);

        if (!file_exists($publicPath)) {
            mkdir($publicPath, 0755, true);
        }

        $file->move($publicPath, $fileName);

        return $fileName;
    }

    private function deleteFile($filename, $path)
    {
        if ($filename) {
            $publicFile = public_path($path . '/' . $filename);

            if (file_exists($publicFile)) {
                unlink($publicFile);
            }
        }
    }
}