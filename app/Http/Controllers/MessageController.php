<?php

namespace App\Http\Controllers;

use App\Events\MessageSent;
use App\Models\Conversation;
use App\Models\Message;
use App\Models\Order;
use App\Notifications\NewMessageNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;


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
              // Emit message via Socket.IO
              $messageData = [
                'conversation_id' => $conversation->id,
                'message_id' => $message->id,
                'message' => $message->message,
                'sender_id' => $authUser->id,
                'sender_type' => Auth::guard('instructor')->check() ? 'App\\Models\\Instructor' : 'App\\Models\\User',
                'sender_name' => $authUser->name ?? 'Anonymous',
                'sender_photo' => $authUser->photo ? asset('upload/' . (Auth::guard('instructor')->check() ? 'instructor_images' : 'user_images') . '/' . $authUser->photo) : asset('upload/no_image.jpg'),
                'created_at' => $message->created_at->toISOString(),
            ];
    
            Http::post('http://localhost:3000/send-message', $messageData);
    
            // Notify the recipient
            $recipient = Auth::guard('instructor')->check() ? $conversation->user : $conversation->instructor;
            $recipient->notify(new NewMessageNotification($conversation, $message, $authUser));
    
            // Emit Socket.IO notification
            $notificationData = [
                'id' => $message->id,
                'type' => 'message',
                'conversation_id' => $conversation->id,
                'message_id' => $message->id,
                'sender_id' => $authUser->id,
                'sender_name' => $authUser->name ?? 'Anonymous',
                'message' => $message->message,
                'report_id' => null,
            ];
    
            $recipientType = Auth::guard('instructor')->check() ? 'user' : 'instructor';
            Http::post('http://localhost:3000/send-notification', [
                'recipient_id' => $recipient->id,
                'recipient_type' => $recipientType,
                'notification' => $notificationData,
            ]);

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
