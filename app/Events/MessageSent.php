<?php

namespace App\Events;

use App\Models\Message;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Support\Facades\Auth;

class MessageSent implements ShouldBroadcast
{
    use InteractsWithSockets;

    public $message;

    public function __construct(Message $message)
    {
        $this->message = $message;
    }

    public function broadcastOn()
    {
        return new PrivateChannel('conversation.' . $this->message->conversation_id);
    }

    public function broadcastWith()
    {
        $sender = $this->message->sender_type === 'App\\Models\\User'
            ? \App\Models\User::find($this->message->sender_id)
            : \App\Models\Instructor::find($this->message->sender_id);

        $sender_photo = $sender->photo
            ? asset('upload/' . ($sender instanceof \App\Models\User ? 'user_images' : 'instructor_images') . '/' . $sender->photo)
            : asset('upload/no_image.jpg');

        return [
            'message_id' => $this->message->id,
            'conversation_id' => $this->message->conversation_id,
            'message' => $this->message->message,
            'sender_id' => $this->message->sender_id,
            'sender_type' => $this->message->sender_type,
            'sender_name' => $sender->name,
            'sender_photo' => $sender_photo,
            'created_at' => $this->message->created_at->toDateTimeString(),
        ];
    }
}