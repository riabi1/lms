<?php

namespace App\Http\Controllers;

use App\Models\Conversation;
use App\Models\Message;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MessageController extends Controller
{
    public function index(Request $request)
    {
        $guard = Auth::guard('instructor')->check() ? 'instructor' : 'web';
        $user = Auth::guard($guard)->user();

        $conversations = Conversation::where('user_id', $user->id)
            ->orWhere('instructor_id', $user->id)
            ->with(['user', 'instructor', 'messages' => function ($query) {
                $query->orderBy('created_at', 'desc')->take(1);
            }])
            ->orderBy('last_message_at', 'desc')
            ->get();

        $selectedConversation = null;
        $view = $guard === 'instructor' ? 'instructor.chat' : 'User.chat';

        return view($view, [
            'conversations' => $conversations,
            'selectedConversation' => $selectedConversation,
        ]);
    }

    public function show(Request $request, Conversation $conversation)
    {
        $guard = Auth::guard('instructor')->check() ? 'instructor' : 'web';
        $user = Auth::guard($guard)->user();

        if ($conversation->user_id !== $user->id && $conversation->instructor_id !== $user->id) {
            return redirect()->route($guard === 'instructor' ? 'instructor.messages.index' : 'messages.index')
                ->with('error', 'Unauthorized access to conversation.');
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

    public function fetchNewMessages(Request $request, Conversation $conversation)
    {
        if ($conversation->user_id !== Auth::id() && $conversation->instructor_id !== Auth::id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $lastMessageId = $request->query('last_message_id', 0);

        $messages = Message::where('conversation_id', $conversation->id)
            ->where('id', '>', $lastMessageId)
            ->orderBy('created_at', 'asc')
            ->get()
            ->map(function ($message) {
                $sender = $message->sender_type === 'App\\Models\\User'
                    ? \App\Models\User::find($message->sender_id)
                    : \App\Models\Instructor::find($message->sender_id);

                $sender_photo = $sender->photo
                    ? asset('upload/' . ($sender instanceof \App\Models\User ? 'user_images' : 'instructor_images') . '/' . $sender->photo)
                    : asset('upload/no_image.jpg');

                return [
                    'message_id' => $message->id,
                    'conversation_id' => $message->conversation_id,
                    'message' => $message->message,
                    'sender_id' => $message->sender_id,
                    'sender_type' => $message->sender_type,
                    'sender_name' => $sender->name,
                    'sender_photo' => $sender_photo,
                    'created_at' => $message->created_at->toDateTimeString(),
                ];
            });

        $conversationData = [
            'id' => $conversation->id,
            'last_message_at' => $conversation->last_message_at->toDateTimeString(),
        ];

        return response()->json([
            'status' => 'success',
            'messages' => $messages,
            'conversation' => $conversationData,
        ]);
    }

    public function typing(Request $request, Conversation $conversation)
    {
        if ($conversation->user_id !== Auth::id() && $conversation->instructor_id !== Auth::id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $typingKey = 'typing:conversation:' . $conversation->id;
        \Illuminate\Support\Facades\Cache::put($typingKey, Auth::user()->name, now()->addSeconds(5));

        return response()->json(['status' => 'success']);
    }

    public function checkTypingStatus(Request $request, Conversation $conversation)
    {
        if ($conversation->user_id !== Auth::id() && $conversation->instructor_id !== Auth::id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $typingKey = 'typing:conversation:' . $conversation->id;
        $typingUser = \Illuminate\Support\Facades\Cache::get($typingKey);

        return response()->json([
            'status' => 'success',
            'typing' => $typingUser ? true : false,
            'user_name' => $typingUser,
        ]);
    }

    public function send(Request $request, Conversation $conversation)
    {
        if ($conversation->user_id !== Auth::id() && $conversation->instructor_id !== Auth::id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $request->validate(['message' => 'required|string|max:1000']);

        $message = new Message();
        $message->conversation_id = $conversation->id;
        $message->sender_id = Auth::id();
        $message->sender_type = Auth::guard('instructor')->check() ? 'App\\Models\\Instructor' : 'App\\Models\\User';
        $message->message = $request->input('message');
        $message->save();

        $conversation->last_message_at = now();
        $conversation->save();

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