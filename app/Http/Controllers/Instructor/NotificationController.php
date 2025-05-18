<?php

namespace App\Http\Controllers\Instructor;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    public function index()
    {
        $notifications = Auth::guard('instructor')->user()->notifications()->latest()->get();
        return view('instructor.notifications.index', compact('notifications'));
    }

    public function markAsRead(Request $request, $id)
    {
        $notification = Auth::guard('instructor')->user()->notifications()->findOrFail($id);
        $notification->markAsRead();
        return redirect()->back()->with('success', 'Notification marked as read.');
    }

    public function markAllAsRead(Request $request)
    {
        Auth::guard('instructor')->user()->unreadNotifications()->update(['read_at' => now()]);
        return redirect()->back()->with('success', 'All notifications marked as read.');
    }
}