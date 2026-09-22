@extends('layouts.admin')

@section('title', 'Global Search')
@section('page_title', 'Search Results')

@section('content')
<div class="mb-4">
    <h5 class="fw-bold">Results for: <span class="text-brand">"{{ $q }}"</span></h5>
</div>

<div class="row g-4">
    <!-- Drivers Results -->
    <div class="col-12">
        <div class="admin-card">
            <div class="card-header bg-white border-0 p-4">
                <h6 class="fw-bold mb-0"><i class="bi bi-person-badge text-brand me-2"></i> Partners (Drivers)</h6>
            </div>
            <div class="card-body p-0">
                <div class="admin-table-wrap">
                    <table class="table table-hover align-middle mb-0 admin-responsive-table">
                        <thead class="bg-light">
                            <tr>
                                <th class="py-3 px-4">Name</th>
                                <th class="py-3">Contact</th>
                                <th class="py-3">Vehicle</th>
                                <th class="py-3">Status</th>
                                <th class="py-3 text-end px-4">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($drivers as $driver)
                            <tr>
                                <td class="px-4 fw-bold text-dark">{{ $driver->name }}</td>
                                <td>{{ $driver->mobile }}<br><small class="text-muted">{{ $driver->email }}</small></td>
                                <td>{{ $driver->vehicle_number }}</td>
                                <td><span class="badge bg-{{ $driver->status == 'approved' ? 'success' : 'warning' }} bg-opacity-10 text-{{ $driver->status == 'approved' ? 'success' : 'warning' }} rounded-pill px-3 py-1">{{ ucfirst($driver->status) }}</span></td>
                                <td class="text-end px-4">
                                    <a href="{{ route('admin.drivers.view', $driver->id) }}" class="btn btn-sm btn-light rounded-pill px-3">View Details</a>
                                </td>
                            </tr>
                            @empty
                            <tr><td colspan="5" class="text-center py-4 text-muted">No partners found matching "{{ $q }}"</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Users Results -->
    <div class="col-md-6">
        <div class="admin-card">
            <div class="card-header bg-white border-0 p-4">
                <h6 class="fw-bold mb-0"><i class="bi bi-person-check text-brand me-2"></i> Customers (Users)</h6>
            </div>
            <div class="card-body p-0">
                <div class="admin-table-wrap">
                    <table class="table table-hover align-middle mb-0 admin-responsive-table">
                        <thead class="bg-light">
                            <tr>
                                <th class="py-3 px-4">Name</th>
                                <th class="py-3">Contact</th>
                                <th class="py-3 text-end px-4">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($users as $user)
                            <tr>
                                <td class="px-4 fw-bold text-dark">{{ $user->name }}</td>
                                <td>{{ $user->mobile }}</td>
                                <td class="text-end px-4">
                                    <a href="{{ route('admin.users.view', $user->id) }}" class="btn btn-sm btn-light rounded-pill px-3">View</a>
                                </td>
                            </tr>
                            @empty
                            <tr><td colspan="3" class="text-center py-4 text-muted">No users found</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Bookings Results -->
    <div class="col-md-6">
        <div class="admin-card">
            <div class="card-header bg-white border-0 p-4">
                <h6 class="fw-bold mb-0"><i class="bi bi-calendar-check text-brand me-2"></i> Bookings</h6>
            </div>
            <div class="card-body p-0">
                <div class="admin-table-wrap">
                    <table class="table table-hover align-middle mb-0 admin-responsive-table">
                        <thead class="bg-light">
                            <tr>
                                <th class="py-3 px-4">Booking ID</th>
                                <th class="py-3">Location</th>
                                <th class="py-3 text-end px-4">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($bookings as $booking)
                            <tr>
                                <td class="px-4 fw-bold text-dark">#{{ $booking->id }}</td>
                                <td class="small">{{ \Illuminate\Support\Str::limit($booking->pickup_location, 30) }}</td>
                                <td class="text-end px-4">
                                    <a href="{{ route('admin.bookings.show', $booking->id) }}" class="btn btn-sm btn-light rounded-pill px-3">Details</a>
                                </td>
                            </tr>
                            @empty
                            <tr><td colspan="3" class="text-center py-4 text-muted">No bookings found</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
