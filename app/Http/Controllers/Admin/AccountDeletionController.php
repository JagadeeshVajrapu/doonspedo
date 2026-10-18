<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AccountDeletionRequest;
use App\Services\AccountDeletionService;
use Illuminate\Http\Request;
use RuntimeException;

class AccountDeletionController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->query('status');
        $query = AccountDeletionRequest::query()->latest();
        if ($status) {
            $query->where('status', $status);
        }

        $requests = $query->paginate(30)->withQueryString();

        return view('backend.account-deletions.index', compact('requests', 'status'));
    }

    public function show(int $id)
    {
        $deletionRequest = AccountDeletionRequest::query()->findOrFail($id);

        return view('backend.account-deletions.show', compact('deletionRequest'));
    }

    public function approve(Request $request, int $id)
    {
        $deletionRequest = AccountDeletionRequest::query()->findOrFail($id);

        // Only review-queue requests may be approved. Do not approve while OTP is still outstanding.
        if ($deletionRequest->status !== AccountDeletionRequest::STATUS_PENDING_REVIEW) {
            return back()->with('error', 'This request cannot be approved in its current status.');
        }

        $deletionRequest->forceFill([
            'status' => AccountDeletionRequest::STATUS_APPROVED,
            'processed_by' => auth('admin')->id(),
            'processed_at' => now(),
            'admin_notes' => $request->input('admin_notes', $deletionRequest->admin_notes),
            'rejection_reason' => null,
            'verification_code_hash' => null,
            'otp_expires_at' => null,
        ])->save();

        return back()->with('success', 'Request approved. Complete it when ready to anonymize the account.');
    }

    public function reject(Request $request, int $id)
    {
        $data = $request->validate([
            'rejection_reason' => ['required', 'string', 'max:2000'],
        ]);

        $deletionRequest = AccountDeletionRequest::query()->findOrFail($id);
        if ($deletionRequest->status === AccountDeletionRequest::STATUS_COMPLETED) {
            return back()->with('error', 'Completed requests cannot be rejected.');
        }

        $deletionRequest->forceFill([
            'status' => AccountDeletionRequest::STATUS_REJECTED,
            'rejection_reason' => $data['rejection_reason'],
            'processed_by' => auth('admin')->id(),
            'processed_at' => now(),
            'verification_code_hash' => null,
        ])->save();

        return back()->with('success', 'Request rejected.');
    }

    public function complete(Request $request, int $id, AccountDeletionService $service)
    {
        $deletionRequest = AccountDeletionRequest::query()->findOrFail($id);

        try {
            $service->complete(
                $deletionRequest,
                auth('admin')->id(),
                $request->input('admin_notes')
            );
        } catch (RuntimeException $e) {
            return back()->with('error', $e->getMessage());
        }

        return back()->with('success', 'Account anonymized and request marked completed. Financial and ride records were retained.');
    }
}
