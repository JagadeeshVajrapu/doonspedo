<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\DriverRegistration;
use App\Models\Vehicle;
use App\Models\VehicleCategory;
use App\Models\VehicleSpecificDocument;
use Illuminate\Support\Facades\DB;

class DriverVehicleController extends Controller
{
    private function getDriver()
    {
        return DriverRegistration::find(session('driver_id'));
    }

    public function index()
    {
        if (!session('driver_id')) return redirect()->route('driver.login');
        
        $driver = $this->getDriver();
        $vehicles = Vehicle::with('category', 'documents')
            ->where('driver_id', $driver->id)
            ->latest()
            ->get();
            
        return view('frontend.driver.vehicles.index', compact('driver', 'vehicles'));
    }

    public function create()
    {
        if (!session('driver_id')) return redirect()->route('driver.login');
        
        $driver = $this->getDriver();
        $categories = VehicleCategory::where('is_active', true)->get();
        
        return view('frontend.driver.vehicles.create', compact('driver', 'categories'));
    }

    public function store(Request $request)
    {
        if (!session('driver_id')) return redirect()->route('driver.login');
        
        $driver = $this->getDriver();
        
        $request->validate([
            'vehicle_category_id' => 'required|exists:vehicle_categories,id',
            'brand'               => 'required|string|max:100',
            'model'               => 'required|string|max:100',
            'number_plate'        => 'required|string|max:50|unique:vehicles,number_plate',
            'color'               => 'required|string|max:50',
            'year'                => 'required|integer|min:1990|max:' . (date('Y') + 1),
            'rc_book'             => 'required|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'insurance'           => 'required|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'vehicle_image'       => 'required|file|mimes:jpg,jpeg,png|max:5120',
        ]);

        DB::beginTransaction();
        try {
            $vehicle = Vehicle::create([
                'driver_id'           => $driver->id,
                'vehicle_category_id' => $request->vehicle_category_id,
                'brand'               => $request->brand,
                'model'               => $request->model,
                'number_plate'        => strtoupper($request->number_plate),
                'color'               => $request->color,
                'year'                => $request->year,
                'status'              => 'inactive', // New vehicles are inactive by default
                'verification_status' => 'pending',
            ]);

            // Handle RC Book Upload
            if ($request->hasFile('rc_book')) {
                $rcPath = $request->file('rc_book')->store('vehicles/documents', 'public');
                VehicleSpecificDocument::create([
                    'vehicle_id'    => $vehicle->id,
                    'document_name' => 'RC Book',
                    'document_path' => $rcPath,
                    'status'        => 'pending',
                ]);
            }

            // Handle Insurance Upload
            if ($request->hasFile('insurance')) {
                $insPath = $request->file('insurance')->store('vehicles/documents', 'public');
                VehicleSpecificDocument::create([
                    'vehicle_id'    => $vehicle->id,
                    'document_name' => 'Insurance',
                    'document_path' => $insPath,
                    'status'        => 'pending',
                ]);
            }

            // Handle Vehicle Photo Upload
            if ($request->hasFile('vehicle_image')) {
                $vehPath = $request->file('vehicle_image')->store('vehicles/documents', 'public');
                VehicleSpecificDocument::create([
                    'vehicle_id'    => $vehicle->id,
                    'document_name' => 'Vehicle Photo',
                    'document_path' => $vehPath,
                    'status'        => 'pending',
                ]);
            }

            DB::commit();
            return redirect()->route('driver.vehicles.index')->with('success', 'Vehicle added successfully and is under review.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Something went wrong: ' . $e->getMessage());
        }
    }

    public function setActive($id)
    {
        if (!session('driver_id')) return redirect()->route('driver.login');
        
        $driver = $this->getDriver();
        $vehicle = Vehicle::where('driver_id', $driver->id)->findOrFail($id);

        if ($vehicle->verification_status !== 'approved') {
            return back()->with('error', 'Vehicle must be approved by admin before setting as active.');
        }

        // Deactivate all others
        Vehicle::where('driver_id', $driver->id)->update(['status' => 'inactive']);
        
        // Activate this one
        $vehicle->update(['status' => 'active']);

        return back()->with('success', 'Active vehicle updated successfully.');
    }

    public function destroy($id)
    {
        if (!session('driver_id')) return redirect()->route('driver.login');
        
        $driver = $this->getDriver();
        $vehicle = Vehicle::where('driver_id', $driver->id)->findOrFail($id);
        
        if ($vehicle->status === 'active') {
            return back()->with('error', 'Cannot delete an active vehicle.');
        }

        $vehicle->delete();
        return back()->with('success', 'Vehicle deleted successfully.');
    }
}
