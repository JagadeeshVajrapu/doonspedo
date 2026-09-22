@extends('layouts.admin')

@section('title', 'Subscription Plans')
@section('page_title', 'Subscription Plans for Drivers')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h5 class="fw-bold mb-0 text-dark"><i class="bi bi-card-checklist text-brand me-2"></i> Manage Subscriptions</h5>
    <button type="button" class="btn btn-brand rounded-pill px-4 shadow-sm" data-bs-toggle="modal" data-bs-target="#createPlanModal">
        <i class="bi bi-plus-lg me-1"></i> Add Plan
    </button>
</div>

@if(session('success'))
    @include('partials.ui.alert', ['variant' => 'success', 'message' => session('success')])
@endif

@if($errors->any())
    <div class="alert alert-danger rounded-4 shadow-sm border-0 mb-4">
        <ul class="mb-0">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<div class="row g-4">
    @if(isset($plans) && count($plans) > 0)
        @foreach($plans as $plan)
        <div class="col-md-6 col-lg-4">
            <div class="card border-0 shadow-sm rounded-4 h-100 position-relative {{ !$plan->is_active ? 'opacity-75' : '' }}">
                @if($plan->is_active)
                    <span class="position-absolute top-0 end-0 m-3 badge bg-success bg-opacity-10 text-success rounded-pill px-3 py-1">Active</span>
                @else
                    <span class="position-absolute top-0 end-0 m-3 badge bg-secondary bg-opacity-10 text-secondary rounded-pill px-3 py-1">Inactive</span>
                @endif
                
                <div class="card-body p-4 text-center d-flex flex-column pt-5">
                    <h4 class="fw-bold text-dark mb-1">{{ $plan->name }}</h4>
                    <h2 class="fw-bold text-primary mb-0">{{ $default_currency?->symbol ?? '?' }}{{ number_format($plan->price, 2) }}</h2>
                    <p class="text-muted small mb-4">per {{ $plan->duration_days }} days</p>
                    <ul class="list-unstyled text-start mb-4 flex-grow-1 border-top border-secondary border-opacity-10 pt-3">
                        <li class="mb-2">
                            <i class="bi bi-car-front-fill text-primary me-2"></i>
                            <strong>Max Rides:</strong>
                            @if($plan->max_rides)
                                <span class="badge bg-primary bg-opacity-10 text-primary rounded-pill px-2">{{ $plan->max_rides }} rides</span>
                            @else
                                <span class="badge bg-success bg-opacity-10 text-success rounded-pill px-2">Unlimited</span>
                            @endif
                        </li>
                        <li class="mb-2">
                            <i class="bi bi-cash-stack text-success me-2"></i>
                            <strong>Max Amount/Ride:</strong>
                            @if($plan->max_ride_amount)
                                <span class="badge bg-success bg-opacity-10 text-success rounded-pill px-2">{{ $default_currency?->symbol ?? '?' }}{{ number_format($plan->max_ride_amount, 2) }}</span>
                            @else
                                <span class="badge bg-success bg-opacity-10 text-success rounded-pill px-2">Unlimited</span>
                            @endif
                        </li>
                    </ul>

                    <div class="d-flex gap-2 mt-auto">
                        <button type="button" class="btn btn-outline-primary w-50 rounded-pill btn-sm fw-bold" data-bs-toggle="modal" data-bs-target="#editPlanModal_{{ $plan->id }}">
                            <i class="bi bi-pencil me-1"></i> Edit
                        </button>
                        <form action="{{ route('admin.subscriptions.destroy', $plan->id) }}" method="POST" class="w-50" onsubmit="return confirm('Are you sure you want to delete this plan?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-outline-danger w-100 rounded-pill btn-sm fw-bold"><i class="bi bi-trash me-1"></i> Delete</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Edit Plan Modal -->
        <div class="modal fade" id="editPlanModal_{{ $plan->id }}" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
                    <div class="modal-header bg-light border-0 py-3 px-4">
                        <h5 class="modal-title fw-bold"><i class="bi bi-pencil-square text-primary me-2"></i> Edit Plan</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form action="{{ route('admin.subscriptions.update', $plan->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="modal-body p-4 bg-white">
                            <div class="mb-3">
                                <label class="form-label small fw-bold text-muted">Plan Name</label>
                                <input type="text" class="form-control" name="name" value="{{ $plan->name }}" required>
                            </div>
                            <div class="row g-3 mb-3">
                                <div class="col-sm-6">
                                    <label class="form-label small fw-bold text-muted">Price ({{ $default_currency?->symbol ?? '?' }})</label>
                                    <input type="number" class="form-control" name="price" value="{{ $plan->price }}" step="0.01" min="0" required>
                                </div>
                                <div class="col-sm-6">
                                    <label class="form-label small fw-bold text-muted">Duration (Days)</label>
                                    <input type="number" class="form-control" name="duration_days" value="{{ $plan->duration_days }}" min="1" required>
                                </div>
                            </div>
                            <div class="row g-3 mb-3">
                                <div class="col-sm-6">
                                    <label class="form-label small fw-bold text-muted">Max Rides Allowed</label>
                                    <input type="number" class="form-control" name="max_rides" value="{{ $plan->max_rides }}" min="1" placeholder="Blank for Unlimited">
                                    <div class="form-text small"><i class="bi bi-info-circle me-1"></i>Blank = Unlimited</div>
                                </div>
                                <div class="col-sm-6">
                                    <label class="form-label small fw-bold text-muted">Max Ride Amount ({{ $default_currency?->symbol ?? '?' }})</label>
                                    <input type="number" class="form-control" name="max_ride_amount" value="{{ $plan->max_ride_amount }}" step="0.01" min="0" placeholder="Blank for Unlimited">
                                    <div class="form-text small"><i class="bi bi-info-circle me-1"></i>Max fare per ride</div>
                                </div>
                            </div>
                            <div class="mb-3 form-check form-switch mt-4">
                                <input class="form-check-input" type="checkbox" name="is_active" id="activeSwitch_{{ $plan->id }}" {{ $plan->is_active ? 'checked' : '' }}>
                                <label class="form-check-label fw-bold" for="activeSwitch_{{ $plan->id }}">Plan is Active</label>
                            </div>
                        </div>
                        <div class="modal-footer bg-light border-0 py-3 px-4">
                            <button type="button" class="btn btn-secondary rounded-pill px-4 fw-bold" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-primary rounded-pill px-4 fw-bold">Save Changes</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        @endforeach
    @else
        <div class="col-12 text-center py-5">
            <div class="bg-light d-inline-block p-4 rounded-circle mb-3">
                <i class="bi bi-card-checklist text-muted" style="font-size: 3rem;"></i>
            </div>
            <h5 class="text-muted fw-bold">No Subscription Plans Found</h5>
            <p class="text-muted small">Drivers can use the platform by default (commission based) or buy plans if you create them.</p>
        </div>
    @endif
