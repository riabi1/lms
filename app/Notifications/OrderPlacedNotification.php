<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class OrderPlacedNotification extends Notification 
{
    use Queueable;

    protected $order;

    public function __construct($order)
    {
        $this->order = $order;
    }

    public function via($notifiable)
    {
        return ['database']; // Stocke dans la base de données
    }

    public function toDatabase($notifiable)
    {
        return [
            'order_id' => $this->order->id,
            'course_title' => $this->order->course_title,
            'user_name' => $this->order->user->name,
            'type' => 'order',
            'message' => "L'utilisateur {$this->order->user->name} a passé une commande pour votre cours '{$this->order->course_title}'.",
        ];
    }
}