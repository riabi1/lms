<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BlogPost;

class BlogPostsController extends Controller
{
    public function index()
    {
        $posts = BlogPost::with([
            'category' => function ($query) {
                $query->select('id', 'name');
            },
            'instructor' => function ($query) {
                $query->select('id', 'name');
            }
        ])->latest()->paginate(10);

        return view('admin.blog_posts.index', compact('posts'));
    }

    public function toggle($id)
    {
        $post = BlogPost::findOrFail($id);
        $post->status = $post->status === 'active' ? 'inactive' : 'active';
        $post->save();

        return redirect()->back()->with('success', 'Blog post status updated successfully');
    }
}