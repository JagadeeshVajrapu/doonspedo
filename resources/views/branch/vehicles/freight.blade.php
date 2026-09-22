@extends('layouts.admin')

@section('title', 'Parcel Categories')
@section('page_title', 'Parcel & Delivery Operations')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h5 class="fw-bold mb-0 text-dark"><i class="bi bi-box-seam text-brand me-2"></i> Manage Freight Vehicles</h5>
    <button data-bs-toggle="modal" data-bs-target="#addParcelModal" class="btn btn-brand btn-sm px-3 rounded-pill fw-bold shadow-sm">
        <i class="bi bi-plus-lg me-1"></i> Add Freight Category
    </button>
</div>

<!-- Intro Alert -->
<div class="alert alert-secondary bg-white border-0 shadow-sm rounded-4 mb-4 d-flex align-items-center">
    <i class="bi bi-info-circle fs-3 text-muted me-3"></i>
    <div>
        <strong class="text-dark">Delivery & Logistics:</strong> Use this section to set up pricing and payload limits for trucks, vans, and pick-ups used exclusively for package or freight delivery.
    </div>
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show shadow-sm border-0 rounded-3 mb-4" role="alert">
        <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

<div class="row g-4">
    @forelse($parcels as $parcel)
        <div class="col-md-6 col-lg-4">
            <div class="card h-100 border-0 shadow-sm rounded-4 overflow-hidden position-relative border-top border-4 border-{{ $parcel->is_active ? 'primary' : 'secondary border-opacity-50' }}">
                <div class="card-body p-4 text-center">
                    <div class="position-absolute top-0 end-0 m-3 badge {{ $parcel->is_active ? 'bg-success bg-opacity-10 text-success' : 'bg-secondary bg-opacity-10 text-secondary' }} rounded-pill px-3 py-1">
                        {{ $parcel->is_active ? 'Active' : 'Inactive' }}
                    </div>
                    
                    <div class="bg-{{ $parcel->is_active ? 'primary' : 'secondary' }} bg-opacity-10 rounded-circle d-inline-block p-4 mb-3">
                        <i class="bi {{ $parcel->icon ?? 'bi-box-seam' }} text-{{ $parcel->is_active ? 'primary' : 'secondary' }}" style="font-size: 3rem;"></i>
                    </div>
                    
                    <h5 class="fw-bold text-{{ $parcel->is_active ? 'dark' : 'muted' }} mb-1">{{ $parcel->name }}</h5>
                    <p class="text-muted small mb-3 border-bottom pb-3">{{ $parcel->description }}</p>
                    
                    <div class="text-start bg-light p-3 rounded-3 small">
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted fw-bold">Max Load:</span>
                            <span class="text-dark fw-bold">{{ $parcel->max_load ?? 'N/A' }}</span>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted fw-bold">Base Price:</span>
                            <span class="text-primary fw-bold">{{ $default_currency->symbol ?? '?' }}{{ $parcel->base_price }}</span>
                        </div>
                        <div class="d-flex justify-content-between">
                            <span class="text-muted fw-bold">Price / KM:</span>
                            <span class="text-dark fw-bold">{{ $default_currency->symbol ?? '?' }}{{ $parcel->price_per_km }}</span>
                        </div>
                    </div>
                    
                    <div class="mt-4 d-flex gap-2">
                        <button data-bs-toggle="modal" data-bs-target="#editParcelModal{{ $parcel->id }}" class="btn btn-outline-primary w-50 btn-sm rounded-pill"><i class="bi bi-pencil me-1"></i> Edit</button>
                        <form action="{{ route('branch.vehicles.parcel.delete', $parcel->id) }}" method="POST" class="w-50" onsubmit="return confirm('Are you sure you want to delete this freight category?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-outline-danger w-100 btn-sm rounded-pill"><i class="bi bi-trash me-1"></i> Delete</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Edit Parcel Modal -->
        <div class="modal fade" id="editParcelModal{{ $parcel->id }}" tabindex="-1" aria-labelledby="editParcelModalLabel{{ $parcel->id }}" aria-hidden="true">
            <div class="modal-dialog">
                <form action="{{ route('branch.vehicles.parcel.update', $parcel->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="modal-content border-0 shadow rounded-4">
                        <div class="modal-header border-0 pb-0">
                            <h5 class="modal-title fw-bold" id="editParcelModalLabel{{ $parcel->id }}">Edit Freight Category</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body pt-3">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Name</label>
                                <input type="text" class="form-control" name="name" value="{{ $parcel->name }}" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-bold">Description</label>
                                <textarea class="form-control" name="description" rows="2">{{ $parcel->description }}</textarea>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-bold">Max Load</label>
                                <input type="text" class="form-control" name="max_load" value="{{ $parcel->max_load }}">
                            </div>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">Base Price</label>
                                    <input type="number" step="0.01" class="form-control" name="base_price" value="{{ $parcel->base_price }}" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">Price / KM</label>
                                    <input type="number" step="0.01" class="form-control" name="price_per_km" value="{{ $parcel->price_per_km }}" required>
                                </div>
                            </div>
                            <div class="mb-3 form-check form-switch">
                                <input class="form-check-input" type="checkbox" name="is_active" id="isActiveSwitch{{ $parcel->id }}" value="1" {{ $parcel->is_active ? 'checked' : '' }}>
                                <label class="form-check-label fw-bold" for="isActiveSwitch{{ $parcel->id }}">Active</label>
                            </div>
                        </div>
                        <div class="modal-footer border-0 pt-0">
                            <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-brand rounded-pill px-4">Update Category</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    @empty
        <div class="col-12 text-center py-5">
            <div class="text-muted mb-3">
                <i class="bi bi-box-seam text-secondary opacity-50" style="font-size: 4rem;"></i>
            </div>
            <h5 class="fw-bold text-dark mb-1">No Freight Categories Found</h5>
            <p class="text-muted">You haven't added any parcel/freight vehicles yet. Click the button above to add one.</p>
        </div>
    @endforelse
</div>

<!-- Add Parcel Modal -->
<div class="modal fade" id="addParcelModal" tabindex="-1" aria-labelledby="addParcelModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <form action="{{ route('branch.vehicles.parcel.store') }}" method="POST">
            @csrf
            <div class="modal-content border-0 shadow rounded-4">
                <div class="modal-header border-0 pb-0">
                    <h5 class="modal-title fw-bold" id="addParcelModalLabel">Add Freight Category</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body pt-3">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Name</label>
                        <input type="text" class="form-control" name="name" required placeholder="e.g. Light Commercial">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Description</label>
                        <textarea class="form-control" name="description" rows="2" placeholder="e.g. Ideal for moving small furniture"></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Max Load</label>
                        <input type="text" class="form-control" name="max_load" placeholder="e.g. 750 KG">
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Base Price</label>
                            <input type="number" step="0.01" class="form-control" name="base_price" required placeholder="e.g. 25.00">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Price / KM</label>
                            <input type="number" step="0.01" class="form-control" name="price_per_km" required placeholder="e.g. 2.50">
                        </div>
                    </div>
                    <div class="mb-3 form-check form-switch">
                        <input class="form-check-input" type="checkbox" name="is_active" id="isActiveSwitch" checked value="1">
                        <label class="form-check-label fw-bold" for="isActiveSwitch">Active</label>
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-brand rounded-pill px-4">Save Category</button>
                </div>
            </div>
        </form>
    </div>
</div>

@endsection
