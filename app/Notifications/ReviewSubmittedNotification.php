<?php

namespace App\Notifications;

use Illuminate\Notifications\Notification;

class ReviewSubmittedNotification extends Notification
{
    protected $review;

    public function __construct($review)
    {
        $this->review = $review;
    }

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toDatabase($notifiable)
    {
        return [
            'review_id' => $this->review->id,
            'course_id' => $this->review->course_id,
            'course_title' => $this->review->course->course_name,
            'user_name' => $this->review->user->name,
            'rating' => $this->review->rating,
            'type' => 'review',
            'message' => "L'utilisateur {$this->review->user->name} a soumis un avis (note : {$this->review->rating}/5) pour votre cours '{$this->review->course->course_name}'.",
        ];
    }
}