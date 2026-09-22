<?php

namespace App\Http\Controllers\Branch;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\DriverRegistration;
use App\Models\Booking;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $branch = Auth::guard('branch')->user();
        
        // Filter data for this branch only
        $totalDrivers = DriverRegistration::where('branch_id', $branch->id)->count();
        $totalBookings = Booking::where('branch_id', $branch->id)->count();
        $pendingBookings = Booking::where('branch_id', $branch->id)->where('status', 'pending')->count();
        
        $recentDrivers = DriverRegistration::where('branch_id', $branch->id)->latest()->take(5)->get();
        $recentBookings = Booking::where('branch_id', $branch->id)->latest()->take(5)->get();

        return view('branch.dashboard', compact('branch', 'totalDrivers', 'totalBookings', 'pendingBookings', 'recentDrivers', 'recentBookings'));
    }
}
