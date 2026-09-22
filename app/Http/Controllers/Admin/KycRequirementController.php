<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\KycRequirement;

class KycRequirementController extends Controller
{
    public function index()
    {
        $requirements = KycRequirement::latest()->get();
        return view('backend.settings.kyc', compact('requirements'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'document_name' => 'required|string|max:255|unique:kyc_requirements',
            'document_type' => 'required|in:image,pdf,text',
        ]);

        KycRequirement::create([
            'document_name' => $request->document_name,
            'document_type' => $request->document_type,
            'is_required' => $request->has('is_required'),
            'is_active' => $request->has('is_active'),
        ]);

        return back()->with('success', 'KYC Requirement added successfully.');
    }

    public function update(Request $request, $id)
    {
        $requirement = KycRequirement::findOrFail($id);
        
        $request->validate([
            'document_name' => 'required|string|max:255|unique:kyc_requirements,document_name,' . $requirement->id,
            'document_type' => 'required|in:image,pdf,text',
        ]);

        $requirement->update([
            'document_name' => $request->document_name,
            'document_type' => $request->document_type,
            'is_required' => $request->has('is_required'),
            'is_active' => $request->has('is_active'),
        ]);

        return back()->with('success', 'KYC Requirement updated successfully.');
    }

    public function destroy($id)
    {
        $requirement = KycRequirement::findOrFail($id);
        $requirement->delete();
        return back()->with('success', 'KYC Requirement deleted.');
    }
}
