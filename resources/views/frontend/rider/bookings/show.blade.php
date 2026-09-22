@extends('layouts.app')

@section('title', 'Booking Details - Doonspedo')
@section('body_class', 'rider-shell')

@section('content')
<div class="rider-shell-page">
    <header class="rider-topbar">
        <a href="{{ route('rider.bookings.index') }}" class="rider-back-btn" aria-label="Back to activity">
            <i class="bi bi-arrow-left"></i>
        </a>
        <h1>Ride Details</h1>
    </header>

    <main class="rider-content">
        @php
            $statusIcons = [
                'pending' => 'bi-hourglass-split',
                'accepted' => 'bi-check2-circle',
                'ongoing' => 'bi-geo-alt',
                'completed' => 'bi-clipboard-check',
                'cancelled' => 'bi-x-circle'
            ];
        @endphp

        <div class="rider-card text-center py-4">
            <i class="bi {{ $statusIcons[$booking->status] ?? 'bi-info-circle' }} display-4 mb-2 text-brand" aria-hidden="true"></i>
            <h2 class="h4 fw-bold mb-1 text-capitalize">{{ $booking->status }}</h2>
            <p class="text-muted small mb-2">{{ $booking->created_at->format('l, d M Y at h:i A') }}</p>
            <span class="rider-status rider-status-{{ $booking->status }}">{{ ucfirst($booking->status) }}</span>
        </div>

        <div class="rider-card">
            <p class="x-small text-muted fw-bold text-uppercase ls-1 mb-3">Route</p>
            <div class="rider-timeline">
                <div class="d-flex align-items-start mb-3">
                    <span class="bg-brand rounded-circle me-3 flex-shrink-0 mt-1" style="width: 12px; height: 12px;" aria-hidden="true"></span>
                    <div>
                        <p class="text-muted x-small fw-bold mb-0">PICKUP</p>
                        <p class="mb-0 small fw-semibold">{{ $booking->pickup_location }}</p>
                    </div>
                </div>
                <div class="d-flex align-items-start">
                    <span class="bg-danger rounded-circle me-3 flex-shrink-0 mt-1" style="width: 12px; height: 12px;" aria-hidden="true"></span>
                    <div>
                        <p class="text-muted x-small fw-bold mb-0">DROPOFF</p>
                        <p class="mb-0 small fw-semibold">{{ $booking->dropoff_location }}</p>
                    </div>
                </div>
            </div>
        </div>

        @if($booking->driver)
        <div class="rider-card">
            <p class="x-small text-muted fw-bold text-uppercase ls-1 mb-3">Driver &amp; Vehicle</p>
            <div class="d-flex align-items-center">
                <img src="{{ $booking->driver->profile_image ? asset('uploads/profiles/' . $booking->driver->profile_image) : 'https://i.pravatar.cc/100?u=' . $booking->driver->id }}" alt="{{ $booking->driver->name }}" class="rounded-circle me-3 border" style="width: 52px; height: 52px; object-fit: cover;" loading="lazy">
                <div>
                    <h3 class="h6 mb-0 fw-bold">{{ $booking->driver->name }}</h3>
                    <p class="mb-0 small text-muted">
                        <i class="bi bi-star-fill text-brand" aria-hidden="true"></i> {{ $booking->driver->vehicle_number }}
                    </p>
                </div>
                <div class="ms-auto">
                    <a href="tel:{{ $booking->driver->mobile }}" class="btn btn-light border rounded-circle p-0 d-flex align-items-center justify-content-center" style="width: 42px; height: 42px;" aria-label="Call driver">
                        <i class="bi bi-telephone-fill text-brand"></i>
                    </a>
                </div>
            </div>
        </div>
        @endif

        <div class="rider-card">
            <p class="x-small text-muted fw-bold text-uppercase ls-1 mb-3">Fare Breakdown</p>
            <div class="d-flex justify-content-between mb-2">
                <span class="text-muted small">Base Fare</span>
                <span class="fw-bold">₹{{ number_format($booking->fare * 0.8, 2) }}</span>
            </div>
            <div class="d-flex justify-content-between mb-2">
                <span class="text-muted small">Taxes &amp; Fees (18% GST)</span>
                <span class="fw-bold">₹{{ number_format($booking->fare * 0.18, 2) }}</span>
            </div>
            <div class="d-flex justify-content-between mb-2">
                <span class="text-muted small">Service Fee</span>
                <span class="fw-bold">₹{{ number_format($booking->fare * 0.02, 2) }}</span>
            </div>
            <hr class="my-3">
            <div class="d-flex justify-content-between align-items-center">
                <h3 class="h6 mb-0 fw-bold">Total Fare</h3>
                <p class="h5 mb-0 fw-bold text-brand">₹{{ number_format($booking->fare, 2) }}</p>
            </div>
            <p class="x-small text-muted mt-2 mb-0">Paid via {{ ucfirst($booking->payment_method) }}</p>
        </div>

        <div class="d-flex gap-2">
            @if($booking->status == 'completed')
                <a href="{{ route('rider.bookings.invoice', $booking->id) }}" target="_blank" class="btn btn-light border flex-grow-1 py-3 rounded-4 fw-bold">
                    <i class="bi bi-download me-2"></i> Invoice
                </a>
            @endif
            <a href="{{ route('rider.bookings.rebook', $booking->id) }}" class="btn btn-brand {{ $booking->status == 'completed' ? 'px-4' : 'w-100' }} py-3 rounded-4 fw-bold">
                <i class="bi bi-arrow-repeat me-2"></i> Rebook
            </a>
        </div>
    </main>

    @include('partials.ui.rider-bottom-nav', ['active' => 'activity'])
</div>
@endsection
