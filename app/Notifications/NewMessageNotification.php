<?php

namespace App\Notifications;

use App\Models\Conversation;
use App\Models\Message;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewMessageNotification extends Notification
{
    use Queueable;

    public $conversation;
    public $message;
    public $sender;

    public function __construct(Conversation $conversation, Message $message, User $sender)
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
            'message' => $this->message->message,
        ];
    }
}