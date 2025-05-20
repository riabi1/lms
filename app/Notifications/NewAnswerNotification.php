<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\CourseQuestion;
use App\Models\Answer;
use App\Models\Course;

class NewAnswerNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $question;
    protected $answer;
    protected $course;
    protected $instructor;

    public function __construct(CourseQuestion $question, Answer $answer, Course $course, $instructor)
    {
        $this->question = $question;
        $this->answer = $answer;
        $this->course = $course;
        $this->instructor = $instructor;
    }

    public function via($notifiable)
    {
        return ['database', 'mail'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('Your Question Has Been Answered')
            ->line('Your question for the course "' . $this->course->course_name . '" has been answered by ' . $this->instructor->name . '.')
            ->line('**Question:** ' . $this->question->question_text)
            ->line('**Answer:** ' . $this->answer->answer_text)
            ->action('View Answer', url('/course/' . $this->course->id . '/learn#question-and-ans'))
            ->line('Thank you for learning with us!');
    }

    public function toArray($notifiable)
    {
        return [
            'type' => 'new_answer',
            'course_id' => $this->course->id,
            'course_title' => $this->course->course_name,
            'question_id' => $this->question->id,
            'answer_id' => $this->answer->id,
            'question_text' => $this->question->question_text,
            'answer_text' => $this->answer->answer_text,
            'instructor_name' => $this->instructor->name,
            'message' => "Your question for '{$this->course->course_name}' has been answered by {$this->instructor->name}",
        ];
    }
}