<?php

namespace App\Notifications;

use Illuminate\Notifications\Notification;

class NewMessageNotification extends Notification
{
    protected $conversation;
    protected $message;
    protected $sender;

    public function __construct($conversation, $message, $sender)
    {
        $this->conversation = $conversation;
        $this->message = $message;
        $this->sender = $sender;
    }

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toArray($notifiable)
    {
        return [
            'type' => 'message',
            'conversation_id' => $this->conversation->id,
            'message_id' => $this->message->id,
            'sender_id' => $this->sender->id,
            'sender_name' => $this->sender->name,
            'message' => "New message from {$this->sender->name}: " . \Str::limit($this->message->message, 50),
        ];
    }
}