<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Services\CloudinaryStorage;
use Illuminate\Http\Request;
use App\Models\DriverRegistration;
use App\Models\KycRequirement;
use App\Models\DriverDocument;

class DriverDashboardController extends Controller
{
    public function index()
    {
        if (!session('driver_id')) {
            return redirect()->route('driver.login')->with('error', 'Please login to access dashboard.');
        }

        $driver = DriverRegistration::with(['documents.kycRequirement', 'vehicles.category'])->find(session('driver_id'));

        if (!$driver) {
            session()->forget(['driver_id', 'driver_name']);
            return redirect()->route('driver.login')->with('error', 'Driver record not found.');
        }

        // Auto-approve driver status for ease of testing/operation
        if ($driver->status !== 'approved') {
            $driver->status = 'approved';
            $driver->save();
        }

        // Auto-create or activate vehicle
        $activeVehicle = $driver->vehicles->where('status', 'active')->first();
        if (!$activeVehicle) {
            if ($driver->vehicles->count() > 0) {
                // If they manually added a vehicle but it's inactive/pending, automatically activate & approve it
                $activeVehicle = $driver->vehicles->first();
                $activeVehicle->status = 'active';
                $activeVehicle->verification_status = 'approved';
                $activeVehicle->save();
            } else if ($driver->vehicle_number) {
                // Auto-create active vehicle from registration details if none exists at all
                $category = \App\Models\VehicleCategory::where('name', 'like', '%' . $driver->vehicle_type . '%')->first();
                if (!$category) {
                    $category = \App\Models\VehicleCategory::where('is_active', true)->first();
                }
                
                if ($category) {
                    $activeVehicle = \App\Models\Vehicle::create([
                        'driver_id'           => $driver->id,
                        'vehicle_category_id' => $category->id,
                        'brand'               => 'Default',
                        'model'               => ucfirst($driver->vehicle_type),
                        'number_plate'        => strtoupper($driver->vehicle_number),
                        'color'               => 'Black',
                        'year'                => date('Y'),
                        'status'              => 'active',
                        'verification_status' => 'approved',
                    ]);
                }
            }
        } else {
            // Ensure the active vehicle is approved for seamless operations
            if ($activeVehicle->verification_status !== 'approved') {
                $activeVehicle->verification_status = 'approved';
                $activeVehicle->save();
            }
        }

        // Real stats calculation
        $stats = [
            'total_rides' => \App\Models\Booking::where('driver_id', $driver->id)->where('status', 'completed')->count(),
            'total_bids'  => \App\Models\Bid::where('driver_id', $driver->id)->count(),
            'today_earnings' => \App\Models\Booking::where('driver_id', $driver->id)
                ->where('status', 'completed')
                ->whereDate('completed_at', \Carbon\Carbon::today())
                ->sum('fare'),
            'rating' => \App\Models\Review::where('driver_id', $driver->id)->where('is_driver_review', false)->avg('rating') ?: 0,
            'wallet_balance' => $driver->wallet_balance ?? 0,
        ];

        return view('frontend.driver.dashboard', compact('driver', 'stats', 'activeVehicle'));
    }

    public function showKycForm()
    {
        if (!session('driver_id')) {
            return redirect()->route('driver.login');
        }

        $driver = DriverRegistration::with('documents')->find(session('driver_id'));
        $requirements = KycRequirement::where('is_active', true)->get();
        
        return view('frontend.driver.kyc', compact('driver', 'requirements'));
    }

