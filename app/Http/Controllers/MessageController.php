<?php

namespace App\Http\Controllers;

use App\Models\Conversation;
use App\Models\Message;
use App\Notifications\NewMessageNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Notification;

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
            ->with(['user', 'instructor', 'messages'])
            ->orderBy('last_message_at', 'desc')
            ->get();

        $selectedConversation = $conversations->first();

        $view = Auth::guard('web')->check() ? 'User.chat' : 'Instructor.chat';

        return view($view, compact('conversations', 'selectedConversation'));
    }

    public function show(Conversation $conversation)
    {
        if ($conversation->user_id !== Auth::id() && $conversation->instructor_id !== Auth::id()) {
            abort(403, 'Unauthorized');
        }

        $conversations = Conversation::where('user_id', Auth::id())
            ->orWhere('instructor_id', Auth::id())
            ->with(['user', 'instructor', 'messages'])
            ->orderBy('last_message_at', 'desc')
            ->get();

        $conversation->load(['messages' => function ($query) {
            $query->orderBy('created_at', 'asc');
        }]);

        $view = Auth::guard('web')->check() ? 'User.chat' : 'Instructor.chat';

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
        abort(403, 'Unauthorized');
    }

    $message = Message::create([
        'conversation_id' => $conversation->id,
        'sender_id' => Auth::id(),
        'sender_type' => Auth::guard('web')->check() ? 'App\\Models\\User' : 'App\\Models\\Instructor',
        'message' => $request->message,
    ]);

    $conversation->update(['last_message_at' => now()]);

    // Notifier l'instructeur si l'expéditeur est un utilisateur
    if (Auth::guard('web')->check()) {
        $instructor = $conversation->instructor;
        if ($instructor) {
            Notification::send($instructor, new NewMessageNotification($conversation, $message, Auth::user()));
        }
    }

    $route = Auth::guard('web')->check() ? 'messages.show' : 'instructor.messages.show';

    return redirect()->route($route, $conversation->id);
}

public function markNotificationAsRead($notificationId)
{
    $instructor = Auth::guard('instructor')->user();
    $notification = $instructor->notifications()->findOrFail($notificationId);

    // Marquer comme lu
    $notification->markAsRead();

    // Rediriger selon le type de notification
    if ($notification->data['type'] === 'message' && !empty($notification->data['conversation_id'])) {
        return redirect()->route('instructor.messages.show', $notification->data['conversation_id']);
    }

    // Par défaut, retourner au tableau de bord
    return redirect()->route('instructor.dashboard');
}
}