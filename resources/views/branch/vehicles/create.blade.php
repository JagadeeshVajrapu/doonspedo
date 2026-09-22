@extends('layouts.admin')

@section('title', 'Add Vehicle')
@section('page_title', 'Branch: Create New Vehicle')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="admin-card">
            <div class="card-header bg-white border-0 p-4">
                <h5 class="fw-bold mb-0">Vehicle Details</h5>
            </div>
            <div class="card-body p-4 pt-0">
                <form action="{{ route('branch.vehicles.store') }}" method="POST">
                    @csrf
                    
                    <div class="row g-4">
                        <!-- Driver Selection -->
                        <div class="col-md-12">
                            <label class="form-label small fw-bold text-muted text-uppercase">Select Driver</label>
                            <select name="driver_id" class="form-select rounded-3 p-3" required>
                                <option value="" disabled selected>Choose a driver from your branch</option>
                                @foreach($drivers as $driver)
                                    <option value="{{ $driver->id }}">{{ $driver->name }} ({{ $driver->mobile }})</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Vehicle Category -->
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-muted text-uppercase">Vehicle Category</label>
                            <select name="vehicle_category_id" class="form-select rounded-3 p-3" required>
                                <option value="" disabled selected>Select type</option>
                                @foreach($categories as $cat)
                                    <option value="{{ $cat->id }}">{{ $cat->name }} ({{ $cat->capacity_seats }} seats)</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Brand -->
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-muted text-uppercase">Brand</label>
                            <input type="text" name="brand" class="form-control rounded-3 p-3" placeholder="e.g. Maruti, Toyota" required>
                        </div>

                        <!-- Model -->
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-muted text-uppercase">Model</label>
                            <input type="text" name="model" class="form-control rounded-3 p-3" placeholder="e.g. Dzire, Innova" required>
                        </div>

                        <!-- Number Plate -->
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-muted text-uppercase">Number Plate</label>
                            <input type="text" name="number_plate" class="form-control rounded-3 p-3" placeholder="e.g. DL 1AB 1234" required>
                        </div>

                        <!-- Color -->
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-muted text-uppercase">Color</label>
                            <input type="text" name="color" class="form-control rounded-3 p-3" placeholder="e.g. White, Silver" required>
                        </div>

                        <!-- Year -->
                        <div class="col-md-4">
                            <label class="form-label small fw-bold text-muted text-uppercase">Year</label>
                            <input type="number" name="year" class="form-control rounded-3 p-3" value="{{ date('Y') }}" min="1990" max="{{ date('Y')+1 }}" required>
                        </div>

                        <!-- Minimum Price -->
                        <div class="col-md-4">
                            <label class="form-label small fw-bold text-muted text-uppercase">Min Price (Base Fare)</label>
                            <div class="input-group">
                                <span class="input-group-text">₹</span>
                                <input type="number" name="min_price" class="form-control rounded-3 p-3" step="0.01" placeholder="0.00" required>
                            </div>
                        </div>

                        <!-- Per KM Price -->
                        <div class="col-md-4">
                            <label class="form-label small fw-bold text-muted text-uppercase">Per KM Price</label>
                            <div class="input-group">
                                <span class="input-group-text">₹</span>
                                <input type="number" name="per_km_price" class="form-control rounded-3 p-3" step="0.01" placeholder="0.00" required>
                            </div>
                        </div>

                        <div class="col-12 mt-4">
                            <div class="alert alert-info border-0 rounded-3 small">
                                <i class="bi bi-info-circle me-1"></i> Vehicles created by branch managers are auto-approved and set as active.
                            </div>
                        </div>

                        <div class="col-12 mt-2">
                            <button type="submit" class="btn btn-brand text-dark w-100 py-3 fw-bold rounded-pill shadow">
                                <i class="bi bi-check-lg me-1"></i> CREATE & ASSIGN VEHICLE
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
