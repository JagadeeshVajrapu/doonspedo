@extends('layouts.admin')

@section('title', 'All Bookings')
@section('page_title', 'Booking Management')

@section('content')
@if(session('success'))
    @include('partials.ui.alert', ['variant' => 'success', 'message' => session('success')])
@endif

<div class="admin-filter-bar">
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
        <h2 class="h6 fw-bold mb-0">
            <i class="bi bi-calendar-check text-brand me-2"></i>
            {{ request('service_type') ? ucfirst(request('service_type')) . ' ' : '' }}Bookings
        </h2>
    </div>
    <form action="{{ route('admin.bookings.index') }}" method="GET" class="row g-3">
        <div class="col-md-4">
            <label class="form-label small fw-bold text-muted" for="service_type">Service type</label>
            <select name="service_type" id="service_type" class="form-select">
                <option value="">All Services</option>
                <option value="ride" {{ request('service_type') == 'ride' ? 'selected' : '' }}>Ride</option>
                <option value="parcel" {{ request('service_type') == 'parcel' ? 'selected' : '' }}>Parcel</option>
                <option value="freight" {{ request('service_type') == 'freight' ? 'selected' : '' }}>Freight</option>
            </select>
        </div>
        <div class="col-md-4">
            <label class="form-label small fw-bold text-muted" for="status">Status</label>
            <select name="status" id="status" class="form-select">
                <option value="">All Statuses</option>
                <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                <option value="accepted" {{ request('status') == 'accepted' ? 'selected' : '' }}>Accepted</option>
                <option value="ongoing" {{ request('status') == 'ongoing' ? 'selected' : '' }}>Ongoing</option>
                <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
            </select>
        </div>
        <div class="col-md-4 d-flex align-items-end gap-2 flex-wrap">
            <button type="submit" class="btn btn-dark rounded-pill px-4 fw-bold"><i class="bi bi-filter me-1"></i> Filter</button>
            <a href="{{ route('admin.bookings.index') }}" class="btn btn-outline-secondary rounded-pill px-4">Reset</a>
        </div>
    </form>
</div>

<div class="admin-card mb-4">
    <div class="admin-table-wrap">
        <table class="table table-hover align-middle mb-0 admin-responsive-table">
            <thead>
                <tr>
                    <th scope="col" class="py-3 px-4 text-muted small fw-bold">ID</th>
                    <th scope="col" class="py-3 text-muted small fw-bold">Customer</th>
                    <th scope="col" class="py-3 text-muted small fw-bold">Type</th>
                    <th scope="col" class="py-3 text-muted small fw-bold">Route</th>
                    <th scope="col" class="py-3 text-muted small fw-bold">Driver</th>
                    <th scope="col" class="py-3 text-muted small fw-bold">Status</th>
                    <th scope="col" class="py-3 px-4 text-muted small fw-bold text-end">Actions</th>
                </tr>
            </thead>
            <tbody>
                @if(isset($bookings) && count($bookings) > 0)
                    @foreach($bookings as $booking)
                    <tr>
                        <td class="fw-bold px-4" data-label="Booking">{{ $booking->displayReference() }}</td>
                        <td data-label="Customer">{{ $booking->user ? $booking->user->name : 'N/A' }}</td>
                        <td data-label="Type">
                            <span class="badge bg-info bg-opacity-10 text-info px-2 py-1 rounded-pill">
                                {{ ucfirst($booking->service_type) }}
                            </span>
                        </td>
                        <td data-label="Route">
                            <div class="small text-truncate" style="max-width: 180px;" title="{{ $booking->pickup_location }}">
                                <i class="bi bi-geo-alt text-success me-1"></i> {{ $booking->pickup_location }}
                            </div>
                            <div class="small text-truncate text-muted mt-1" style="max-width: 180px;" title="{{ $booking->dropoff_location }}">
                                <i class="bi bi-geo text-danger me-1"></i> {{ $booking->dropoff_location }}
                            </div>
                        </td>
                        <td data-label="Partner">{{ $booking->driver?->name ?? 'Unassigned' }}</td>
                        <td data-label="Status">
                            @php
                                $statusColors = [
                                    'pending' => 'warning',
                                    'accepted' => 'info',
                                    'ongoing' => 'info',
                                    'completed' => 'success',
                                    'cancelled' => 'danger',
                                ];
                                $variant = $statusColors[$booking->status] ?? 'neutral';
                            @endphp
                            @include('partials.ui.status-badge', ['label' => ucfirst($booking->status), 'variant' => $variant])
                        </td>
                        <td class="text-end px-4" data-label="Actions">
                            <a href="{{ route('admin.bookings.show', $booking->id) }}" class="btn btn-sm btn-light border rounded-circle shadow-sm me-1" title="View Details" aria-label="View booking">
                                <i class="bi bi-eye text-primary"></i>
                            </a>
                            <a href="{{ route('admin.bookings.edit', $booking->id) }}" class="btn btn-sm btn-light border rounded-circle shadow-sm me-1" title="Edit Status" aria-label="Edit booking">
                                <i class="bi bi-pencil text-secondary"></i>
                            </a>
                        </td>
                    </tr>
                    @endforeach
                @else
                    <tr>
                        <td colspan="7" class="p-4">
                            @include('partials.ui.empty-state', [
                                'title' => 'No bookings found',
                                'message' => 'No bookings match your current filters.',
                                'icon' => 'bi-inbox',
                            ])
                        </td>
                    </tr>
                @endif
            </tbody>
        </table>
    </div>
    <div class="p-3 border-top">
        {{ $bookings->links() }}
    </div>
</div>
@endsection
