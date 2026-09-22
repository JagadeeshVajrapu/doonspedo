<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\DriverRegistration;
use App\Models\User;

class DashboardController extends Controller
{
    public function index()
    {
        // Statistics
        $totalUsers = User::count();
        $totalDrivers = DriverRegistration::count();
        $pendingKyc = DriverRegistration::where('status', 'pending')->count();
        $approvedDrivers = DriverRegistration::where('status', 'approved')->count();
        $totalBranches = \App\Models\Branch::count();
        
        // Dynamic metrics (Tables don't exist yet, setting to 0 for now)
        $totalBookings = 0; 
        $totalRevenue = 0; 
        $activeRidesCount = 0; 
        
        // Recent drivers
        $recentDrivers = DriverRegistration::latest()->take(5)->get();

        // Active rides (Table doesn't exist yet)
        $activeRides = [];

        // Chart Data (Tables don't exist yet)
        $revenueChartData = [
            'labels' => ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'],
            'data' => [0, 0, 0, 0, 0, 0, 0]
        ];

        $bookingsChartData = [
            'labels' => ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'],
            'data' => [0, 0, 0, 0, 0, 0, 0]
        ];

        return view('backend.index', compact(
            'totalUsers', 'totalDrivers', 'pendingKyc', 'approvedDrivers', 'totalBranches',
            'totalBookings', 'totalRevenue', 'activeRidesCount',
            'recentDrivers', 'activeRides', 
            'revenueChartData', 'bookingsChartData'
        ));
    }

    public function search(Request $request)
    {
        $q = $request->input('q');
        if (empty($q)) {
            return redirect()->back();
        }

        $users = User::where('name', 'LIKE', "%{$q}%")
            ->orWhere('mobile', 'LIKE', "%{$q}%")
            ->orWhere('email', 'LIKE', "%{$q}%")
            ->take(10)->get();

        $drivers = DriverRegistration::where('name', 'LIKE', "%{$q}%")
            ->orWhere('mobile', 'LIKE', "%{$q}%")
            ->orWhere('email', 'LIKE', "%{$q}%")
            ->orWhere('vehicle_number', 'LIKE', "%{$q}%")
            ->take(10)->get();

        $bookings = \App\Models\Booking::where('id', 'LIKE', "%{$q}%")
            ->orWhere('pickup_location', 'LIKE', "%{$q}%")
            ->orWhere('dropoff_location', 'LIKE', "%{$q}%")
            ->take(10)->get();

        return view('backend.search', compact('users', 'drivers', 'bookings', 'q'));
    }
}
