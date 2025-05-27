<?php

namespace App\Http\Controllers;

use App\Models\Conversation;
use App\Models\Message;
use App\Notifications\NewMessageNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Notification;
use App\Events\MessageSent;

class MessageController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:web,instructor');
    }

    public function index()
    {
        $conversations = Conversation::where('user_id', Auth::id())
            ->orWhere('instructor_id', Auth::id())
            ->with(['user', 'instructor', 'messages' => function ($query) {
                $query->orderBy('created_at', 'asc');
            }])
            ->orderBy('last_message_at', 'desc')
            ->get();

        $selectedConversation = $conversations->first();

        $view = Auth::guard('web')->check() ? 'User.chat' : 'instructor.chat';

        return view($view, compact('conversations', 'selectedConversation'));
    }

    public function show(Conversation $conversation)
    {
        if ($conversation->user_id !== Auth::id() && $conversation->instructor_id !== Auth::id()) {
            abort(403, 'Unauthorized');
        }

        $conversations = Conversation::where('user_id', Auth::id())
            ->orWhere('instructor_id', Auth::id())
            ->with(['user', 'instructor', 'messages' => function ($query) {
                $query->orderBy('created_at', 'asc');
            }])
            ->orderBy('last_message_at', 'desc')
            ->get();

        $conversation->load(['messages' => function ($query) {
            $query->orderBy('created_at', 'asc');
        }]);

        $view = Auth::guard('web')->check() ? 'User.chat' : 'instructor.chat';

        return view($view, [
            'conversations' => $conversations,
            'selectedConversation' => $conversation,
        ]);
    }

    public function send(Request $request, Conversation $conversation)
    {
        $request->validate([
            'message' => 'required|string|max:1000',
        ]);

        if ($conversation->user_id !== Auth::id() && $conversation->instructor_id !== Auth::id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $message = Message::create([
            'conversation_id' => $conversation->id,
            'sender_id' => Auth::id(),
            'sender_type' => Auth::guard('web')->check() ? 'App\\Models\\User' : 'App\\Models\\Instructor',
            'message' => $request->message,
        ]);

        $conversation->update(['last_message_at' => now()]);

        $sender = Auth::user();
        $sender_photo = $sender->photo
            ? asset('upload/' . ($sender instanceof \App\Models\User ? 'user_images' : 'instructor_images') . '/' . $sender->photo)
            : asset('upload/no_image.jpg');
        broadcast(new MessageSent($message, $sender->name, $sender_photo))->toOthers();

        if (Auth::guard('web')->check()) {
            $instructor = $conversation->instructor;
            if ($instructor) {
                Notification::send($instructor, new NewMessageNotification($conversation, $message, Auth::user()));
            }
        } elseif (Auth::guard('instructor')->check()) {
            $user = $conversation->user;
            if ($user) {
                Notification::send($user, new NewMessageNotification($conversation, $message, Auth::guard('instructor')->user()));
            }
        }

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

    public function typing(Request $request, Conversation $conversation)
    {
        if ($conversation->user_id !== Auth::id() && $conversation->instructor_id !== Auth::id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        broadcast(new \App\Events\Typing(Auth::user(), $conversation->id))->toOthers();

        return response()->json(['status' => 'success']);
    }

    public function markNotificationAsRead($notificationId)
    {
        $user = Auth::guard('instructor')->check() ? Auth::guard('instructor')->user() : Auth::guard('web')->user();
        $notification = $user->notifications()->findOrFail($notificationId);

        $notification->markAsRead();

        if ($notification->data['type'] === 'message' && !empty($notification->data['conversation_id'])) {
            $route = Auth::guard('instructor')->check() ? 'instructor.messages.show' : 'messages.show';
            return redirect()->route($route, $notification->data['conversation_id']);
        }

        $route = Auth::guard('instructor')->check() ? 'instructor.dashboard' : 'user.dashboard';
        return redirect()->route($route);
    }
}