</div>

<!-- Create Plan Modal -->
<div class="modal fade" id="createPlanModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
            <div class="modal-header bg-light border-0 py-3 px-4">
                <h5 class="modal-title fw-bold"><i class="bi bi-plus-circle text-brand me-2"></i> Create New Plan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('admin.subscriptions.store') }}" method="POST">
                @csrf
                <div class="modal-body p-4 bg-white">
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-muted">Plan Name *</label>
                        <input type="text" class="form-control" name="name" placeholder="e.g. Premium Driver" required>
                    </div>
                    <div class="row g-3 mb-3">
                        <div class="col-sm-6">
                            <label class="form-label small fw-bold text-muted">Price ({{ $default_currency?->symbol ?? '?' }}) *</label>
                            <input type="number" class="form-control" name="price" value="29.99" step="0.01" min="0" required>
                        </div>
                        <div class="col-sm-6">
                            <label class="form-label small fw-bold text-muted">Duration (Days) *</label>
                            <input type="number" class="form-control" name="duration_days" value="30" min="1" required>
                        </div>
                    </div>
                    <div class="row g-3 mb-3">
                        <div class="col-sm-6">
                            <label class="form-label small fw-bold text-muted">Max Rides Allowed</label>
                            <input type="number" class="form-control" name="max_rides" min="1" placeholder="Blank for Unlimited">
                            <div class="form-text small"><i class="bi bi-info-circle me-1"></i>Blank = Unlimited</div>
                        </div>
                        <div class="col-sm-6">
                            <label class="form-label small fw-bold text-muted">Max Ride Amount ({{ $default_currency?->symbol ?? '?' }})</label>
                            <input type="number" class="form-control" name="max_ride_amount" step="0.01" min="0" placeholder="Blank for Unlimited">
                            <div class="form-text small"><i class="bi bi-info-circle me-1"></i>Max fare per ride</div>
                        </div>
                    </div>
                    <input type="hidden" name="is_active" value="1">
                </div>
                <div class="modal-footer bg-light border-0 py-3 px-4">
                    <button type="button" class="btn btn-secondary rounded-pill px-4 fw-bold" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-brand rounded-pill px-4 fw-bold">Create Plan</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

