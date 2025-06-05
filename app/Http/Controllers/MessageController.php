<?php

namespace App\Http\Controllers;

use App\Events\MessageSent;
use App\Models\Conversation;
use App\Models\Message;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MessageController extends Controller
{
    public function show($conversationId)
    {
        $conversation = Conversation::with('messages', 'instructor', 'user')->findOrFail($conversationId);
        $conversations = Auth::guard('instructor')->check()
            ? Auth::guard('instructor')->user()->conversations
            : Auth::user()->conversations;

        return view(Auth::guard('instructor')->check() ? 'instructor.chat' : 'User.chat', [
            'selectedConversation' => $conversation,
            'conversations' => $conversations,
        ]);
    }

    public function send(Request $request, $conversationId)
    {
        $request->validate([
            'message' => 'required|string',
        ]);

        $conversation = Conversation::findOrFail($conversationId);

        $message = Message::create([
            'conversation_id' => $conversationId,
            'sender_id' => Auth::guard('instructor')->check() ? Auth::guard('instructor')->id() : Auth::id(),
            'sender_type' => Auth::guard('instructor')->check() ? 'App\\Models\\Instructor' : 'App\\Models\\User',
            'message' => $request->message,
        ]);

        // Trigger event
        event(new MessageSent($message));

        // Get the authenticated user or instructor
        $authUser = Auth::guard('instructor')->check() ? Auth::guard('instructor')->user() : Auth::user();

        return response()->json([
            'status' => 'success',
            'message' => [
                'message_id' => $message->id,
                'message' => $message->message,
                'sender_name' => $authUser->name ?? 'Anonymous',
                'sender_photo' => $authUser->photo ? asset('upload/' . (Auth::guard('instructor')->check() ? 'instructor_images' : 'user_images') . '/' . $authUser->photo) : asset('upload/no_image.jpg'),
            ],
            'conversation' => $conversation,
        ]);
    }
}
