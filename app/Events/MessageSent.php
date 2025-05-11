<?php

namespace App\Events;

use App\Models\Message;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class MessageSent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $message;

    public function __construct(Message $message)
    {
        $this->message = $message;
        Log::info('MessageSent event constructed', [
            'message_id' => $message->id,
            'conversation_id' => $message->conversation_id,
            'sender_id' => $message->sender_id,
            'sender_type' => $message->sender_type
        ]);
    }

    public function broadcastOn()
    {
        Log::info('Broadcasting MessageSent on channel', ['channel' => 'conversation.' . $this->message->conversation_id]);
        return new PrivateChannel('conversation.' . $this->message->conversation_id);
    }

    public function broadcastWith()
    {
        Log::info('Broadcasting MessageSent event data', ['message_id' => $this->message->id]);
        return [
            'message_id' => $this->message->id,
            'conversation_id' => $this->message->conversation_id,
            'message' => $this->message->message,
            'sender_id' => $this->message->sender_id,
            'sender_type' => $this->message->sender_type,
            'sender_name' => $this->message->sender_type === 'App\\Models\\User' 
                ? $this->message->conversation->user->name 
                : $this->message->conversation->instructor->name,
            'sender_photo' => $this->message->sender_type === 'App\\Models\\User'
                ? (\Storage::disk('public')->exists('upload/user_images/' . $this->message->conversation->user->photo)
                    ? asset('storage/upload/user_images/' . $this->message->conversation->user->photo)
                    : asset('upload/no_image.jpg'))
                : (file_exists(public_path('upload/instructor_images/' . $this->message->conversation->instructor->photo))
                    ? asset('upload/instructor_images/' . $this->message->conversation->instructor->photo)
                    : asset('upload/no_image.jpg')),
            'created_at' => $this->message->created_at->toDateTimeString(),
        ];
    }
}