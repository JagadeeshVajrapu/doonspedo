@extends('layouts.admin')

@section('title', 'Driver Applications')

@section('content')
<div class="p-4 p-md-5">

    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-5">
        <div>
            <h1 class="h2 fw-bold text-dark mb-1">Driver Applications</h1>
            <p class="text-muted mb-0">Manage all driver registration requests</p>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show rounded-3 mb-4" role="alert">
            <i class="bi bi-check-circle me-2"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Stats -->
    <div class="row g-4 mb-5">
        <div class="col-md-4">
            <div class="card p-4 border-0 shadow-sm">
                <div class="d-flex align-items-center">
                    <div class="bg-warning bg-opacity-10 p-3 rounded-circle me-3">
                        <i class="bi bi-hourglass-split text-warning fs-4"></i>
                    </div>
                    <div>
                        <p class="text-muted small text-uppercase fw-bold mb-0">Pending</p>
                        <h3 class="fw-bold mb-0">{{ $totalPending }}</h3>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card p-4 border-0 shadow-sm">
                <div class="d-flex align-items-center">
                    <div class="bg-success bg-opacity-10 p-3 rounded-circle me-3">
                        <i class="bi bi-check-circle text-success fs-4"></i>
                    </div>
                    <div>
                        <p class="text-muted small text-uppercase fw-bold mb-0">Approved</p>
                        <h3 class="fw-bold mb-0">{{ $totalApproved }}</h3>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card p-4 border-0 shadow-sm">
                <div class="d-flex align-items-center">
                    <div class="bg-danger bg-opacity-10 p-3 rounded-circle me-3">
                        <i class="bi bi-x-circle text-danger fs-4"></i>
                    </div>
                    <div>
                        <p class="text-muted small text-uppercase fw-bold mb-0">Rejected</p>
                        <h3 class="fw-bold mb-0">{{ $totalRejected }}</h3>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Table -->
    <div class="admin-card">
        <div class="card-header bg-white py-3">
            <h5 class="mb-0 fw-bold">All Applications</h5>
        </div>
        <div class="card-body p-0">
            <div class="admin-table-wrap">
                <table class="table table-hover align-middle mb-0 admin-responsive-table">
                    <thead class="bg-light">
                        <tr>
                            <th class="px-4">#ID</th>
                            <th>Name</th>
                            <th>Mobile</th>
                            <th>Email</th>
                            <th>Vehicle</th>
                            <th>City</th>
                            <th>Status</th>
                            <th class="text-end px-4">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if(isset($applications) && count($applications) > 0)
                            @foreach($applications as $app)
                            <tr>
                                <td class="px-4 text-muted">#{{ $app->id }}</td>
                                <td class="fw-semibold">{{ $app->name }}</td>
                                <td>{{ $app->mobile }}</td>
                                <td class="text-muted">{{ $app->email }}</td>
                                <td>
                                    <span class="badge bg-light text-dark border">
                                        {{ ucfirst($app->vehicle_type) }} &bull; {{ $app->vehicle_number }}
                                    </span>
                                </td>
                                <td>{{ $app->city }}</td>
                                <td>
                                    @if($app->status === 'pending')
                                        <span class="badge bg-warning text-dark rounded-pill">Pending</span>
                                    @elseif($app->status === 'approved')
                                        <span class="badge bg-success rounded-pill">Approved</span>
                                    @else
                                        <span class="badge bg-danger rounded-pill">Rejected</span>
                                    @endif
                                </td>
                                <td class="text-end px-4">
                                    @if($app->status === 'pending')
                                    <a href="{{ route('admin.driver-applications.status', [$app->id, 'approved']) }}"
                                       class="btn btn-sm btn-success me-1"
                                       onclick="return confirm('Approve this driver?')">
                                        <i class="bi bi-check-lg"></i> Approve
                                    </a>
                                    <a href="{{ route('admin.driver-applications.status', [$app->id, 'rejected']) }}"
                                       class="btn btn-sm btn-danger"
                                       onclick="return confirm('Reject this application?')">
                                        <i class="bi bi-x-lg"></i> Reject
                                    </a>
                                    @else
                                        <span class="text-muted small">Done</span>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        @else
                            <tr>
                                <td colspan="8" class="text-center py-5 text-muted">
                                    <i class="bi bi-inbox fs-1 d-block mb-3"></i>
                                    No driver applications yet.
                                </td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
        @if($applications->hasPages())
        <div class="card-footer bg-white">
            {{ $applications->links() }}
        </div>
        @endif
    </div>

</div>
@endsection
