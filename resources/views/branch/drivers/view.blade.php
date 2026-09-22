@extends('layouts.admin')

@section('title', 'Driver Profile')
@section('page_title', 'Driver Details')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h5 class="fw-bold mb-0 text-dark">Driver Profile</h5>
    <a href="{{ route('branch.drivers.index') }}" class="btn btn-outline-secondary rounded-pill px-4 shadow-sm">
        <i class="bi bi-arrow-left me-1"></i> Back to Drivers
    </a>
</div>

<div class="admin-card">
    <div class="card-body p-4">
        <div class="row">
            <div class="col-md-4 text-center mb-4 mb-md-0">
                @if($driver->profile_image)
                    <img src="{{ asset('storage/' . $driver->profile_image) }}" alt="Driver Profile" class="img-fluid rounded-circle shadow-sm" style="width: 150px; height: 150px; object-fit: cover;">
                @else
                    <div class="bg-light rounded-circle d-flex align-items-center justify-content-center mx-auto shadow-sm" style="width: 150px; height: 150px;">
                        <i class="bi bi-person text-secondary" style="font-size: 4rem;"></i>
                    </div>
                @endif
                <h4 class="fw-bold mt-3 mb-1">{{ $driver->name }}</h4>
                <span class="badge {{ $driver->status == 'approved' ? 'bg-success' : ($driver->status == 'rejected' ? 'bg-danger' : 'bg-warning text-dark') }} rounded-pill px-3">
                    {{ ucfirst($driver->status) }}
                </span>
            </div>
            
            <div class="col-md-8">
                <h5 class="border-bottom pb-2 mb-3 fw-bold text-dark">Contact Information</h5>
                <div class="row mb-4">
                    <div class="col-sm-6 mb-3">
                        <small class="text-muted d-block">Mobile Number</small>
                        <span class="fw-medium fs-5">{{ $driver->mobile }}</span>
                    </div>
                    <div class="col-sm-6 mb-3">
                        <small class="text-muted d-block">Email Address</small>
                        <span class="fw-medium fs-5">{{ $driver->email }}</span>
                    </div>
                    <div class="col-sm-6 mb-3">
                        <small class="text-muted d-block">City</small>
                        <span class="fw-medium fs-5">{{ $driver->city ?? 'N/A' }}</span>
                    </div>
                </div>

                <h5 class="border-bottom pb-2 mb-3 fw-bold text-dark">Vehicle & License Details</h5>
                <div class="row">
                    <div class="col-sm-6 mb-3">
                        <small class="text-muted d-block">Vehicle Number</small>
                        <span class="fw-medium fs-5 text-uppercase">{{ $driver->vehicle_number }}</span>
                    </div>
                    <div class="col-sm-6 mb-3">
                        <small class="text-muted d-block">Vehicle Type</small>
                        <span class="fw-medium fs-5">{{ ucfirst($driver->vehicle_type) ?? 'N/A' }}</span>
                    </div>
                    <div class="col-sm-6 mb-3">
                        <small class="text-muted d-block">License Number</small>
                        <span class="fw-medium fs-5 text-uppercase">{{ $driver->license_number ?? 'N/A' }}</span>
                    </div>
                    <div class="col-sm-6 mb-3">
                        <small class="text-muted d-block">Registered On</small>
                        <span class="fw-medium fs-5">{{ $driver->created_at ? $driver->created_at->format('d M, Y') : 'N/A' }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
