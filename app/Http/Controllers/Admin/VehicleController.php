<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\VehicleCategory;
use App\Models\Parcel;

use Illuminate\Support\Facades\File;

class VehicleController extends Controller
{
    private $settingsFile;

    public function __construct()
    {
        $this->settingsFile = storage_path('app/settings.json');
    }

    private function getSettings()
    {
        if (File::exists($this->settingsFile)) {
            return json_decode(File::get($this->settingsFile), true);
        }
        return [];
    }

    private function saveSettings($data)
    {
        File::put($this->settingsFile, json_encode($data, JSON_PRETTY_PRINT));
    }
    public function categories()
    {
        $categories = VehicleCategory::latest()->get();
        return view('backend.vehicles.categories', compact('categories'));
    }

    public function storeCategory(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'icon' => 'required|string',
            'capacity_seats' => 'required|integer|min:1',
            'capacity_bags' => 'required|integer|min:0',
            'base_fare' => 'required|numeric|min:0',
            'rate_per_km' => 'required|numeric|min:0',
        ]);

        VehicleCategory::create($request->all());

        return back()->with('success', 'Vehicle category created successfully.');
    }

    public function updateCategory(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'icon' => 'required|string',
            'capacity_seats' => 'required|integer|min:1',
            'capacity_bags' => 'required|integer|min:0',
            'base_fare' => 'required|numeric|min:0',
            'rate_per_km' => 'required|numeric|min:0',
        ]);

        $category = VehicleCategory::findOrFail($id);
        
        $data = $request->all();
        $data['is_active'] = $request->has('is_active');
            
        $category->update($data);

        return back()->with('success', 'Vehicle category updated successfully.');
    }

    public function deleteCategory($id)
    {
        $category = VehicleCategory::findOrFail($id);
        $category->delete();

        return back()->with('success', 'Vehicle category deleted successfully.');
    }

    public function rentals()
    {
        return view('backend.vehicles.rentals');
    }

    public function pricing()
    {
        $settings = $this->getSettings();
        return view('backend.vehicles.pricing', compact('settings'));
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
        $parcels = Parcel::all();
        return view('backend.vehicles.freight', compact('parcels'));
    }

    public function storeParcel(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'max_load' => 'nullable|string|max:255',
            'base_price' => 'required|numeric',
            'price_per_km' => 'required|numeric',
        ]);

        Parcel::create([
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
        $request->validate([
            'name' => 'required|string|max:255',
            'max_load' => 'nullable|string|max:255',
            'base_price' => 'required|numeric',
            'price_per_km' => 'required|numeric',
        ]);
        
        $parcel = Parcel::findOrFail($id);
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
        $parcel = Parcel::findOrFail($id);
        $parcel->delete();
        return back()->with('success', 'Parcel category deleted successfully.');
    }
}

