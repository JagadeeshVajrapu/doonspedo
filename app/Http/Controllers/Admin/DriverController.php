<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\DriverRegistration;
use App\Models\DriverDocument;

class DriverController extends Controller
{
    public function index()
    {
        $drivers = DriverRegistration::with('documents.kycRequirement')->latest()->get();
        return view('backend.drivers.index', compact('drivers'));
    }

    public function create()
    {
        return view('backend.drivers.create');
    }

    public function kycApproved()
    {
        $drivers = DriverRegistration::with('documents.kycRequirement')->where('status', 'approved')->latest()->get();
        return view('backend.drivers.kyc-approved', compact('drivers'));
    }

    public function kycPending()
    {
        $drivers = DriverRegistration::with('documents.kycRequirement')->where('status', 'pending')->latest()->get();
        return view('backend.drivers.kyc-pending', compact('drivers'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'           => 'required|string|max:255',
            'mobile'         => 'required|string|max:15',
            'email'          => 'required|email|max:255',
            'vehicle_type'   => 'required|string',
            'vehicle_number' => 'required|string|max:50',
            'license_number' => 'required|string|max:50',
            'city'           => 'required|string|max:100',
        ]);

        $data = $request->all();
        $data['min_price'] = $request->min_price ?? 0;
        $data['per_km_price'] = $request->per_km_price ?? 0;

        DriverRegistration::create($data);

        return redirect()->route('admin.drivers.index')->with('success', 'Driver created successfully.');
    }

    public function approve($id)
    {
        $driver = DriverRegistration::findOrFail($id);
        $driver->update(['status' => 'approved']);

        return back()->with('success', 'Driver KYC approved successfully.');
    }

    public function reject($id)
    {
        $driver = DriverRegistration::findOrFail($id);
        $driver->update(['status' => 'rejected']);

        return back()->with('success', 'Driver KYC rejected.');
    }

    public function viewDetails($id)
    {
        $driver = DriverRegistration::with(['documents.kycRequirement', 'vehicles.category', 'vehicles.documents'])->findOrFail($id);
        return view('backend.drivers.view', compact('driver'));
    }

    public function block($id)
    {
        $driver = DriverRegistration::findOrFail($id);
        $toggle = !$driver->is_blocked;
        $driver->update(['is_blocked' => $toggle]);
        
        $msg = $toggle ? 'blocked' : 'unblocked';
        return back()->with('success', "Driver successfully {$msg}.");
    }

    public function updateCommission(Request $request, $id)
    {
        $request->validate([
            'commission_rate' => 'required|numeric|min:0|max:100'
        ]);

        $driver = DriverRegistration::findOrFail($id);
        $driver->update(['commission_rate' => $request->commission_rate]);

        return back()->with('success', 'Driver commission rate updated successfully.');
    }

    public function edit($id)
    {
        $driver = DriverRegistration::findOrFail($id);
        return view('backend.drivers.edit', compact('driver'));
    }

    public function update(Request $request, $id)
    {
        $driver = DriverRegistration::findOrFail($id);
        
        $request->validate([
            'name'           => 'required|string|max:255',
            'mobile'         => 'required|string|max:15',
            'email'          => 'required|email|max:255',
            'vehicle_type'   => 'required|string',
            'vehicle_number' => 'required|string|max:50',
            'license_number' => 'required|string|max:50',
            'city'           => 'required|string|max:100',
        ]);

        $data = $request->all();
        if(!isset($data['min_price'])) $data['min_price'] = $driver->min_price ?? 0;
        if(!isset($data['per_km_price'])) $data['per_km_price'] = $driver->per_km_price ?? 0;

        $driver->update($data);

        return redirect()->route('admin.drivers.index')->with('success', 'Driver profile updated successfully.');
    }

    public function destroy($id)
    {
        $driver = DriverRegistration::findOrFail($id);
        $driver->delete();

        return back()->with('success', 'Driver and all associated records deleted permanently.');
    }

    public function updatePhoto(Request $request, $id)
    {
        $driver = DriverRegistration::findOrFail($id);

        $request->validate([
            'profile_image' => 'nullable|image|max:2048',
            'profile_image_base64' => 'nullable|string',
        ]);

        if ($request->filled('profile_image_base64')) {
            $base64Image = $request->input('profile_image_base64');
            $image_parts = explode(";base64,", $base64Image);
            if (count($image_parts) > 1) {
                $image_type_aux = explode("image/", $image_parts[0]);
                $image_type = $image_type_aux[1];
                $image_base64 = base64_decode($image_parts[1]);

                try {
                    $stored = app(\App\Services\CloudinaryStorage::class)->storeBinary($image_base64, uniqid().'.'.$image_type, 'drivers/profile');
                } catch (\RuntimeException $e) {
                    return back()->with('error', $e->getMessage());
                }
                $driver->update(['profile_image' => $stored]);
            }
        } elseif ($request->hasFile('profile_image')) {
            try {
                $path = app(\App\Services\CloudinaryStorage::class)->storeUploadedFile($request->file('profile_image'), 'drivers/profile');
            } catch (\RuntimeException $e) {
                return back()->with('error', $e->getMessage());
            }
            $driver->update(['profile_image' => $path]);
        }

        return back()->with('success', 'Driver profile photo updated successfully.');
    }

    public function updateDocumentStatus(Request $request, $id)
    {
        $request->validate(['status' => 'required|in:approved,rejected']);
        $doc = DriverDocument::findOrFail($id);
        $doc->update(['status' => $request->status]);

        $label = $request->status === 'approved' ? 'Verified' : 'Rejected';

        return back()->with('success', 'Document status updated to ' . $label);
    }
}
