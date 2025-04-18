<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Comment;
use App\Models\CommentReply;

class CommentController extends Controller
{
    public function index()
    {
        // Load comments with their blog post and user
        $comments = Comment::with(['blogPost', 'user'])
            ->select('id', 'message', 'approved', 'created_at', 'blog_post_id', 'user_id')
            ->get()
            ->map(function ($comment) {
                return [
                    'id' => $comment->id,
                    'message' => $comment->message,
                    'approved' => $comment->approved,
                    'created_at' => $comment->created_at,
                    'post_title' => $comment->blogPost->title ?? 'N/A',
                    'user_name' => $comment->user->name ?? 'Unknown',
                    'type' => 'comment',
                    'parent_comment' => null,
                ];
            });

        // Load replies with their comment, blog post, and polymorphic user
        $replies = CommentReply::with(['comment.blogPost', 'user'])
            ->select('id', 'message', 'approved', 'created_at', 'comment_id')
            ->get()
            ->map(function ($reply) {
                return [
                    'id' => $reply->id,
                    'message' => $reply->message,
                    'approved' => $reply->approved,
                    'created_at' => $reply->created_at,
                    'post_title' => $reply->comment->blogPost->title ?? 'N/A',
                    'user_name' => $reply->user->name ?? 'Unknown',
                    'type' => 'reply',
                    'parent_comment' => $reply->comment->message ?? 'N/A',
                ];
            });

        // Merge and sort by created_at
        $items = $comments->concat($replies)->sortByDesc('created_at')->values();

        return view('admin.comments.index', compact('items'));
    }

    public function toggleApproval(Request $request, $id, $type = 'comment')
    {
        try {
            if ($type === 'comment') {
                $item = Comment::findOrFail($id);
            } else {
                $item = CommentReply::findOrFail($id);
            }

            $item->approved = !$item->approved;
            $item->save();

            return redirect()->route('admin.comments.index')->with('success', 'Status updated successfully.');
        } catch (\Exception $e) {
            return redirect()->route('admin.comments.index')->with('error', 'Failed to update status: ' . $e->getMessage());
        }
    }

    public function destroy($id, $type = 'comment')
    {
        try {
            if ($type === 'comment') {
                $item = Comment::findOrFail($id);
            } else {
                $item = CommentReply::findOrFail($id);
            }

            $item->delete();

            return redirect()->route('admin.comments.index')->with('success', 'Item deleted successfully.');
        } catch (\Exception $e) {
            return redirect()->route('admin.comments.index')->with('error', 'Failed to delete item: ' . $e->getMessage());
        }
    }
}