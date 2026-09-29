<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdminNotification;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function index()
    {
        $notifications = AdminNotification::query()->latest()->paginate(20);

        return view('backend.notifications.index', compact('notifications'));
    }

    public function feed()
    {
        $latest = AdminNotification::query()->latest()->limit(5)->get([
            'id', 'title', 'message', 'is_read', 'action_url', 'created_at',
        ]);

        return response()->json([
            'unread' => AdminNotification::query()->where('is_read', false)->count(),
            'latest' => $latest->first(),
        ]);
    }

    public function open($id)
    {
        $notification = AdminNotification::query()->findOrFail($id);
        $notification->update(['is_read' => true]);

        return redirect($notification->action_url ?: route('admin.notifications.index'));
    }

    public function markAllRead()
    {
        AdminNotification::query()->where('is_read', false)->update(['is_read' => true]);

        return back()->with('success', 'All notifications marked as read.');
    }

    public function push()
    {
        return view('backend.notifications.push');
    }

    public function storePush(Request $request)
    {
        return back()->with('success', 'Push notification sent successfully.');
    }

    public function email()
    {
        return view('backend.notifications.email');
    }

    public function storeEmail(Request $request)
    {
        return back()->with('success', 'Email configuration updated successfully.');
    }

    public function sms()
    {
        return view('backend.notifications.sms');
    }

    public function storeSms(Request $request)
    {
        return back()->with('success', 'SMS configuration updated successfully.');
    }
}
