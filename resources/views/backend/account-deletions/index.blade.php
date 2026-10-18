@extends('layouts.admin')

@section('title', 'Account Deletion Requests')
@section('page_title', 'Account Deletion Requests')

@section('content')
<div class="admin-card">
    <div class="card-header bg-white border-0 p-4 d-flex flex-wrap justify-content-between align-items-center gap-2">
        <h5 class="fw-bold mb-0">Deletion Requests</h5>
        <div class="d-flex flex-wrap gap-2">
            @foreach(['' => 'All', 'pending_verification' => 'Pending verification', 'pending_review' => 'Pending review', 'approved' => 'Approved', 'completed' => 'Completed', 'rejected' => 'Rejected'] as $value => $label)
                <a href="{{ route('admin.account-deletions.index', $value !== '' ? ['status' => $value] : []) }}"
                   class="btn btn-sm rounded-pill {{ ($status ?? '') === $value || ($value === '' && empty($status)) ? 'btn-dark' : 'btn-outline-dark' }}">
                    {{ $label }}
                </a>
            @endforeach
        </div>
    </div>
    <div class="card-body p-0">
        <div class="admin-table-wrap">
            <table class="table table-hover align-middle mb-0 admin-responsive-table">
                <thead class="bg-light">
                    <tr>
                        <th class="ps-4">Reference</th>
                        <th>Type</th>
                        <th>Contact</th>
                        <th>Status</th>
                        <th>Verified</th>
                        <th>Submitted</th>
                        <th class="text-end pe-4">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($requests as $req)
                    <tr>
                        <td class="ps-4 fw-bold">{{ $req->reference_no }}</td>
                        <td><span class="badge bg-dark rounded-pill">{{ ucfirst($req->account_type) }}</span></td>
                        <td class="small">
                            <div>{{ $req->email ?: '—' }}</div>
                            <div class="text-muted">{{ $req->mobile ?: '—' }}</div>
                        </td>
                        <td><span class="badge bg-secondary rounded-pill">{{ str_replace('_', ' ', $req->status) }}</span></td>
                        <td>{{ $req->is_verified ? 'Yes' : 'No' }}</td>
                        <td class="small text-muted">{{ $req->created_at?->format('d M, Y h:i A') }}</td>
                        <td class="text-end pe-4">
                            <a href="{{ route('admin.account-deletions.show', $req->id) }}" class="btn btn-sm btn-outline-primary rounded-pill">Review</a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center py-5 text-muted">No deletion requests found.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if(method_exists($requests, 'links'))
            <div class="p-4">{{ $requests->links() }}</div>
        @endif
    </div>
</div>
@endsection
