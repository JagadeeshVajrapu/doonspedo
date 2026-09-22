@extends('layouts.admin')

@section('title', 'Branch Vehicles')
@section('page_title', 'Branch: Managed Vehicles')

@section('content')
<div class="mb-4 d-flex justify-content-between align-items-center">
    <div>
        <h3 class="fw-bold mb-1">Fleet Management</h3>
        <p class="text-muted mb-0 small">List of all vehicles assigned to drivers in your branch.</p>
    </div>
    <a href="{{ route('branch.vehicles.create') }}" class="btn btn-brand text-dark rounded-pill px-4 fw-bold shadow-sm">
        <i class="bi bi-plus-lg me-1"></i> CREATE VEHICLE
    </a>
</div>

@if(session('success'))
    <div class="alert alert-success border-0 shadow-sm rounded-4 mb-4">
        <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
    </div>
@endif

<div class="admin-card">
    <div class="card-body p-0">
        <div class="admin-table-wrap">
            <table class="table table-hover align-middle mb-0 admin-responsive-table">
                <thead class="bg-light">
                    <tr>
                        <th class="ps-4">Vehicle</th>
                        <th>Driver</th>
                        <th>Category</th>
                        <th>Number Plate</th>
                        <th>Pricing</th>
                        <th>Status</th>
                        <th class="text-end pe-4">Created</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($vehicles as $vehicle)
                    <tr>
                        <td class="ps-4">
                            <div class="fw-bold text-dark">{{ $vehicle->brand }} {{ $vehicle->model }}</div>
                            <div class="small text-muted">{{ $vehicle->color }} ({{ $vehicle->year }})</div>
                        </td>
                        <td>
                            <div class="fw-bold">{{ $vehicle->driver->name }}</div>
                            <div class="small text-muted">{{ $vehicle->driver->mobile }}</div>
                        </td>
                        <td>
                            <span class="badge bg-dark rounded-pill px-3 fw-normal small">
                                {{ $vehicle->category->name }}
                            </span>
                        </td>
                        <td><span class="fw-bold text-primary">{{ $vehicle->number_plate }}</span></td>
                        <td>
                            <div class="small fw-bold">Min: ₹{{ number_format($vehicle->min_price, 2) }}</div>
                            <div class="small">KM: ₹{{ number_format($vehicle->per_km_price, 2) }}</div>
                        </td>
                        <td>
                            @if($vehicle->status == 'active')
                                <span class="badge bg-success bg-opacity-10 text-success rounded-pill px-3 py-1">Active</span>
                            @else
                                <span class="badge bg-secondary bg-opacity-10 text-secondary rounded-pill px-3 py-1">Inactive</span>
                            @endif
                            <br>
                            <small class="text-{{ $vehicle->verification_status == 'approved' ? 'success' : 'warning' }} fw-bold">
                                {{ ucfirst($vehicle->verification_status) }}
                            </small>
                        </td>
                        <td class="text-end pe-4 small text-muted">
                            {{ $vehicle->created_at->format('d M, Y') }}
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center py-5 text-muted">
                            <i class="bi bi-car-front fs-2 d-block mb-2"></i>
                            No vehicles found in your branch fleet.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($vehicles->hasPages())
    <div class="card-footer bg-white border-0 p-4">
        {{ $vehicles->links() }}
    </div>
    @endif
</div>
@endsection
