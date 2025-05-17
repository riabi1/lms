<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Report;
use Illuminate\Http\Request;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class NotificationController extends Controller
{
    public function index()
    {
        $notifications = Auth::guard('web')->user()->notifications()->latest()->get();
        return view('notifications.index', compact('notifications'));
    }

    public function markAsRead(Request $request, $id)
    {
        $notification = Auth::guard('web')->user()->notifications()->findOrFail($id);
        $notification->markAsRead();
        return redirect()->back()->with('success', 'Notification marked as read.');
    }

    public function markAllAsRead(Request $request)
    {
        Auth::guard('web')->user()->unreadNotifications()->update(['read_at' => now()]);
        return redirect()->back()->with('success', 'All notifications marked as read.');
    }

    public function read(DatabaseNotification $notification, Request $request)
    {
        try {
            // Verify the notification belongs to the user
            if ($notification->notifiable_type === 'App\Models\User' && $notification->notifiable_id === Auth::guard('web')->id()) {
                // Mark as read
                $notification->markAsRead();
                Log::info("Notification {$notification->id} marked as read for user ID " . Auth::guard('web')->id());

                // Redirect to report details for report_resolution
                if ($notification->data['type'] === 'report_resolution' && $request->has('report_id')) {
                    $report = Report::find($request->query('report_id'));
                    if ($report && ($report->reporter_type === 'App\Models\User' && $report->reporter_id === Auth::guard('web')->id())) {
                        return redirect()->route('reports.show', $report->id);
                    } else {
                        Log::warning("Invalid report ID {$request->query('report_id')} for notification {$notification->id}");
                        return redirect()->route('notifications.index')->with('error', 'Report not found or not accessible.');
                    }
                }

                // Fallback redirect
                return redirect()->route('notifications.index');
            } else {
                Log::warning("Unauthorized access to notification {$notification->id} by user ID " . Auth::guard('web')->id());
                return redirect()->route('notifications.index')->with('error', 'Unauthorized access to notification.');
            }
        } catch (\Exception $e) {
            Log::error("Failed to mark notification {$notification->id} as read: {$e->getMessage()}", ['exception' => $e->getTraceAsString()]);
            return redirect()->route('notifications.index')->with('error', 'Failed to process notification.');
        }
    }
}