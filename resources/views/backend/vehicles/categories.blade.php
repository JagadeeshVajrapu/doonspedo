@extends('layouts.admin')

@section('title', 'Vehicle Categories')
@section('page_title', 'Vehicle Categories Management')

@section('content')

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

<div class="d-flex justify-content-between align-items-center mb-4">
    <h5 class="fw-bold mb-0 text-dark"><i class="bi bi-car-front text-brand me-2"></i> Manage Vehicle Types</h5>
    <button type="button" class="btn btn-brand btn-sm px-3 rounded-pill fw-bold shadow-sm" data-bs-toggle="modal" data-bs-target="#createCategoryModal">
        <i class="bi bi-plus-lg me-1"></i> Add Category
    </button>
</div>

<div class="row g-4">
    @if(isset($categories) && count($categories) > 0)
        @foreach($categories as $category)
        <div class="col-md-4 col-sm-6">
            <div class="card h-100 border-0 shadow-sm rounded-4 overflow-hidden position-relative {{ !$category->is_active ? 'opacity-75' : '' }}">
                <div class="bg-light p-4 text-center border-bottom border-secondary border-opacity-10 position-relative">
                    @if($category->is_active)
                        <span class="position-absolute top-0 end-0 m-2 badge bg-success bg-opacity-10 text-success rounded-pill px-3 py-1">Active</span>
                    @else
                        <span class="position-absolute top-0 end-0 m-2 badge bg-secondary bg-opacity-10 text-secondary rounded-pill px-3 py-1">Inactive</span>
                    @endif
                    <i class="bi {{ $category->icon }} text-muted" style="font-size: 4rem;"></i>
                </div>
                <div class="card-body p-4 text-center d-flex flex-column">
                    <h5 class="fw-bold text-dark mb-1">{{ $category->name }}</h5>
                    <p class="text-muted small mb-3">{{ $category->description ?? 'No description provided' }}</p>
                    <div class="d-flex justify-content-center gap-2 mb-2 mt-auto">
                        <span class="badge bg-secondary bg-opacity-10 text-secondary border px-2 py-1"><i class="bi bi-people me-1"></i> {{ $category->capacity_seats }} Seats</span>
                        <span class="badge bg-secondary bg-opacity-10 text-secondary border px-2 py-1"><i class="bi bi-briefcase me-1"></i> {{ $category->capacity_bags }} Bags</span>
                    </div>
                    <div class="d-flex justify-content-center gap-2 mb-3">
                        <span class="small text-muted fw-bold">Base: {{ $sys_settings['currency_symbol'] ?? '₹' }}{{ $category->base_fare }}</span>
                        <span class="small text-muted fw-bold">|</span>
                        <span class="small text-muted fw-bold">Rate: {{ $sys_settings['currency_symbol'] ?? '₹' }}{{ $category->rate_per_km }}/km</span>
                    </div>
                    <div class="mt-3 d-flex gap-2">
                        <button type="button" class="btn btn-outline-primary w-50 btn-sm rounded-pill" data-bs-toggle="modal" data-bs-target="#editCategoryModal_{{ $category->id }}">
                            <i class="bi bi-pencil me-1"></i> Edit
                        </button>
                        <form action="{{ route('admin.vehicles.categories.delete', $category->id) }}" method="POST" class="w-50" onsubmit="return confirm('Are you sure you want to delete this category?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-outline-danger w-100 btn-sm rounded-pill"><i class="bi bi-trash me-1"></i> Delete</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    @else
        <div class="col-12 text-center py-5">
            <div class="bg-light d-inline-block p-4 rounded-circle mb-3">
                <i class="bi bi-car-front text-muted" style="font-size: 3rem;"></i>
            </div>
            <h5 class="text-muted fw-bold">No Vehicle Categories Found</h5>
            <p class="text-muted small">Get started by creating your first vehicle category.</p>
        </div>
    @endif
</div>

