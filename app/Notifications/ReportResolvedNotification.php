<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Support\Facades\Log;

class ReportResolvedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $report;

    public function __construct($report)
    {
        $this->report = $report;
    }

    public function via($notifiable)
    {
        Log::info("Preparing notification for report ID {$this->report->id} to " . get_class($notifiable) . " ID {$notifiable->id}");
        return ['database', 'broadcast'];
    }

    public function toDatabase($notifiable)
    {
        try {
            $title = $this->report->title ?? 'Untitled Report';
            $status = $this->report->status ?? 'unknown';
            $resolutionNotes = $this->report->resolution_notes ?? 'No additional notes.';
            $statusMessage = in_array($status, ['fixed', 'not_fixed']) ? ($status === 'fixed' ? 'resolved' : 'not resolved') : 'updated';

            $data = [
                'type' => 'report_resolution',
                'report_id' => $this->report->id,
                'title' => $title,
                'message' => "Your report '{$title}' has been marked as {$statusMessage}. Resolution: {$resolutionNotes}",
            ];
            Log::info("Storing database notification for report ID {$this->report->id}", $data);
            return $data;
        } catch (\Exception $e) {
            Log::error("Failed to store database notification for report ID {$this->report->id}: {$e->getMessage()}", [
                'exception' => $e->getTraceAsString(),
                'report_data' => [
                    'id' => $this->report->id,
                    'title' => $this->report->title,
                    'status' => $this->report->status,
                    'resolution_notes' => $this->report->resolution_notes,
                ],
            ]);
            throw $e; // Let queue retry
        }
    }

    public function toBroadcast($notifiable)
    {
        try {
            $title = $this->report->title ?? 'Untitled Report';
            $status = $this->report->status ?? 'unknown';
            $resolutionNotes = $this->report->resolution_notes ?? 'No additional notes.';
            $statusMessage = in_array($status, ['fixed', 'not_fixed']) ? ($status === 'fixed' ? 'resolved' : 'not resolved') : 'updated';

            $data = [
                'type' => 'report_resolution',
                'report_id' => $this->report->id,
                'title' => $title,
                'message' => "Your report '{$title}' has been marked as {$statusMessage}. Resolution: {$resolutionNotes}",
                'created_at' => now()->toDateTimeString(),
                'id' => $this->id,
            ];
            Log::info("Broadcasting notification for report ID {$this->report->id}", $data);
            return new BroadcastMessage($data);
        } catch (\Exception $e) {
            Log::error("Failed to broadcast notification for report ID {$this->report->id}: {$e->getMessage()}", [
                'exception' => $e->getTraceAsString(),
                'report_data' => [
                    'id' => $this->report->id,
                    'title' => $this->report->title,
                    'status' => $this->report->status,
                    'resolution_notes' => $this->report->resolution_notes,
                ],
            ]);
            throw $e; // Let queue retry
        }
    }

    public function failed(\Exception $exception)
    {
        Log::error("Notification job failed for report ID {$this->report->id}: {$exception->getMessage()}", [
            'exception' => $exception->getTraceAsString(),
            'report_data' => [
                'id' => $this->report->id,
                'title' => $this->report->title,
                'status' => $this->report->status,
                'resolution_notes' => $this->report->resolution_notes,
            ],
        ]);
    }
}