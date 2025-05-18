<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class FeedbackReceivedNotification extends Notification
{
    use Queueable;

    protected $report;

    public function __construct($report)
    {
        $this->report = $report;
    }

    public function via($notifiable)
    {
        return ['mail', 'database'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('New Feedback Received for Report')
            ->line('A reporter has provided feedback for the report titled: ' . $this->report->title)
            ->line('Feedback: ' . $this->report->feedback)
            ->action('View Report', route('admin.reports.index'))
            ->line('Thank you for addressing this report!');
    }

    public function toArray($notifiable)
    {
        return [
            'type' => 'feedback_received',
            'report_id' => $this->report->id,
            'title' => $this->report->title,
            'feedback' => $this->report->feedback,
        ];
    }
}