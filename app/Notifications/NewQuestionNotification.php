<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\CourseQuestion;
use App\Models\Course;
use App\Models\User;

class NewQuestionNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $question;
    protected $course;
    protected $user;

    public function __construct(CourseQuestion $question, Course $course, User $user)
    {
        $this->question = $question;
        $this->course = $course;
        $this->user = $user;
    }

    public function via($notifiable)
    {
        return ['database', 'mail'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('New Question for Your Course: ' . $this->course->course_name)
            ->line('A new question has been asked by ' . $this->user->name . ' for your course "' . $this->course->course_name . '".')
            ->line('Question: ' . $this->question->question_text)
            ->action('View Question', url('/instructor/course/' . $this->course->id . '/questions'))
            ->line('Thank you for being an instructor with us!');
    }

    public function toArray($notifiable)
    {
        return [
            'type' => 'new_question',
            'course_id' => $this->course->id,
            'course_title' => $this->course->course_name,
            'question_id' => $this->question->id,
            'question_text' => $this->question->question_text,
            'user_name' => $this->user->name,
            'message' => "New question by {$this->user->name} for your course '{$this->course->course_name}'",
        ];
    }
}