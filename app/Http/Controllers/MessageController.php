<?php

namespace App\Http\Controllers;

use App\Models\Conversation;
use App\Models\Message;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MessageController extends Controller
{
    public function index(Request $request)
    {
        $guard = Auth::guard('instructor')->check() ? 'instructor' : 'web';
        $user = Auth::guard($guard)->user();

        // Create conversations for paid orders
        $this->ensureConversations($user, $guard);

        // Fetch all conversations for the user or instructor
        $conversations = Conversation::where('user_id', $user->id)
            ->orWhere('instructor_id', $user->id)
            ->with(['user', 'instructor', 'messages' => function ($query) {
                $query->orderBy('created_at', 'desc')->take(1);
            }])
            ->orderBy('last_message_at', 'desc')
            ->get();

        $view = $guard === 'instructor' ? 'instructor.chat' : 'User.chat';

        return view($view, [
            'conversations' => $conversations,
            'selectedConversation' => null,
        ]);
    }

    protected function ensureConversations($user, $guard)
    {
        // Fetch paid orders for the user or instructor
        $query = Order::where('payment_status', 'paid');
        $query->where($guard === 'instructor' ? 'instructor_id' : 'user_id', $user->id);
        $orders = $query->get();

        foreach ($orders as $order) {
            // Check if a conversation exists
            $exists = Conversation::where('user_id', $order->user_id)
                ->where('instructor_id', $order->instructor_id)
                ->exists();

            if (!$exists) {
                Conversation::create([
                    'user_id' => $order->user_id,
                    'instructor_id' => $order->instructor_id,
                    'last_message_at' => now(),
                ]);
            }
        }
    }

    public function show(Request $request, Conversation $conversation)
    {
        $guard = Auth::guard('instructor')->check() ? 'instructor' : 'web';
        $user = Auth::guard($guard)->user();

        // Ensure the user or instructor is part of the conversation
        if ($conversation->user_id !== $user->id && $conversation->instructor_id !== $user->id) {
            return redirect()->route($guard === 'instructor' ? 'instructor.messages.index' : 'messages.index')
                ->with('error', 'Unauthorized access.');
        }

        $conversations = Conversation::where('user_id', $user->id)
            ->orWhere('instructor_id', $user->id)
            ->with(['user', 'instructor', 'messages' => function ($query) {
                $query->orderBy('created_at', 'desc')->take(1);
            }])
            ->orderBy('last_message_at', 'desc')
            ->get();

        $conversation->load(['messages' => function ($query) {
            $query->orderBy('created_at', 'asc');
        }, 'user', 'instructor']);

        $view = $guard === 'instructor' ? 'instructor.chat' : 'User.chat';

        return view($view, [
            'conversations' => $conversations,
            'selectedConversation' => $conversation,
        ]);
    }

    public function send(Request $request, Conversation $conversation)
    {
        if ($conversation->user_id !== Auth::id() && $conversation->instructor_id !== Auth::id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $request->validate(['message' => 'required|string|max:1000']);

        $message = Message::create([
            'conversation_id' => $conversation->id,
            'sender_id' => Auth::id(),
            'sender_type' => Auth::guard('instructor')->check() ? 'App\\Models\\Instructor' : 'App\\Models\\User',
            'message' => $request->input('message'),
        ]);

        $conversation->update(['last_message_at' => now()]);

        // Broadcast the message using Reverb
        broadcast(new \App\Events\MessageSent($message))->toOthers();

        $sender = Auth::user();
        $sender_photo = $sender->photo
            ? asset('upload/' . ($sender instanceof \App\Models\User ? 'user_images' : 'instructor_images') . '/' . $sender->photo)
            : asset('upload/no_image.jpg');

        return response()->json([
            'status' => 'success',
            'message' => [
                'message_id' => $message->id,
                'conversation_id' => $message->conversation_id,
                'message' => $message->message,
                'sender_id' => $message->sender_id,
                'sender_type' => $message->sender_type,
                'sender_name' => $sender->name,
                'sender_photo' => $sender_photo,
                'created_at' => $message->created_at->toDateTimeString(),
            ],
            'conversation' => [
                'id' => $conversation->id,
                'last_message_at' => $conversation->last_message_at->toDateTimeString(),
            ],
        ]);
    }
}