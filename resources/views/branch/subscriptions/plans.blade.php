@extends('layouts.admin')

@section('title', 'Subscription Plans')
@section('page_title', 'Branch: Subscription Plans')

@section('content')
<!-- Branch Plans Section -->
<div class="admin-card">
    <div class="card-header bg-white border-0 p-4">
        <div class="d-flex justify-content-between align-items-center">
            <h5 class="fw-bold mb-0">Your Branch Plans</h5>
            <div>
                <button type="button" class="btn btn-sm btn-brand text-dark fw-bold rounded-pill px-3" data-bs-toggle="modal" data-bs-target="#createPlanModal">
                    <i class="bi bi-plus-circle me-1"></i> Create Plan
                </button>
            </div>
        </div>
    </div>
    <div class="card-body p-0">
        @if($branchPlans->count() > 0)
            <div class="admin-table-wrap">
                <table class="table table-hover align-middle mb-0 admin-responsive-table">
                    <thead class="bg-light">
                        <tr>
                            <th class="border-0 px-4 py-3 text-muted small fw-bold">Plan Name</th>
                            <th class="border-0 px-4 py-3 text-muted small fw-bold">Price</th>
                            <th class="border-0 px-4 py-3 text-muted small fw-bold">Duration</th>
                            <th class="border-0 px-4 py-3 text-muted small fw-bold">Max Rides</th>
                            <th class="border-0 px-4 py-3 text-muted small fw-bold">Max Amount</th>
                            <th class="border-0 px-4 py-3 text-muted small fw-bold">Status</th>
                            <th class="border-0 px-4 py-3 text-muted small fw-bold text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($branchPlans as $bPlan)
                        <tr>
                            <td class="px-4 py-3 fw-bold text-dark">{{ $bPlan->name }}</td>
                            <td class="px-4 py-3 text-success fw-bold">?{{ number_format($bPlan->price, 2) }}</td>
                            <td class="px-4 py-3">{{ $bPlan->duration_days }} Days</td>
                            <td class="px-4 py-3">{{ $bPlan->max_rides ?? 'Unlimited' }}</td>
                            <td class="px-4 py-3">{{ $bPlan->max_ride_amount ? '?'.number_format($bPlan->max_ride_amount, 2) : 'Unlimited' }}</td>
                            <td class="px-4 py-3">
                                <span class="badge {{ $bPlan->is_active ? 'bg-success-subtle text-success' : 'bg-danger-subtle text-danger' }} rounded-pill px-3 py-2">
                                    {{ $bPlan->is_active ? 'Active' : 'Inactive' }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-end">
                                <form action="{{ route('branch.subscriptions.plans.destroy', $bPlan->id) }}" method="POST" class="d-inline-block" onsubmit="return confirm('Are you sure you want to delete this plan?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-light text-danger rounded-circle p-2" title="Delete Plan">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="text-center py-5">
                <i class="bi bi-box-seam text-muted" style="font-size: 2.5rem;"></i>
                <p class="text-muted mt-3 mb-0">You haven't created any plans yet.</p>
            </div>
        @endif
    </div>
</div>


<!-- Create Plan Modal -->
<div class="modal fade" id="createPlanModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <form action="{{ route('branch.subscriptions.plans.store') }}" method="POST">
                @csrf
                <div class="modal-header border-0 p-4">
                    <h5 class="modal-title fw-bold">Create New Plan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4 pt-0">
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-muted">Plan Name <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control" placeholder="e.g. Premium Driver" required>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label small fw-bold text-muted">Price (?) <span class="text-danger">*</span></label>
                            <input type="number" name="price" class="form-control" placeholder="29.99" step="0.01" min="0" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label small fw-bold text-muted">Duration (Days) <span class="text-danger">*</span></label>
                            <input type="number" name="duration_days" class="form-control" placeholder="30" min="1" required>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label small fw-bold text-muted">Max Rides Allowed</label>
                            <input type="number" name="max_rides" class="form-control" placeholder="Blank for Unlimited" min="1">
                            <div class="form-text small"><i class="bi bi-info-circle"></i> Blank = Unlimited rides</div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label small fw-bold text-muted">Max Ride Amount (?)</label>
                            <input type="number" name="max_ride_amount" class="form-control" placeholder="Blank for Unlimited" step="0.01" min="0">
                            <div class="form-text small"><i class="bi bi-info-circle"></i> Max fare covered per ride</div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0 p-4 pt-0">
                    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary rounded-pill px-4">Create Plan</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

