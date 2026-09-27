@extends('layouts.admin')

@section('title', 'Booking Details')
@section('page_title', 'View Booking #' . $booking->id)

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h5 class="fw-bold mb-0 text-dark"><i class="bi bi-file-earmark-text text-brand me-2"></i> Booking Details</h5>
    <div class="d-flex gap-2">
        <a href="{{ route('admin.bookings.edit', $booking->id) }}" class="btn btn-warning rounded-pill px-4 shadow-sm text-dark fw-bold">
            <i class="bi bi-pencil me-1"></i> Edit Status
        </a>
        <a href="{{ route('admin.bookings.index') }}" class="btn btn-outline-secondary rounded-pill px-4 shadow-sm">
            <i class="bi bi-arrow-left me-1"></i> Back to List
        </a>
    </div>
</div>

<div class="row g-4 mb-4">
    <div class="col-md-6">
        <div class="card border-0 shadow-sm rounded-4 h-100">
            <div class="card-header bg-white border-0 pt-4 px-4 pb-0">
                <h6 class="fw-bold text-muted mb-0"><i class="bi bi-info-circle me-1"></i> General Information</h6>
            </div>
            <div class="card-body p-4">
                <table class="table table-borderless mb-0">
                    <tbody>
                        <tr>
                            <td class="text-muted" width="40%">Booking ID</td>
                            <td class="fw-bold fs-5">#{{ $booking->id }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Type</td>
                            <td>
                                <span class="badge bg-info bg-opacity-10 text-info px-2 py-1 rounded-pill">
                                    {{ ucfirst($booking->service_type) }}
                                </span>
                            </td>
                        </tr>
                        <tr>
                            <td class="text-muted">Status</td>
                            <td>
                                @php
                                    $statusColors = [
                                        'pending' => 'warning',
                                        'accepted' => 'primary',
                                        'ongoing' => 'info',
                                        'completed' => 'success',
                                        'cancelled' => 'danger',
                                    ];
                                    $color = $statusColors[$booking->status] ?? 'secondary';
                                @endphp
                                <span class="badge bg-{{ $color }} bg-opacity-10 text-{{ $color }} px-3 py-2 rounded-pill shadow-sm">
                                    {{ ucfirst($booking->status) }}
                                </span>
                            </td>
                        </tr>
                        <tr>
                            <td class="text-muted">OTP verification</td>
                            <td>
                                @if($booking->ride_otp_verified_at)
                                    Verified {{ $booking->ride_otp_verified_at->format('M d, Y h:i A') }}
                                @elseif($booking->driver_id)
                                    Waiting for driver to verify the customer OTP
                                @else
                                    Not assigned
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <td class="text-muted">Created At</td>
                            <td>{{ $booking->created_at->format('M d, Y h:i A') }}</td>
                        </tr>
                        @if($booking->service_type === 'parcel' || $booking->service_type === 'freight')
                        <tr>
                            <td class="text-muted">Item Details</td>
                            <td>{{ $booking->parcel_details ?? 'N/A' }}</td>
                        </tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    
    <div class="col-md-6">
        <div class="card border-0 shadow-sm rounded-4 h-100">
            <div class="card-header bg-white border-0 pt-4 px-4 pb-0">
                <h6 class="fw-bold text-muted mb-0"><i class="bi bi-geo-alt me-1"></i> Route Details</h6>
            </div>
            <div class="card-body p-4">
                <div class="position-relative ps-4 ms-2 border-start border-2 border-primary mb-4 pb-2">
                    <span class="position-absolute top-0 start-0 translate-middle p-2 bg-success border border-white border-2 rounded-circle"></span>
                    <h6 class="fw-bold mb-1">Pickup Location</h6>
                    <p class="text-muted mb-0">{{ $booking->pickup_location }}</p>
                </div>
                
                <div class="position-relative ps-4 ms-2 border-start border-2 border-transparent">
                    <span class="position-absolute top-0 start-0 translate-middle p-2 bg-danger border border-white border-2 rounded-circle"></span>
                    <h6 class="fw-bold mb-1">Dropoff Location</h6>
                    <p class="text-muted mb-0">{{ $booking->dropoff_location }}</p>
                </div>
                
                <div class="mt-4 pt-3 border-top border-secondary border-opacity-10">
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="text-muted">Estimated Distance</span>
                        <span class="fw-bold">{{ $booking->distance ?? 'N/A' }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        <div class="card border-0 shadow-sm rounded-4 h-100">
            <div class="card-header bg-white border-0 pt-4 px-4 pb-0">
                <h6 class="fw-bold text-muted mb-0"><i class="bi bi-person me-1"></i> Customer</h6>
            </div>
            <div class="card-body p-4 text-center">
                <div class="bg-light d-inline-block p-3 rounded-circle mb-3">
                    <i class="bi bi-person text-primary" style="font-size: 2rem;"></i>
                </div>
                @if($booking->user)
                    <h5 class="fw-bold mb-1">{{ $booking->user->name }}</h5>
                    <p class="text-muted mb-2"><i class="bi bi-envelope me-1"></i> {{ $booking->user->email }}</p>
                    <p class="text-muted mb-0"><i class="bi bi-telephone me-1"></i> {{ $booking->user->mobile ?? $booking->user->phone ?? 'N/A' }}</p>
                @else
                    <h5 class="fw-bold text-muted">Unknown</h5>
                @endif
            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        <div class="card border-0 shadow-sm rounded-4 h-100">
            <div class="card-header bg-white border-0 pt-4 px-4 pb-0">
                <h6 class="fw-bold text-muted mb-0"><i class="bi bi-person-badge me-1"></i> Assigned Driver</h6>
            </div>
            <div class="card-body p-4 text-center">
                @if($booking->driver)
                    <div class="bg-light d-inline-block p-3 rounded-circle mb-3">
                        <i class="bi bi-person-badge text-info" style="font-size: 2rem;"></i>
                    </div>
                    <h5 class="fw-bold mb-1">{{ $booking->driver->name }}</h5>
                    <p class="text-muted mb-2"><i class="bi bi-envelope me-1"></i> {{ $booking->driver->email }}</p>
                    <p class="text-muted mb-0"><i class="bi bi-telephone me-1"></i> {{ $booking->driver->mobile }}</p>
                @else
                    <div class="bg-light d-inline-block p-3 rounded-circle mb-3 border border-secondary border-dashed">
                        <i class="bi bi-question-lg text-muted" style="font-size: 2rem;"></i>
                    </div>
                    <h5 class="fw-medium text-muted mb-0">No driver assigned yet</h5>
                @endif
            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        <div class="card border-0 shadow-sm rounded-4 bg-primary text-white h-100 relative overflow-hidden">
            <div class="card-body p-4 text-center d-flex flex-column justify-content-center">
                <h6 class="text-white-50 fw-bold mb-3"><i class="bi bi-cash-stack me-1"></i> Total Fare</h6>
                <h2 class="fw-bold mb-4">{{ $default_currency->symbol ?? '₹' }}{{ number_format($booking->fare, 2) ?? '0.00' }}</h2>
                
                <div class="d-flex justify-content-between align-items-center p-3 bg-white bg-opacity-10 rounded-3 text-start">
                    <div>
                        <span class="d-block text-white-50 small">Payment Method</span>
                        <span class="fw-bold">{{ strtoupper($booking->payment_method) }}</span>
                    </div>
                    <div>
                        <span class="d-block text-white-50 small">Payment Status</span>
                        <span class="badge {{ $booking->payment_status == 'completed' ? 'bg-success' : 'bg-warning text-dark' }} mt-1">
                            {{ ucfirst($booking->payment_status) }}
                        </span>
                    </div>
                </div>
            </div>
            <i class="bi bi-wallet2 position-absolute top-0 end-0 opacity-10 m-3" style="font-size: 6rem;"></i>
        </div>
    </div>
</div>
@endsection
