<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\DriverNotification;
use App\Models\DriverRegistration;

class DriverNotificationController extends Controller
{
    private function getDriver()
    {
        return DriverRegistration::find(session('driver_id'));
    }

    public function index()
    {
        if (!session('driver_id')) return redirect()->route('driver.login');

        $driver = $this->getDriver();
        $notifications = DriverNotification::where('driver_id', $driver->id)->latest()->paginate(20);

        return view('frontend.driver.notifications.index', compact('driver', 'notifications'));
    }

    public function markAsRead($id)
    {
        if (!session('driver_id')) return response()->json(['success' => false], 401);

        $notification = DriverNotification::where('driver_id', session('driver_id'))->findOrFail($id);
        $notification->update(['is_read' => true]);

        if ($notification->action_url) {
            return redirect($notification->action_url);
        }

        return back()->with('success', 'Notification marked as read.');
    }

    public function markAllRead()
    {
        if (!session('driver_id')) return response()->json(['success' => false], 401);

        DriverNotification::where('driver_id', session('driver_id'))->update(['is_read' => true]);

        return back()->with('success', 'All notifications marked as read.');
    }
}
