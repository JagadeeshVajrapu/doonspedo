<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CustomerKycSubmission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CustomerKycController extends Controller
{
    public function index()
    {
        $submissions = CustomerKycSubmission::with('user')->latest()->paginate(20);
        return view('backend.customers.kyc', compact('submissions'));
    }

    public function download(CustomerKycSubmission $submission)
    {
        if (preg_match('#^https?://#i', (string) $submission->document_path)) {
            return redirect()->away($submission->document_path);
        }

        if (!str_starts_with($submission->document_path, 'customer-kyc/')) {
            abort(404);
        }

        if (!Storage::disk('local')->exists($submission->document_path)) {
            abort(404);
        }

        return Storage::disk('local')->download($submission->document_path);
    }

    public function approve(CustomerKycSubmission $submission)
    {
        $submission->update([
            'status' => 'approved',
            'rejection_reason' => null,
            'reviewed_at' => now(),
        ]);

        return back()->with('success', 'Customer KYC approved.');
    }

    public function reject(Request $request, CustomerKycSubmission $submission)
    {
        $request->validate([
            'rejection_reason' => 'required|string|max:500',
        ]);

        $submission->update([
            'status' => 'rejected',
            'rejection_reason' => $request->rejection_reason,
            'reviewed_at' => now(),
        ]);

        return back()->with('success', 'Customer KYC rejected.');
    }
}
