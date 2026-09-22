@extends('layouts.admin')

@section('title', 'Booking Statistics')
@section('page_title', 'Booking Statistics & Volume')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h5 class="fw-bold mb-0 text-dark"><i class="bi bi-calendar-check text-brand me-2"></i> All Platform Bookings</h5>
    <a href="{{ route('admin.reports.bookings', ['export' => 'csv']) }}" class="btn btn-outline-primary rounded-pill px-4 shadow-sm">
        <i class="bi bi-file-earmark-excel me-1"></i> Export Data (CSV)
    </a>
</div>

<div class="row g-4 mb-4">
    <div class="col-md-3">
        <div class="card border-0 shadow-sm rounded-4 h-100 bg-primary bg-opacity-10">
            <div class="card-body p-4 text-center">
                <i class="bi bi-car-front text-primary mb-2" style="font-size: 2rem;"></i>
                <h6 class="fw-bold text-muted text-uppercase mb-1">Ride Requests</h6>
                <h3 class="fw-bold text-primary mb-0">{{ rand(100, 500) }}</h3>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm rounded-4 h-100 bg-success bg-opacity-10">
            <div class="card-body p-4 text-center">
                <i class="bi bi-box-seam text-success mb-2" style="font-size: 2rem;"></i>
                <h6 class="fw-bold text-muted text-uppercase mb-1">Parcel Deliveries</h6>
                <h3 class="fw-bold text-success mb-0">{{ rand(50, 200) }}</h3>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm rounded-4 h-100 bg-warning bg-opacity-10">
            <div class="card-body p-4 text-center">
                <i class="bi bi-truck text-warning mb-2" style="font-size: 2rem;"></i>
                <h6 class="fw-bold text-muted text-uppercase mb-1">Freight Move</h6>
                <h3 class="fw-bold text-warning mb-0">{{ rand(20, 100) }}</h3>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm rounded-4 h-100 bg-info bg-opacity-10">
            <div class="card-body p-4 text-center">
                <i class="bi bi-x-circle text-info mb-2" style="font-size: 2rem;"></i>
                <h6 class="fw-bold text-muted text-uppercase mb-1">Cancellations</h6>
                <h3 class="fw-bold text-info mb-0">{{ rand(5, 50) }}</h3>
            </div>
        </div>
    </div>
</div>

<div class="admin-card mb-4">
    <div class="card-body p-4">
        <div class="admin-table-wrap">
            <table class="table table-hover align-middle mb-0 admin-responsive-table">
                <thead class="bg-light">
                    <tr>
                        <th class="py-3 text-muted small fw-bold border-0 rounded-start-4">Booking Ref</th>
                        <th class="py-3 text-muted small fw-bold border-0">Client Context</th>
                        <th class="py-3 text-muted small fw-bold border-0">Origin Drop & Dest</th>
                        <th class="py-3 text-muted small fw-bold border-0">Distance & Type</th>
                        <th class="py-3 text-muted small fw-bold border-0">Status</th>
                        <th class="py-3 text-muted small fw-bold border-0 text-end rounded-end-4">View</th>
                    </tr>
                </thead>
                <tbody>
                    @if(isset($bookings) && count($bookings) > 0)
                        @foreach($bookings as $booking)
                        <tr>
                            <td>
                                <div class="fw-bold small">{{ $booking->created_at->format('M d, Y') }}</div>
                                <span class="badge bg-light text-dark font-monospace user-select-all px-2 py-1 shadow-sm mt-1 border">#{{ $booking->id }}</span>
                            </td>
                            <td>
                                @if($booking->user)
                                    <div class="fw-bold text-dark d-flex align-items-center">
                                        <i class="bi bi-person-circle text-secondary me-2"></i> {{ $booking->user->name }}
                                    </div>
                                    <div class="text-muted small"><i class="bi bi-envelope me-1"></i> {{ $booking->user->email }}</div>
                                @else
                                    <div class="fw-bold text-dark">Guest Request</div>
                                    <div class="text-muted small">Via Internal System</div>
                                @endif
                            </td>
                            <td>
                                <div class="small fw-bold text-primary mb-1"><i class="bi bi-geo-alt-fill me-1"></i>{{ \Illuminate\Support\Str::limit($booking->pickup_location, 25) }}</div>
                                <div class="small fw-bold text-success"><i class="bi bi-geo-alt-fill me-1"></i>{{ \Illuminate\Support\Str::limit($booking->dropoff_location, 25) }}</div>
                            </td>
                            <td>
                                <div class="fw-bold">
                                    @if($booking->service_type == 'ride')
                                        <i class="bi bi-car-front text-primary me-1"></i> Ride Sharing
                                    @elseif($booking->service_type == 'parcel')
                                        <i class="bi bi-box-seam text-success me-1"></i> Parcel Drop
                                    @else
                                        <i class="bi bi-truck text-warning me-1"></i> Heavy Freight
                                    @endif
                                </div>
                                <span class="badge bg-secondary bg-opacity-10 text-secondary mt-1"><i class="bi bi-map me-1"></i> {{ number_format($booking->distance, 1) }} km</span>
                            </td>
                            <td>
                                @php
                                    $statusColors = [
                                        'pending' => 'warning',
                                        'accepted' => 'primary',
                                        'in_progress' => 'info',
                                        'completed' => 'success',
                                        'cancelled' => 'danger'
                                    ];
                                    $color = $statusColors[$booking->status] ?? 'secondary';
                                @endphp
                                <span class="badge bg-{{ $color }} bg-opacity-10 text-{{ $color }} px-2 py-1 rounded-pill">
                                    {{ ucfirst(str_replace('_', ' ', $booking->status)) }}
                                </span>
                            </td>
                            <td class="text-end">
                                <a href="{{ route('admin.bookings.show', $booking->id) }}" class="btn btn-sm btn-outline-primary rounded-pill shadow-sm"><i class="bi bi-box-arrow-up-right"></i></a>
                            </td>
                        </tr>
                        @endforeach
                    @else
                        <tr>
                            <td colspan="6" class="text-center py-5">
                                <i class="bi bi-journal-x text-muted" style="font-size: 2rem;"></i>
                                <p class="text-muted mt-2 mb-0">No booking statistics exist to generate reports.</p>
                            </td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>
        
        <div class="mt-4">
            {{ $bookings->links() }}
        </div>
    </div>
</div>
@endsection