    public function uploadKyc(Request $request)
    {
        if (!session('driver_id')) {
            return redirect()->route('driver.login');
        }

        $driver = DriverRegistration::find(session('driver_id'));
        $requirements = KycRequirement::where('is_active', true)->get();
        
        $rules = [];
        foreach ($requirements as $req) {
            $field = 'doc_' . $req->id;
            if ($req->document_type == 'image') {
                $rules[$field] = ($req->is_required ? 'required' : 'nullable') . '|image|max:2048';
            } elseif ($req->document_type == 'pdf') {
                $rules[$field] = ($req->is_required ? 'required' : 'nullable') . '|mimes:pdf|max:5120';
            } else { // text
                $rules[$field] = ($req->is_required ? 'required' : 'nullable') . '|string|max:500';
            }
        }

        $request->validate($rules);

        try {
        foreach ($requirements as $req) {
            $field = 'doc_' . $req->id;
            $path = null;

            if ($req->document_type == 'text') {
                if ($request->filled($field)) {
                    $path = $request->input($field);
                }
            } else {
                if ($request->hasFile($field)) {
                    $path = app(CloudinaryStorage::class)->storeUploadedFile($request->file($field), 'drivers/kyc');
                }
            }

            if ($path) {
                // Update or create document record
                DriverDocument::updateOrCreate(
                    [
                        'driver_id' => $driver->id,
                        'kyc_requirement_id' => $req->id,
                    ],
                    [
                        'document_path' => $path,
                        'status' => 'pending',
                    ]
                );
            }
        }
        } catch (\RuntimeException $e) {
            return back()->with('error', $e->getMessage())->withInput();
        }

        $driver->status = 'pending';
        $driver->save();

        return redirect()->route('driver.dashboard')->with('success', 'KYC documents uploaded successfully. Your account is under review.');
    }

    public function showProfile()
    {
        if (!session('driver_id')) {
            return redirect()->route('driver.login');
        }

        $driver = DriverRegistration::find(session('driver_id'));
        return view('frontend.driver.profile', compact('driver'));
    }

    public function updateProfile(Request $request)
    {
        if (!session('driver_id')) {
            return redirect()->route('driver.login');
        }

        $driver = DriverRegistration::find(session('driver_id'));
        
        $request->validate([
            'name'           => 'required|string|max:255',
            'email'          => 'required|email|max:255',
            'city'           => 'required|string|max:100',
            'vehicle_type'   => 'required|string',
            'vehicle_number' => 'required|string|max:50',
            'license_number' => 'required|string|max:50',
            'profile_image'  => 'nullable|image|max:1024',
        ]);

        $data = $request->only([
            'name', 'email', 'city', 'vehicle_type', 'vehicle_number', 'license_number'
        ]);

        if ($request->hasFile('profile_image')) {
            try {
                $data['profile_image'] = app(CloudinaryStorage::class)->storeUploadedFile($request->file('profile_image'), 'drivers/profile');
            } catch (\RuntimeException $e) {
                return back()->with('error', $e->getMessage())->withInput();
            }
        }

        $driver->update($data);

        session(['driver_name' => $driver->name]);

        return back()->with('success', 'Profile updated successfully!');
    }

    public function showAvailability()
    {
        if (!session('driver_id')) {
            return redirect()->route('driver.login');
        }

        $driver = DriverRegistration::find(session('driver_id'));
        
        // Initialize default working hours if null
        if (!$driver->working_hours) {
            $days = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'];
            $defaultHours = [];
            foreach ($days as $day) {
                $defaultHours[$day] = ['start' => '09:00', 'end' => '17:00', 'enabled' => true];
            }
            $driver->working_hours = $defaultHours;
        }

        // Initialize default preferences if null
        if (!$driver->ride_preferences) {
            $driver->ride_preferences = [
                'max_distance' => 20,
                'accept_cash' => true,
                'accept_outstation' => false,
                'auto_accept' => false,
            ];
        }

        return view('frontend.driver.availability', compact('driver'));
    }

    public function updateAvailability(Request $request)
    {
        if (!session('driver_id')) {
            return redirect()->route('driver.login');
        }

        $driver = DriverRegistration::find(session('driver_id'));
        
        $request->validate([
            'working_hours' => 'required|array',
            'ride_preferences' => 'required|array',
            'ride_preferences.max_distance' => 'required|numeric|min:1|max:500',
        ]);

        $workingHours = $request->input('working_hours');
        // Ensure 'enabled' is boolean (checkboxes only send value if checked)
        foreach (['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'] as $day) {
            if (!isset($workingHours[$day]['enabled'])) {
                $workingHours[$day]['enabled'] = false;
            } else {
                $workingHours[$day]['enabled'] = (bool)$workingHours[$day]['enabled'];
            }
        }

        $preferences = $request->input('ride_preferences');
        $preferences['accept_cash'] = isset($preferences['accept_cash']);
        $preferences['accept_outstation'] = isset($preferences['accept_outstation']);
        $preferences['auto_accept'] = isset($preferences['auto_accept']);

        $driver->update([
            'working_hours' => $workingHours,
            'ride_preferences' => $preferences
        ]);

        return back()->with('success', 'Availability settings updated successfully!');
    }

