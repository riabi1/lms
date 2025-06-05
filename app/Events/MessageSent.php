<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;

class MessageSent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $message;

    public function __construct($message)
    {
        $this->message = $message;

        // Log event firing
        \Log::info('MessageSent event fired', [
            'conversation_id' => $message->conversation_id,
            'message_id' => $message->id,
            'sender_id' => $message->sender_id,
            'sender_type' => $message->sender_type,
            'sender_photo' => $message->sender->photo ? asset('upload/' . ($message->sender_type === 'App\\Models\\Instructor' ? 'instructor_images' : 'user_images') . '/' . $message->sender->photo) : asset('upload/no_image.jpg'),
        ]);

        // Send to Socket.IO server
        $response = Http::post('http://localhost:3000/send-message', [
            'conversationId' => $message->conversation_id,
            'message' => $message->message,
            'sender_id' => $message->sender_id,
            'sender_type' => $message->sender_type,
            'sender_name' => $message->sender->name ?? 'Anonymous',
            'sender_photo' => $message->sender->photo ? asset('upload/' . ($message->sender_type === 'App\\Models\\Instructor' ? 'instructor_images' : 'user_images') . '/' . $message->sender->photo) : asset('upload/no_image.jpg'),
            'message_id' => $message->id,
        ]);

        \Log::info('HTTP POST to Socket.IO server', [
            'status' => $response->status(),
            'body' => $response->body(),
        ]);
    }

    public function broadcastOn()
    {
        return new Channel('conversation.' . $this->message->conversation_id);
    }
}
