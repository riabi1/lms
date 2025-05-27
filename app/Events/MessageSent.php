<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use App\Models\Message;

class MessageSent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $message;
    public $sender_name;
    public $sender_photo;

    public function __construct(Message $message, $sender_name, $sender_photo)
    {
        $this->message = $message;
        $this->sender_name = $sender_name;
        $this->sender_photo = $sender_photo;
    }

    public function broadcastOn()
    {
        return new PrivateChannel('conversation.' . $this->message->conversation_id);
    }

    public function broadcastWith()
    {
        return [
            'message_id' => $this->message->id,
            'conversation_id' => $this->message->conversation_id,
            'message' => $this->message->message,
            'sender_id' => $this->message->sender_id,
            'sender_type' => $this->message->sender_type,
            'sender_name' => $this->sender_name,
            'sender_photo' => $this->sender_photo,
            'created_at' => $this->message->created_at->toDateTimeString(),
        ];
    }
}