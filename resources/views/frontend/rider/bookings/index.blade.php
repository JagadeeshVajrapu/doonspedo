@extends('layouts.app')

@section('title', 'My Activities - Doonspedo')
@section('body_class', 'rider-shell')

@section('content')
<div class="rider-shell-page">
    <header class="rider-topbar">
        <a href="{{ route('rider.app') }}" class="rider-back-btn" aria-label="Back to booking">
            <i class="bi bi-arrow-left"></i>
        </a>
        <h1>My Activity</h1>
    </header>

    <main class="rider-content">
        <div class="ds-tabs w-100 mb-3" role="tablist" aria-label="Activity filters">
            <button type="button" class="ds-tab is-active flex-grow-1" aria-selected="true">Recent</button>
            <button type="button" class="ds-tab flex-grow-1" aria-selected="false" disabled title="Coming soon">Scheduled</button>
        </div>

        @if($bookings->isEmpty())
            @include('partials.ui.empty-state', [
                'icon' => 'bi-clock-history',
                'title' => 'No rides yet',
                'message' => 'Your booking history will appear here after your first trip.',
                'actionLabel' => 'Book a Ride',
                'actionUrl' => route('rider.app'),
                'classExtra' => 'bg-white',
            ])
        @else
            <div class="booking-list">
                @foreach($bookings as $booking)
                    <article class="rider-card">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <div class="d-flex align-items-center">
                                <div class="ds-stat-icon me-3">
                                    @if($booking->service_type == 'ride')
                                        <i class="bi bi-car-front-fill"></i>
                                    @elseif($booking->service_type == 'parcel')
                                        <i class="bi bi-box-seam-fill"></i>
                                    @else
                                        <i class="bi bi-truck"></i>
                                    @endif
                                </div>
                                <div>
                                    <h2 class="h6 mb-0 fw-bold text-capitalize">{{ $booking->service_type }} Request</h2>
                                    <p class="mb-0 small text-muted">{{ $booking->created_at->format('d M, h:i A') }}</p>
                                </div>
                            </div>
                            <span class="rider-status rider-status-{{ $booking->status }}">{{ ucfirst($booking->status) }}</span>
                        </div>

                        <div class="rider-timeline my-3">
                            <div class="d-flex align-items-center mb-2">
                                <i class="bi bi-circle-fill text-brand x-small me-3" aria-hidden="true"></i>
                                <p class="mb-0 small text-truncate">{{ $booking->pickup_location }}</p>
                            </div>
                            <div class="d-flex align-items-center">
                                <i class="bi bi-geo-alt-fill text-danger x-small me-3" aria-hidden="true"></i>
                                <p class="mb-0 small text-truncate">{{ $booking->dropoff_location }}</p>
                            </div>
                        </div>

                        <div class="d-flex justify-content-between align-items-center pt-2 border-top">
                            <div>
                                <p class="mb-0 x-small text-muted">Fare</p>
                                <p class="mb-0 fw-bold">₹{{ number_format($booking->fare, 2) }}</p>
                            </div>
                            @if($booking->status == 'completed' || $booking->status == 'cancelled')
                                <div class="d-flex gap-2">
                                    <a href="{{ route('rider.bookings.rebook', $booking->id) }}" class="btn btn-sm btn-light border rounded-pill px-3 fw-bold">Rebook</a>
                                    <a href="{{ route('rider.bookings.show', $booking->id) }}" class="btn btn-sm btn-brand rounded-pill px-3 fw-bold">Details</a>
                                </div>
                            @elseif($booking->status == 'pending')
                                <a href="{{ route('rider.app') }}?booking_id={{ $booking->id }}" class="btn btn-sm btn-outline-brand rounded-pill px-3 fw-bold">View Bids</a>
                            @elseif($booking->driver)
                                <div class="d-flex align-items-center">
                                    <div class="text-end me-2">
                                        <p class="mb-0 x-small fw-bold">{{ $booking->driver->name }}</p>
                                        <p class="mb-0 x-small text-muted">{{ $booking->driver->vehicle_number }}</p>
                                    </div>
                                    <img src="{{ $booking->driver->profile_image ? asset('uploads/profiles/' . $booking->driver->profile_image) : 'https://i.pravatar.cc/100?u=' . $booking->driver->id }}" alt="{{ $booking->driver->name }}" class="rounded-circle border" style="width: 36px; height: 36px; object-fit: cover;" loading="lazy">
                                </div>
                            @else
                                <a href="{{ route('rider.bookings.show', $booking->id) }}" class="btn btn-sm btn-brand rounded-pill px-3 fw-bold">Details</a>
                            @endif
                        </div>
                    </article>
                @endforeach
            </div>
        @endif
    </main>

    @include('partials.ui.rider-bottom-nav', ['active' => 'activity'])
</div>
@endsection
