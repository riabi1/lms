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
                    'post_id' => $comment->blog_post_id,
                    'post_title' => $comment->blogPost->title ?? 'N/A',
                    'user_name' => $comment->user->name ?? 'Unknown',
                    'type' => 'comment',
                    'parent_id' => null,
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
                    'post_id' => $reply->comment->blog_post_id ?? null,
                    'post_title' => $reply->comment->blogPost->title ?? 'N/A',
                    'user_name' => $reply->user->name ?? 'Unknown',
                    'type' => 'reply',
                    'parent_id' => $reply->comment_id ?? null,
                ];
            });

        // Group items by blog post
        $groupedItems = $comments->concat($replies)
            ->groupBy('post_id')
            ->map(function ($items, $postId) {
                return [
                    'post_id' => $postId,
                    'post_title' => $items->first()['post_title'],
                    'items' => $items->sortByDesc('created_at')->values()
                ];
            })
            ->sortBy('post_title')
            ->values();

        return view('admin.comments.index', compact('groupedItems'));
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

            return response()->json(['success' => true, 'message' => 'Status updated successfully.', 'approved' => $item->approved]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Failed to update status: ' . $e->getMessage()], 500);
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