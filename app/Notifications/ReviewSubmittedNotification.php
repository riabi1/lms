<?php

namespace App\Notifications;

use Illuminate\Notifications\Notification;
use App\Models\Course;

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
        // Vérifier si l'avis est lié à un cours
        $course = $this->review->reviewable_type === 'App\Models\Course' 
            ? $this->review->reviewable 
            : null;

        // Si pas de cours, retourner un message par défaut ou lever une exception selon vos besoins
        if (!$course) {
            return [
                'review_id' => $this->review->id,
                'course_id' => null,
                'course_title' => 'Unknown Course',
                'user_name' => $this->review->user->name,
                'rating' => $this->review->rating,
                'type' => 'review',
                'message' => "L'utilisateur {$this->review->user->name} a soumis un avis (note : {$this->review->rating}/5) pour un cours inconnu.",
            ];
        }

        return [
            'review_id' => $this->review->id,
            'course_id' => $course->id,
            'course_title' => $course->course_name,
            'user_name' => $this->review->user->name,
            'rating' => $this->review->rating,
            'type' => 'review',
            'message' => "L'utilisateur {$this->review->user->name} a soumis un avis (note : {$this->review->rating}/5) pour votre cours '{$course->course_name}'.",
        ];
    }
}