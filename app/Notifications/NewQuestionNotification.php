<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class NewQuestionNotification extends Notification
{
    public $question;
    public $userName;

    public function __construct($question, $userName)
    {
        $this->question = $question;
        $this->userName = $userName;
    }

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toArray($notifiable)
    {
        return [
            'question_id' => $this->question->id,
            'course_id' => $this->question->course_id,
            'course_title' => $this->question->course->course_name,
            'user_name' => $this->userName,
            'type' => 'question',
            'message' => "The user {$this->userName} has submitted a question for your course '{$this->question->course->course_name}'.",
        ];
    }
}