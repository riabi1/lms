<?php

namespace App\Http\Controllers\Admin;

use App\Models\Report;
use App\Models\User;
use App\Models\Instructor;
use App\Models\Admin; 
use App\Models\ReportStatusHistory;
use Illuminate\Http\Request;
use App\Models\ReportCategory;
use App\Http\Controllers\Controller;
use App\Notifications\ReportResolvedNotification;
use App\Notifications\FeedbackReceivedNotification;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class ReportController extends Controller
{
    public function index()
    {
        $reports = Report::with(['reporter', 'course', 'reportCategory'])->latest()->get();
        return view('admin.reports.index', compact('reports'));
    }

    public function update(Request $request, Report $report)
    {
        $validated = $request->validate([
            'status' => 'required|in:pending,fixed,not_fixed',
            'resolution_notes' => 'nullable|string|max:1000',
        ]);

        try {
            // Store status history
            ReportStatusHistory::create([
                'report_id' => $report->id,
                'status' => $validated['status'],
                'resolution_notes' => $validated['resolution_notes'],
                'changed_by_id' => Auth::guard('admin')->id(),
                'changed_by_type' => 'App\Models\Admin',
                'changed_at' => now(),
            ]);

            // Update report
            $report->update([
                'status' => $validated['status'],
                'resolution_notes' => $validated['resolution_notes'],
                'updated_at' => now(),
            ]);

            // Notify the reporter
            $notifiable = null;
            if ($report->reporter_type === 'App\Models\User') {
                $notifiable = User::find($report->reporter_id);
                if (!$notifiable) {
                    Log::error("User not found for report ID {$report->id}, reporter_id: {$report->reporter_id}");
                }
            } elseif ($report->reporter_type === 'App\Models\Instructor') {
                $notifiable = Instructor::find($report->reporter_id);
                if (!$notifiable) {
                    Log::error("Instructor not found for report ID {$report->id}, reporter_id: {$report->reporter_id}");
                }
            } else {
                Log::error("Invalid reporter_type for report ID {$report->id}: {$report->reporter_type}");
            }

            if ($notifiable && in_array($validated['status'], ['fixed', 'not_fixed'])) {
                Log::info("Sending notification for report ID {$report->id} to " . get_class($notifiable) . " ID {$notifiable->id}");
                try {
                    Notification::send($notifiable, new ReportResolvedNotification($report));
                    Log::info("Notification sent successfully for report ID {$report->id} to " . get_class($notifiable) . " ID {$notifiable->id}");
                } catch (\Exception $e) {
                    Log::error("Failed to send notification for report ID {$report->id} to " . get_class($notifiable) . " ID {$notifiable->id}: {$e->getMessage()}", [
                        'exception' => $e->getTraceAsString(),
                        'notifiable_type' => $report->reporter_type,
                        'notifiable_id' => $report->reporter_id,
                        'report_data' => [
                            'id' => $report->id,
                            'title' => $report->title,
                            'status' => $report->status,
                            'resolution_notes' => $report->resolution_notes,
                        ],
                    ]);
                }
            } else {
                Log::warning("Notification not sent for report ID {$report->id}. Notifiable: " . ($notifiable ? get_class($notifiable) : 'null') . ", Status: {$validated['status']}, Reporter ID: {$report->reporter_id}, Reporter Type: {$report->reporter_type}");
            }

            return redirect()->route('admin.reports.index')
                ->with('success', 'Report updated successfully.');
        } catch (\Exception $e) {
            Log::error("Failed to update report ID {$report->id}: {$e->getMessage()}", ['exception' => $e->getTraceAsString()]);
            return redirect()->back()
                ->with('error', 'Failed to update report: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function storeFeedback(Request $request, Report $report)
    {
        $validated = $request->validate([
            'feedback' => 'required|string|max:1000',
        ]);

        try {
            $report->update([
                'feedback' => $validated['feedback'],
                'updated_at' => now(),
            ]);

            // Notify admins about feedback
            $admins = Admin::all();
            Notification::send($admins, new FeedbackReceivedNotification($report));

            return redirect()->route('instructor.reports.show', $report->id)
                ->with('success', 'Feedback submitted successfully.');
        } catch (\Exception $e) {
            Log::error("Failed to submit feedback for report ID {$report->id}: {$e->getMessage()}", ['exception' => $e->getTraceAsString()]);
            return redirect()->back()
                ->with('error', 'Failed to submit feedback: ' . $e->getMessage())
                ->withInput();
        }
    }
}