<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DriverRegistration;

class DriverApplicationController extends Controller
{
    public function index()
    {
        $applications = DriverRegistration::latest()->paginate(15);
        $totalPending  = DriverRegistration::where('status', 'pending')->count();
        $totalApproved = DriverRegistration::where('status', 'approved')->count();
        $totalRejected = DriverRegistration::where('status', 'rejected')->count();

        return view('backend.driver-applications', compact(
            'applications', 'totalPending', 'totalApproved', 'totalRejected'
        ));
    }

    public function updateStatus(DriverRegistration $application, $status)
    {
        $application->update(['status' => $status]);

        return back()->with('success', "Application #{$application->id} marked as {$status}.");
    }
}