<!-- Edit Modals outside for reliability -->
@if(isset($categories) && count($categories) > 0)
@foreach($categories as $category)
<div class="modal fade" id="editCategoryModal_{{ $category->id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
            <div class="modal-header bg-light border-0 py-3 px-4">
                <h5 class="modal-title fw-bold"><i class="bi bi-pencil-square text-primary me-2"></i> Edit Category</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('admin.vehicles.categories.update', $category->id) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body p-4 bg-white">
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-muted">Category Name</label>
                        <input type="text" class="form-control" name="name" value="{{ $category->name }}" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-muted">Description</label>
                        <textarea class="form-control" name="description" rows="2">{{ $category->description }}</textarea>
                    </div>
                    <div class="row g-3 mb-3">
                        <div class="col-sm-6">
                            <label class="form-label small fw-bold text-muted">Passenger Capacity</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light text-muted"><i class="bi bi-people"></i></span>
                                <input type="number" class="form-control" name="capacity_seats" value="{{ $category->capacity_seats }}" min="1">
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <label class="form-label small fw-bold text-muted">Bag Capacity</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light text-muted"><i class="bi bi-briefcase"></i></span>
                                <input type="number" class="form-control" name="capacity_bags" value="{{ $category->capacity_bags }}" min="0">
                            </div>
                        </div>
                    </div>
                    <div class="row g-3 mb-3">
                        <div class="col-sm-6">
                            <label class="form-label small fw-bold text-muted">Base Fare ({{ $sys_settings['currency_symbol'] ?? '₹' }})</label>
                            <input type="number" step="0.01" class="form-control" name="base_fare" value="{{ $category->base_fare }}" min="0" required>
                        </div>
                        <div class="col-sm-6">
                            <label class="form-label small fw-bold text-muted">Rate Per KM ({{ $sys_settings['currency_symbol'] ?? '₹' }})</label>
                            <input type="number" step="0.01" class="form-control" name="rate_per_km" value="{{ $category->rate_per_km }}" min="0" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-muted">Bootstrap Icon Class</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light text-muted"><i class="bi {{ $category->icon }}"></i></span>
                            <input type="text" class="form-control" name="icon" value="{{ $category->icon }}" placeholder="bi-car-front" required>
                        </div>
                        <div class="form-text small">Use a valid bootstrap icon class like 'bi-car-front' or 'bi-truck'</div>
                    </div>
                    <div class="mb-3 form-check form-switch mt-4">
                        <input class="form-check-input" type="checkbox" name="is_active" value="1" id="activeSwitch_{{ $category->id }}" {{ $category->is_active ? 'checked' : '' }}>
                        <label class="form-check-label fw-bold" for="activeSwitch_{{ $category->id }}">Category is Active</label>
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
@endif

<!-- Create Category Modal -->
<div class="modal fade" id="createCategoryModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
            <div class="modal-header bg-light border-0 py-3 px-4">
                <h5 class="modal-title fw-bold"><i class="bi bi-plus-circle text-brand me-2"></i> Create New Category</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('admin.vehicles.categories.store') }}" method="POST">
                @csrf
                <div class="modal-body p-4 bg-white">
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-muted">Category Name *</label>
                        <input type="text" class="form-control" name="name" placeholder="e.g. Premium Sedan" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-muted">Description</label>
                        <textarea class="form-control" name="description" rows="2" placeholder="Brief details about the category"></textarea>
                    </div>
                    <div class="row g-3 mb-3">
                        <div class="col-sm-6">
                            <label class="form-label small fw-bold text-muted">Passenger Capacity *</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light text-muted"><i class="bi bi-people"></i></span>
                                <input type="number" class="form-control" name="capacity_seats" value="4" min="1" required>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <label class="form-label small fw-bold text-muted">Bag Capacity *</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light text-muted"><i class="bi bi-briefcase"></i></span>
                                <input type="number" class="form-control" name="capacity_bags" value="2" min="0" required>
                            </div>
                        </div>
                    </div>
                    <div class="row g-3 mb-3">
                        <div class="col-sm-6">
                            <label class="form-label small fw-bold text-muted">Base Fare ({{ $sys_settings['currency_symbol'] ?? '₹' }}) *</label>
                            <input type="number" step="0.01" class="form-control" name="base_fare" value="50" min="0" required>
                        </div>
                        <div class="col-sm-6">
                            <label class="form-label small fw-bold text-muted">Rate Per KM ({{ $sys_settings['currency_symbol'] ?? '₹' }}) *</label>
                            <input type="number" step="0.01" class="form-control" name="rate_per_km" value="15" min="0" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-muted">Bootstrap Icon Class *</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light text-muted"><i class="bi bi-star"></i></span>
                            <input type="text" class="form-control" name="icon" value="bi-car-front" placeholder="bi-car-front" required>
                        </div>
                        <div class="form-text small">Example: bi-car-front, bi-truck, bi-car-front-fill</div>
                    </div>
                    <input type="hidden" name="is_active" value="1">
                </div>
                <div class="modal-footer bg-light border-0 py-3 px-4">
                    <button type="button" class="btn btn-secondary rounded-pill px-4 fw-bold" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-brand rounded-pill px-4 fw-bold">Create Category</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection
