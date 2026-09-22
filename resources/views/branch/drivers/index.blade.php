@extends('layouts.admin')

@section('title', 'My Drivers')
@section('page_title', 'Drivers in your Branch')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h5 class="fw-bold mb-0 text-dark">Branch Drivers</h5>
    <a href="{{ route('branch.drivers.create') }}" class="btn btn-brand rounded-pill px-4 shadow-sm">
        <i class="bi bi-person-plus me-1"></i> Add New Driver
    </a>
</div>

@if(session('success'))
    <div class="alert alert-success border-0 shadow-sm rounded-4 mb-4">
        {{ session('success') }}
    </div>
@endif
<div class="admin-card">
    <div class="card-body p-0">
        <div class="admin-table-wrap">
            <table class="table table-hover align-middle mb-0 admin-responsive-table">
                <thead class="bg-light">
                    <tr>
                        <th class="py-3 px-4">Driver Name</th>
                        <th class="py-3">Mobile</th>
                        <th class="py-3">Vehicle</th>
                        <th class="py-3">Status</th>
                        <th class="py-3 text-end px-4">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($drivers as $driver)
                    <tr>
                        <td class="px-4 fw-bold text-dark">{{ $driver->name }}</td>
                        <td>{{ $driver->mobile }}</td>
                        <td>{{ $driver->vehicle_number }}</td>
                        <td>
                            <span class="badge bg-{{ $driver->status == 'approved' ? 'success' : 'warning' }} bg-opacity-10 text-{{ $driver->status == 'approved' ? 'success' : 'warning' }} rounded-pill px-3 py-1">
                                {{ ucfirst($driver->status) }}
                            </span>
                        </td>
                        <td class="text-end px-4">
                            <a href="{{ route('branch.drivers.view', $driver->id) }}" class="btn btn-sm btn-light rounded-pill px-3 border">View Profile</a>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="5" class="text-center py-5 text-muted">No drivers registered in your branch.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($drivers->hasPages())
    <div class="card-footer bg-white border-0 p-4">
        {{ $drivers->links() }}
    </div>
    @endif
</div>
@endsection
