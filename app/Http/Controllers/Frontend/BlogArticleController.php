<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\Comment;
use App\Models\Instructor;
use App\Models\CommentReply;
use App\Notifications\BlogCommentNotification;

class BlogArticleController extends Controller
{
    public function index(Request $request)
    {
        $query = DB::table('blog_posts')
            ->join('instructors', 'blog_posts.instructor_id', '=', 'instructors.id')
            ->join('blog_categories', 'blog_posts.blog_category_id', '=', 'blog_categories.id')
            ->select('blog_posts.*', 'instructors.name as author_name', 'blog_categories.name as category_name')
            ->where('blog_posts.status', 'active');

        if ($request->query('category')) {
            $query->where('blog_posts.blog_category_id', $request->query('category'));
        }

        $blogPosts = $query->orderBy('blog_posts.created_at', 'desc')->paginate(6);
        $categories = DB::table('blog_categories')->get();

        if ($request->ajax()) {
            return response()->json([
                'blogs' => $blogPosts->items(),
                'pagination' => (string) $blogPosts->appends(request()->query())->render('vendor.pagination.bootstrap-4'),
            ]);
        }

        return view('frontend.blog.blog_list', compact('blogPosts', 'categories'));
    }

    public function show($slug)
    {
        $post = DB::table('blog_posts')
            ->where('slug', $slug)
            ->where('status', 'active')
            ->first();

        if (!$post) {
            abort(404, 'Blog post not found');
        }

        $author = DB::table('instructors')->where('id', $post->instructor_id)->first();
        $category = DB::table('blog_categories')->where('id', $post->blog_category_id)->first();

        $comments = Comment::with(['user', 'replies.user'])
            ->where('blog_post_id', $post->id)
            ->where('approved', true)
            ->orderBy('created_at', 'desc')
            ->get();

        $otherBlogs = DB::table('blog_posts')
            ->join('instructors', 'blog_posts.instructor_id', '=', 'instructors.id')
            ->join('blog_categories', 'blog_posts.blog_category_id', '=', 'blog_categories.id')
            ->select('blog_posts.*', 'instructors.name as author_name', 'blog_categories.name as category_name')
            ->where('blog_posts.status', 'active')
            ->where('blog_posts.id', '!=', $post->id)
            ->orderBy('blog_posts.created_at', 'desc')
            ->limit(5)
            ->get();

        if (!$author) {
            $author = (object) [
                'name' => 'Unknown Author',
                'email' => null,
                'photo' => null,
                'bio' => null,
            ];
        }

        return view('frontend.blog.blog_detail', compact('post', 'author', 'category', 'comments', 'otherBlogs'));
    }

    public function storeComment(Request $request, $slug)
    {
        $post = DB::table('blog_posts')->where('slug', $slug)->where('status', 'active')->first();
        if (!$post) {
            abort(404, 'Blog post not found');
        }

        $request->validate(['message' => 'required|string|max:1000']);

        $comment = Comment::create([
            'user_id' => Auth::id(),
            'blog_post_id' => $post->id,
            'message' => $request->input('message'),
            'approved' => false,
        ]);

        $instructor = Instructor::find($post->instructor_id);
        if ($instructor) {
            $instructor->notify(new BlogCommentNotification($post, $comment));
        }

        return redirect()->route('blog.detail', $slug)->with('success', 'Your comment has been submitted and is awaiting approval.');
    }

    public function replyComment(Request $request, $slug, $commentId)
    {
        $post = DB::table('blog_posts')->where('slug', $slug)->where('status', 'active')->first();
        if (!$post) {
            abort(404, 'Blog post not found');
        }

        $request->validate(['message' => 'required|string|max:1000']);

        if (Auth::check()) {
            try {
                CommentReply::create([
                    'comment_id' => $commentId,
                    'user_id' => Auth::id(),
                    'user_type' => \App\Models\User::class,
                    'message' => $request->input('message'),
                    'approved' => false,
                ]);
            } catch (\Exception $e) {
                return redirect()->route('blog.detail', $slug)
                    ->with('error', 'Failed to post reply: ' . $e->getMessage());
            }

            return redirect()->route('blog.detail', $slug)->with('success', 'Your reply has been submitted and is awaiting approval.');
        }

        return redirect()->route('blog.detail', $slug)->with('error', 'You must be logged in to reply.');
    }
}