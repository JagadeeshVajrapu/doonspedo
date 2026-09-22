<?php

namespace App\Http\Controllers\Branch;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\DriverRegistration;
use Illuminate\Support\Facades\Auth;

class DriverController extends Controller
{
    public function index()
    {
        $branch = Auth::guard('branch')->user();
        $drivers = DriverRegistration::where('branch_id', $branch->id)->latest()->paginate(15);
        return view('branch.drivers.index', compact('drivers'));
    }

    public function create()
    {
        return view('branch.drivers.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'mobile' => 'required|string|max:15|unique:driver_registrations,mobile',
            'vehicle_number' => 'required|string|max:20|unique:driver_registrations,vehicle_number',
        ]);

        $branch = Auth::guard('branch')->user();
        
        $driver = new DriverRegistration();
        $driver->name = $request->name;
        $driver->mobile = $request->mobile;
        
        // Generate a placeholder email since it's required by the DB
        $cleanMobile = preg_replace('/[^0-9]/', '', $request->mobile);
        $driver->email = 'driver_' . $cleanMobile . '_' . uniqid() . '@cabbooking.local';
        
        $driver->vehicle_number = $request->vehicle_number;
        $driver->vehicle_type = $request->vehicle_type ?? 'N/A'; // Default vehicle type
        $driver->license_number = 'N/A'; // Default license number
        $driver->city = 'N/A'; // Default city
        $driver->branch_id = $branch->id;
        $driver->status = 'pending'; // Default status
        
        // Removed dummy password since the column does not exist 
        
        $driver->save();

        return redirect()->route('branch.drivers.index')->with('success', 'Driver added successfully to your branch.');
    }

    public function view($id)
    {
        $branch = Auth::guard('branch')->user();
        $driver = DriverRegistration::where('branch_id', $branch->id)->findOrFail($id);
        return view('branch.drivers.view', compact('driver'));
    }
}