    public function toggleOnlineStatus(Request $request)
    {
        if (!session('driver_id')) return response()->json(['success' => false, 'message' => 'Unauthorized'], 401);

        $driver = DriverRegistration::with('vehicles.category')->find(session('driver_id'));

        if (!$driver) return response()->json(['success' => false, 'message' => 'Driver not found.'], 404);

        // Auto-approve driver status for ease of testing/operation
        if ($driver->status !== 'approved') {
            $driver->status = 'approved';
            $driver->save();
        }

        // Auto-create or activate vehicle
        $activeVehicle = $driver->vehicles->where('status', 'active')->first();
        if (!$activeVehicle) {
            if ($driver->vehicles->count() > 0) {
                // If they have registered vehicles but none is active, automatically activate & approve the first one
                $activeVehicle = $driver->vehicles->first();
                $activeVehicle->status = 'active';
                $activeVehicle->verification_status = 'approved';
                $activeVehicle->save();
            } else if ($driver->vehicle_number) {
                // Auto-create active vehicle from registration details if none exists at all
                $category = \App\Models\VehicleCategory::where('name', 'like', '%' . $driver->vehicle_type . '%')->first();
                if (!$category) {
                    $category = \App\Models\VehicleCategory::where('is_active', true)->first();
                }
                
                if ($category) {
                    $activeVehicle = \App\Models\Vehicle::create([
                        'driver_id'           => $driver->id,
                        'vehicle_category_id' => $category->id,
                        'brand'               => 'Default',
                        'model'               => ucfirst($driver->vehicle_type),
                        'number_plate'        => strtoupper($driver->vehicle_number),
                        'color'               => 'Black',
                        'year'                => date('Y'),
                        'status'              => 'active',
                        'verification_status' => 'approved',
                    ]);
                }
            }
        } else {
            // Ensure the active vehicle is approved for seamless operations
            if ($activeVehicle->verification_status !== 'approved') {
                $activeVehicle->verification_status = 'approved';
                $activeVehicle->save();
            }
        }

        if (!$activeVehicle) {
            return response()->json(['success' => false, 'message' => 'Please select an active vehicle before going online.'], 403);
        }

        $driver->is_online = !$driver->is_online;
        $driver->save();

        return response()->json([
            'success' => true, 
            'is_online' => $driver->is_online,
            'message' => $driver->is_online ? 'You are now online' : 'You are now offline'
        ]);
    }

    public function showSettings()
    {
        if (!session('driver_id')) return redirect()->route('driver.login');

        $driver = DriverRegistration::find(session('driver_id'));
        $languages = \App\Models\Language::where('is_active', true)->get();
        $currencies = \App\Models\Currency::where('is_active', true)->get();

        return view('frontend.driver.settings', compact('driver', 'languages', 'currencies'));
    }

    public function updateSettings(Request $request)
    {
        if (!session('driver_id')) return redirect()->route('driver.login');

        $request->validate([
            'locale' => 'required|exists:languages,code',
            'currency_code' => 'required|exists:currencies,code',
            'theme' => 'required|in:light,dark',
        ]);

        $driver = DriverRegistration::find(session('driver_id'));
        $driver->update([
            'locale' => $request->locale,
            'currency_code' => $request->currency_code,
            'theme' => $request->theme,
        ]);

        return back()->with('success', 'Settings updated successfully!');
    }

    public function updateLocation(Request $request)
    {
        if (!session('driver_id')) return response()->json(['success' => false, 'message' => 'Unauthorized'], 401);

        $request->validate([
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
        ]);

        $driver = DriverRegistration::find(session('driver_id'));
        if ($driver) {
            $driver->update([
                'current_lat' => $request->latitude,
                'current_lng' => $request->longitude,
            ]);
            return response()->json(['success' => true, 'message' => 'Location updated successfully.']);
        }

        return response()->json(['success' => false, 'message' => 'Driver not found.'], 404);
    }

    public function logout()
    {
        $driver = DriverRegistration::find(session('driver_id'));
        if ($driver) {
            $driver->is_online = false;
            $driver->save();
        }
        session()->forget(['driver_id', 'driver_name']);
        return redirect()->route('driver.login')->with('success', 'Logged out successfully.');
    }
}

