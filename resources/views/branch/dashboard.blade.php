@extends('layouts.admin')

@section('title', 'Branch Dashboard')
@section('page_title', 'Branch: ' . $branch->name)

@section('content')
@if(session('success'))
    @include('partials.ui.alert', ['variant' => 'success', 'message' => session('success')])
@endif

<div class="row g-3 mb-4">
    <div class="col-md-4">
        <div class="admin-kpi">
            <div class="d-flex align-items-center justify-content-between mb-2">
                <div class="admin-kpi-label mb-0">Your drivers</div>
                <span class="rounded-3 bg-primary bg-opacity-10 text-primary p-2"><i class="bi bi-person-badge"></i></span>
            </div>
            <div class="admin-kpi-value">{{ number_format($totalDrivers) }}</div>
            <div class="small text-success mt-1">Active in your branch</div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="admin-kpi">
            <div class="d-flex align-items-center justify-content-between mb-2">
                <div class="admin-kpi-label mb-0">Total bookings</div>
                <span class="rounded-3 bg-success bg-opacity-10 text-success p-2"><i class="bi bi-calendar-check"></i></span>
            </div>
            <div class="admin-kpi-value">{{ number_format($totalBookings) }}</div>
            <div class="small text-muted mt-1">All time for this branch</div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="admin-kpi">
            <div class="d-flex align-items-center justify-content-between mb-2">
                <div class="admin-kpi-label mb-0">Pending bookings</div>
                <span class="rounded-3 bg-warning bg-opacity-10 text-warning p-2"><i class="bi bi-clock-history"></i></span>
            </div>
            <div class="admin-kpi-value text-warning">{{ number_format($pendingBookings) }}</div>
            <div class="small text-warning mt-1">Needs attention</div>
        </div>
    </div>
</div>

<div class="row g-4">
    <div class="col-lg-7">
        <div class="admin-card h-100">
            <div class="admin-card-header">
                <h2 class="admin-card-title">Recent branch bookings</h2>
                <a href="{{ route('branch.bookings.index') }}" class="btn btn-sm btn-link text-decoration-none fw-bold">View all</a>
            </div>
            <div class="admin-table-wrap">
                <table class="table table-hover align-middle mb-0 admin-responsive-table">
                    <thead>
                        <tr>
                            <th class="px-4 py-3">ID</th>
                            <th class="py-3">Pickup</th>
                            <th class="py-3">Status</th>
                            <th class="text-end px-4 py-3">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($recentBookings as $booking)
                        <tr>
                            <td class="px-4 fw-bold">#{{ $booking->id }}</td>
                            <td class="small text-truncate" style="max-width: 200px;">{{ $booking->pickup_location }}</td>
                            <td>
                                @include('partials.ui.status-badge', [
                                    'label' => ucfirst($booking->status),
                                    'variant' => $booking->status == 'completed' ? 'success' : ($booking->status == 'pending' ? 'warning' : 'danger'),
                                ])
                            </td>
                            <td class="text-end px-4">
                                <a href="{{ route('branch.bookings.index') }}" class="btn btn-sm btn-light border rounded-pill px-3">View</a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="p-4">
                                @include('partials.ui.empty-state', [
                                    'title' => 'No bookings found',
                                    'message' => 'Bookings for your branch will appear here.',
                                    'icon' => 'bi-calendar-check',
                                ])
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="col-lg-5">
        <div class="admin-card h-100">
            <div class="admin-card-header">
                <h2 class="admin-card-title">Your drivers</h2>
                <a href="{{ route('branch.drivers.index') }}" class="btn btn-sm btn-link text-decoration-none fw-bold">Manage</a>
            </div>
            <div class="list-group list-group-flush">
                @forelse($recentDrivers as $driver)
                <div class="list-group-item border-0 px-4 py-3 border-bottom">
                    <div class="d-flex align-items-center">
                        <div class="bg-light p-2 rounded-circle me-3">
                            <i class="bi bi-person fs-5"></i>
                        </div>
                        <div class="flex-grow-1">
                            <h3 class="h6 mb-0 fw-bold text-dark">{{ $driver->name }}</h3>
                            <p class="mb-0 small text-muted">{{ $driver->mobile }}</p>
                        </div>
                        @include('partials.ui.status-badge', [
                            'label' => ucfirst($driver->status),
                            'variant' => $driver->status == 'approved' ? 'success' : 'warning',
                        ])
                    </div>
                </div>
                @empty
                <div class="p-4">
                    @include('partials.ui.empty-state', [
                        'title' => 'No drivers yet',
                        'message' => 'Drivers registered to your branch will show here.',
                        'icon' => 'bi-person-badge',
                        'actionLabel' => 'Add driver',
                        'actionUrl' => route('branch.drivers.create'),
                    ])
                </div>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection
