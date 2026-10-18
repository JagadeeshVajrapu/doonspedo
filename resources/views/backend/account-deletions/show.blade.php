@extends('layouts.admin')

@section('title', 'Deletion Request '.$deletionRequest->reference_no)
@section('page_title', 'Deletion Request Detail')

@section('content')
@if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif
@if(session('error'))
    <div class="alert alert-danger">{{ session('error') }}</div>
@endif

<div class="admin-card mb-4">
    <div class="card-body p-4">
        <div class="d-flex flex-wrap justify-content-between gap-3 mb-4">
            <div>
                <h5 class="fw-bold mb-1">{{ $deletionRequest->reference_no }}</h5>
                <div class="text-muted small">Submitted {{ $deletionRequest->created_at?->format('d M Y, h:i A') }}</div>
            </div>
            <div>
                <span class="badge bg-dark rounded-pill px-3 py-2">{{ str_replace('_', ' ', $deletionRequest->status) }}</span>
            </div>
        </div>

        <div class="row g-3 small">
            <div class="col-md-4"><strong>Account type</strong><div>{{ ucfirst($deletionRequest->account_type) }}</div></div>
            <div class="col-md-4"><strong>Account ID</strong><div>{{ $deletionRequest->account_id ?: 'Not matched' }}</div></div>
            <div class="col-md-4"><strong>Verified</strong><div>{{ $deletionRequest->is_verified ? 'Yes' : 'No' }} @if($deletionRequest->verified_at) ({{ $deletionRequest->verified_at->format('d M Y H:i') }}) @endif</div></div>
            <div class="col-md-4"><strong>Email</strong><div>{{ $deletionRequest->email ?: '—' }}</div></div>
            <div class="col-md-4"><strong>Mobile</strong><div>{{ $deletionRequest->mobile ?: '—' }}</div></div>
            <div class="col-md-4"><strong>Channel</strong><div>{{ $deletionRequest->verification_channel ?: '—' }}</div></div>
            <div class="col-md-4"><strong>Processed at</strong><div>{{ $deletionRequest->processed_at?->format('d M Y H:i') ?: '—' }}</div></div>
            <div class="col-md-4"><strong>Completed at</strong><div>{{ $deletionRequest->completed_at?->format('d M Y H:i') ?: '—' }}</div></div>
            <div class="col-md-4"><strong>Processed by (admin id)</strong><div>{{ $deletionRequest->processed_by ?: '—' }}</div></div>
        </div>

        @if($deletionRequest->admin_notes)
            <div class="mt-4"><strong class="small">Admin notes</strong><div class="small text-muted">{{ $deletionRequest->admin_notes }}</div></div>
        @endif
        @if($deletionRequest->rejection_reason)
            <div class="mt-3"><strong class="small">Rejection reason</strong><div class="small text-danger">{{ $deletionRequest->rejection_reason }}</div></div>
        @endif
    </div>
</div>

<div class="row g-4">
    @if($deletionRequest->status === 'pending_review')
    <div class="col-md-4">
        <div class="admin-card h-100">
            <div class="card-body p-4">
                <h6 class="fw-bold">Approve</h6>
                <p class="small text-muted">Marks the request approved. Does not delete or anonymize the account yet. OTP-outstanding requests cannot be approved here.</p>
                <form method="POST" action="{{ route('admin.account-deletions.approve', $deletionRequest->id) }}">
                    @csrf
                    <textarea name="admin_notes" class="form-control mb-3" rows="2" placeholder="Optional notes">{{ old('admin_notes', $deletionRequest->admin_notes) }}</textarea>
                    <button type="submit" class="btn btn-success rounded-pill w-100" onclick="return confirm('Approve this deletion request? Account data will not be changed until you Complete.');">Approve</button>
                </form>
            </div>
        </div>
    </div>
    @endif

    @if($deletionRequest->status !== 'completed' && $deletionRequest->status !== 'rejected')
    <div class="col-md-4">
        <div class="admin-card h-100">
            <div class="card-body p-4">
                <h6 class="fw-bold">Reject</h6>
                <form method="POST" action="{{ route('admin.account-deletions.reject', $deletionRequest->id) }}">
                    @csrf
                    <textarea name="rejection_reason" class="form-control mb-3" rows="3" placeholder="Reason for rejection" required>{{ old('rejection_reason') }}</textarea>
                    <button type="submit" class="btn btn-danger rounded-pill w-100" onclick="return confirm('Reject this request?');">Reject</button>
                </form>
            </div>
        </div>
    </div>
    @endif

    @if($deletionRequest->status === 'approved')
    <div class="col-md-4">
        <div class="admin-card h-100 border border-warning">
            <div class="card-body p-4">
                <h6 class="fw-bold text-warning">Complete (anonymize)</h6>
                <p class="small text-muted">Anonymizes personal profile data and revokes login. Ride/payment records are retained. This cannot be undone from the admin UI.</p>
                <form method="POST" action="{{ route('admin.account-deletions.complete', $deletionRequest->id) }}">
                    @csrf
                    <textarea name="admin_notes" class="form-control mb-3" rows="2" placeholder="Completion notes (optional)"></textarea>
                    <button type="submit" class="btn btn-warning rounded-pill w-100" onclick="return confirm('Complete deletion? Personal data will be anonymized. Financial/ride records will be retained.');">Mark Completed</button>
                </form>
            </div>
        </div>
    </div>
    @endif
</div>

<div class="mt-4">
    <a href="{{ route('admin.account-deletions.index') }}" class="btn btn-outline-dark rounded-pill">Back to list</a>
</div>
@endsection
