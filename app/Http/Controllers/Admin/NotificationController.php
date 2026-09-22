<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
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
