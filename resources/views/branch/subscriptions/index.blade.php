@extends('layouts.admin')

@section('title', 'All Subscriptions')
@section('page_title', 'Branch: Driver Subscriptions History')

@section('content')
<div class="admin-card">
    <div class="card-header bg-white border-0 p-4">
        <div class="d-flex justify-content-between align-items-center">
            <h5 class="fw-bold mb-0">Subscription History</h5>
            <div>
                <button type="button" class="btn btn-sm btn-outline-primary rounded-pill px-3 me-2" data-bs-toggle="modal" data-bs-target="#createPlanModal">
                    <i class="bi bi-plus-circle me-1"></i> Create Plan
                </button>
                <a href="{{ route('branch.subscriptions.requests') }}" class="btn btn-sm btn-brand text-dark rounded-pill px-3 fw-bold">Pending Requests</a>
            </div>
        </div>
    </div>
    <div class="card-body p-0">
        <div class="admin-table-wrap">
            <table class="table table-hover align-middle mb-0 admin-responsive-table">
                <thead class="bg-light">
                    <tr>
                        <th class="ps-4">Driver</th>
                        <th>Plan</th>
                        <th>Price</th>
                        <th>Status</th>
                        <th>Duration</th>
                        <th class="pe-4">Purchased At</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($subscriptions as $sub)
                    <tr>
                        <td class="ps-4">
                            <div class="d-flex align-items-center">
                                <div class="avatar-sm rounded-circle bg-brand bg-opacity-10 text-brand fw-bold d-flex align-items-center justify-content-center me-3" style="width: 40px; height: 40px;">
                                    {{ substr($sub->driver->name, 0, 1) }}
                                </div>
                                <div>
                                    <div class="fw-bold text-dark">{{ $sub->driver->name }}</div>
                                    <div class="small text-muted">{{ $sub->driver->mobile }}</div>
                                </div>
                            </div>
                        </td>
                        <td>
                            <div class="fw-bold">{{ $sub->plan->name }}</div>
                        </td>
                        <td>?{{ number_format($sub->price, 2) }}</td>
                        <td>
                            @if($sub->status == 'active')
                                <span class="badge bg-success bg-opacity-10 text-success rounded-pill px-3 py-1">Active</span>
                            @elseif($sub->status == 'pending')
                                <span class="badge bg-warning bg-opacity-10 text-warning rounded-pill px-3 py-1">Pending</span>
                            @elseif($sub->status == 'expired')
                                <span class="badge bg-danger bg-opacity-10 text-danger rounded-pill px-3 py-1">Expired</span>
                            @else
                                <span class="badge bg-secondary bg-opacity-10 text-secondary rounded-pill px-3 py-1">{{ ucfirst($sub->status) }}</span>
                            @endif
                        </td>
                        <td>
                            @if($sub->starts_at && $sub->expires_at)
                                <div class="small">{{ $sub->starts_at->format('d/m/Y') }} - {{ $sub->expires_at->format('d/m/Y') }}</div>
                            @else
                                <span class="text-muted small">Not set</span>
                            @endif
                        </td>
                        <td class="pe-4 small text-muted">
                            {{ $sub->created_at->format('d M, Y') }}
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center py-5 text-muted">
                            <i class="bi bi-inbox fs-2 d-block mb-2"></i>
                            No subscriptions found.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($subscriptions->hasPages())
    <div class="card-footer bg-white border-0 p-4">
        {{ $subscriptions->links() }}
    </div>
    @endif
</div>

@endsection

