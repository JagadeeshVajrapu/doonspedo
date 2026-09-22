<?php

namespace App\Http\Controllers\Branch;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Vehicle;
use App\Models\VehicleCategory;
use App\Models\DriverRegistration;
use App\Models\Parcel;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class VehicleController extends Controller
{
    private function getSettingsFile()
    {
        $branch = Auth::guard('branch')->user();
        return storage_path("app/settings_branch_{$branch->id}.json");
    }

    private function getSettings()
    {
        $file = $this->getSettingsFile();
        if (File::exists($file)) {
            return json_decode(File::get($file), true);
        }
        return [];
    }

    private function saveSettings($data)
    {
        $file = $this->getSettingsFile();
        File::put($file, json_encode($data, JSON_PRETTY_PRINT));
    }

    public function index()
    {
        $branch = Auth::guard('branch')->user();
        
        $vehicles = Vehicle::with(['driver', 'category'])
            ->whereHas('driver', function($query) use ($branch) {
                $query->where('branch_id', $branch->id);
            })
            ->latest()
            ->paginate(15);
            
        return view('branch.vehicles.index', compact('vehicles'));
    }

    public function create()
    {
        $branch = Auth::guard('branch')->user();
        $drivers = DriverRegistration::where('branch_id', $branch->id)->get();
        // Allow using global categories or branch specific categories
        $categories = VehicleCategory::where('is_active', true)
                        ->where(function($q) use ($branch) {
                            $q->whereNull('branch_id')->orWhere('branch_id', $branch->id);
                        })
                        ->get();
        
        return view('branch.vehicles.create', compact('drivers', 'categories'));
    }

    public function store(Request $request)
    {
        $branch = Auth::guard('branch')->user();
        
        $request->validate([
            'driver_id'           => 'required|exists:driver_registrations,id',
            'vehicle_category_id' => 'required|exists:vehicle_categories,id',
            'brand'               => 'required|string|max:100',
            'model'               => 'required|string|max:100',
            'number_plate'        => 'required|string|max:50|unique:vehicles,number_plate',
            'color'               => 'required|string|max:50',
            'year'                => 'required|integer|min:1990|max:' . (date('Y') + 1),
            'min_price'           => 'required|numeric|min:0',
            'per_km_price'        => 'required|numeric|min:0',
        ]);

        // Ensure the driver belongs to this branch
        $driver = DriverRegistration::where('branch_id', $branch->id)->findOrFail($request->driver_id);

        // Deactivate all other vehicles for this driver
        Vehicle::where('driver_id', $driver->id)->update(['status' => 'inactive']);

        Vehicle::create([
            'driver_id'           => $driver->id,
            'vehicle_category_id' => $request->vehicle_category_id,
            'brand'               => $request->brand,
            'model'               => $request->model,
            'number_plate'        => strtoupper($request->number_plate),
            'color'               => $request->color,
            'year'                => $request->year,
            'min_price'           => $request->min_price,
            'per_km_price'        => $request->per_km_price,
            'status'              => 'active',
            'verification_status' => 'approved',
        ]);

        return redirect()->route('branch.vehicles.index')->with('success', 'Vehicle created and assigned successfully.');
    }

    // --- Master Data Methods ---

    public function categories()
    {
        $branch = Auth::guard('branch')->user();
        $categories = VehicleCategory::where('branch_id', $branch->id)->latest()->get();
        return view('branch.vehicles.categories', compact('categories'));
    }

    public function storeCategory(Request $request)
    {
        $branch = Auth::guard('branch')->user();
        
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'icon' => 'required|string',
            'capacity_seats' => 'required|integer|min:1',
            'capacity_bags' => 'required|integer|min:0',
            'base_fare' => 'required|numeric|min:0',
            'rate_per_km' => 'required|numeric|min:0',
        ]);

        $data = $request->all();
        $data['branch_id'] = $branch->id;
        VehicleCategory::create($data);

        return back()->with('success', 'Vehicle category created successfully.');
    }

    public function updateCategory(Request $request, $id)
    {
        $branch = Auth::guard('branch')->user();
        
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'icon' => 'required|string',
            'capacity_seats' => 'required|integer|min:1',
            'capacity_bags' => 'required|integer|min:0',
            'base_fare' => 'required|numeric|min:0',
            'rate_per_km' => 'required|numeric|min:0',
        ]);

        $category = VehicleCategory::where('branch_id', $branch->id)->findOrFail($id);
        
        $data = $request->all();
        $data['is_active'] = $request->has('is_active');
            
        $category->update($data);

        return back()->with('success', 'Vehicle category updated successfully.');
    }

    public function deleteCategory($id)
    {
        $branch = Auth::guard('branch')->user();
        $category = VehicleCategory::where('branch_id', $branch->id)->findOrFail($id);
        $category->delete();

        return back()->with('success', 'Vehicle category deleted successfully.');
    }

    public function rentals()
    {
        return view('branch.vehicles.rentals');
    }

    public function pricing()
    {
        $settings = $this->getSettings();
        return view('branch.vehicles.pricing', compact('settings'));
    }

    public function updatePricing(Request $request)
    {
        $settings = $this->getSettings();

        $keys = [
            'ac_rate_per_km', 'non_ac_rate_per_km',
            'rain_surge_multiplier', 'night_premium_amount'
        ];

        foreach ($keys as $key) {
            if ($request->has($key)) {
                $settings[$key] = $request->input($key);
            }
        }

        $settings['rain_surge_enabled'] = $request->has('rain_surge_enabled') ? '1' : '0';
        $settings['night_premium_enabled'] = $request->has('night_premium_enabled') ? '1' : '0';

        $this->saveSettings($settings);

        return back()->with('success', 'Pricing rules updated successfully.');
    }

    public function freight()
    {
        $branch = Auth::guard('branch')->user();
        $parcels = Parcel::where('branch_id', $branch->id)->get();
        return view('branch.vehicles.freight', compact('parcels'));
    }

    public function storeParcel(Request $request)
    {
        $branch = Auth::guard('branch')->user();
        
        $request->validate([
            'name' => 'required|string|max:255',
            'max_load' => 'nullable|string|max:255',
            'base_price' => 'required|numeric',
            'price_per_km' => 'required|numeric',
        ]);

        Parcel::create([
            'branch_id' => $branch->id,
            'name' => $request->name,
            'description' => $request->description,
            'icon' => $request->icon ?? 'bi-box-seam',
            'max_load' => $request->max_load,
            'base_price' => $request->base_price,
            'price_per_km' => $request->price_per_km,
            'is_active' => $request->has('is_active') ? 1 : 0,
        ]);

        return back()->with('success', 'Parcel category added successfully.');
    }

    public function updateParcel(Request $request, $id)
    {
        $branch = Auth::guard('branch')->user();
        
        $request->validate([
            'name' => 'required|string|max:255',
            'max_load' => 'nullable|string|max:255',
            'base_price' => 'required|numeric',
            'price_per_km' => 'required|numeric',
        ]);
        
        $parcel = Parcel::where('branch_id', $branch->id)->findOrFail($id);
        $parcel->update([
            'name' => $request->name,
            'description' => $request->description,
            'icon' => $request->icon ?? $parcel->icon,
            'max_load' => $request->max_load,
            'base_price' => $request->base_price,
            'price_per_km' => $request->price_per_km,
            'is_active' => $request->has('is_active') ? 1 : 0,
        ]);

        return back()->with('success', 'Parcel category updated successfully.');
    }

    public function deleteParcel($id)
    {
        $branch = Auth::guard('branch')->user();
        $parcel = Parcel::where('branch_id', $branch->id)->findOrFail($id);
        $parcel->delete();
        return back()->with('success', 'Parcel category deleted successfully.');
    }
}
