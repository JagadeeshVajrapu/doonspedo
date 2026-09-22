@extends('layouts.driver')

@section('title', 'Active Rides - Doonspedo')

@section('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />
<style>
    #map { height: 100%; width: 100%; min-height: 400px; border-radius: 0 0 0 0; }
    .leaflet-container { font-family: 'Outfit', sans-serif; }
</style>
@endsection

@section('content')
<div class="drv-page-header">
    <div>
        <h1>Active ride</h1>
        <p class="text-muted mb-0 small">Manage your current ongoing trip.</p>
    </div>
</div>

@if(session('success'))
    @include('partials.ui.alert', ['variant' => 'success', 'message' => session('success')])
@endif

@if(session('error'))
    @include('partials.ui.alert', ['variant' => 'danger', 'message' => session('error')])
@endif

<div class="row">
    <div class="col-lg-12">
        @if($activeRide)
            <div class="drv-card overflow-hidden">
                <div class="row g-0">
                    <!-- Navigation Map -->
                    <div class="col-lg-7 position-relative" style="min-height: 400px;">
                        <div id="map"></div>
                        @if(!$activeRide->pickup_lat)
                        <div class="position-absolute top-50 start-50 translate-middle text-center w-100 p-4" style="z-index: 1000; background: rgba(255,255,255,0.8);">
                            <i class="bi bi-geo-alt-fill text-danger display-4 mb-2"></i>
                            <h2 class="h6 fw-bold">GPS coordinates missing</h2>
                            <p class="small text-muted mb-0">Navigation requires latitude/longitude data.</p>
                        </div>
                        @endif
                    </div>

                    <!-- Ride Details: Status → Customer → Pickup → Destination → Fare → Action -->
                    <div class="col-lg-5 p-4 d-flex flex-column justify-content-between">
                        <div>
                            <div class="mb-3">
                                <div class="small text-muted text-uppercase fw-bold mb-2">Ride status</div>
                                <div class="d-flex flex-wrap align-items-center gap-2">
                                    <span class="badge {{ $activeRide->status == 'ongoing' ? 'bg-success' : 'bg-brand text-dark' }} rounded-pill px-3 py-2 fw-bold">
                                        <i class="bi bi-record-circle-fill me-1"></i> {{ strtoupper($activeRide->status) }}
                                    </span>
                                    <span class="badge bg-dark rounded-pill px-3 py-2 fw-bold">
                                        {{ strtoupper($activeRide->service_type) }}
                                    </span>
                                </div>
                            </div>

                            <div class="bg-light p-3 rounded-4 mb-3">
                                <div class="small text-muted text-uppercase fw-bold mb-2">Customer</div>
                                <div class="d-flex align-items-center">
                                    <img src="https://ui-avatars.com/api/?name={{ urlencode($activeRide->user->name) }}&background=cddc29&color=000" class="rounded-circle me-3" style="width: 50px;" alt="">
                                    <div>
                                        <h2 class="h6 fw-bold mb-0">{{ $activeRide->user->name }}</h2>
                                        <div class="small text-muted">Passenger</div>
                                    </div>
                                    <div class="ms-auto">
                                        <a href="tel:{{ $activeRide->user->phone ?? '#' }}" class="btn btn-brand rounded-circle p-2 shadow-sm" aria-label="Call passenger">
                                            <i class="bi bi-telephone-fill"></i>
                                        </a>
                                    </div>
                                </div>
                            </div>

                            <div class="ride-path position-relative ps-4 mb-3">
                                <div class="path-line position-absolute start-1 top-0 bottom-0 border-start border-2 border-brand border-dashed" style="left: 6px;"></div>
                                <div class="mb-4">
                                    <div class="position-absolute start-0 bg-brand rounded-circle" style="width: 14px; height: 14px; left: 0;"></div>
                                    <div class="small text-muted fw-bold text-uppercase mb-1" style="font-size: 0.6rem;">Pickup</div>
                                    <div class="fw-bold small">{{ $activeRide->pickup_location }}</div>
                                </div>
                                <div>
                                    <div class="position-absolute start-0 bg-dark rounded-circle" style="width: 14px; height: 14px; left: 0;"></div>
                                    <div class="small text-muted fw-bold text-uppercase mb-1" style="font-size: 0.6rem;">Destination</div>
                                    <div class="fw-bold small">{{ $activeRide->dropoff_location }}</div>
                                </div>
                            </div>

                            <div class="d-flex justify-content-between align-items-center border rounded-4 p-3 mb-3">
                                <div class="small text-muted fw-bold text-uppercase">Est. fare</div>
                                <div class="h4 fw-bold mb-0 text-dark">₹{{ number_format($activeRide->fare ?? 0, 2) }}</div>
                            </div>
                        </div>

                        <div class="d-grid gap-2">
                            <div class="small text-muted text-uppercase fw-bold">Current action</div>
                            @if($activeRide->status == 'accepted')
                                <form action="{{ route('driver.rides.pickup', $activeRide->id) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="btn btn-dark w-100 py-3 rounded-pill fw-bold shadow active-scale">
                                        I have arrived (pickup)
                                    </button>
                                </form>
                            @elseif($activeRide->status == 'ongoing')
                                <form action="{{ route('driver.rides.complete', $activeRide->id) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="btn btn-brand w-100 py-3 rounded-pill fw-bold shadow active-scale">
                                        Complete trip & collect fare
                                    </button>
                                </form>
                            @endif
                            <a href="{{ route('driver.support.create') }}" class="btn btn-link text-muted btn-sm text-decoration-none">Report issue</a>
                        </div>
                    </div>
                </div>
            </div>
        @else
            <div class="drv-card p-4">
                @include('partials.ui.empty-state', [
                    'title' => 'No active ride',
                    'message' => "You don't have any ongoing rides at the moment. Stay online to receive requests.",
                    'icon' => 'bi-map',
                    'actionLabel' => 'View dashboard',
                    'actionUrl' => route('driver.dashboard'),
                ])
            </div>
        @endif
    </div>
</div>

<style>
.bg-brand {
    background-color: #cddc29 !important;
}
.text-brand {
    color: #cddc29 !important;
}
.btn-brand {
    background-color: #cddc29;
    color: #000;
}
.btn-brand:hover {
    background-color: #b9c825;
}
.border-dashed {
    border-style: dashed !important;
}
.active-scale:active {
    transform: scale(0.98);
}
</style>
@section('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
@if($activeRide)
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const pickupLat = {{ $activeRide->pickup_lat ?? 'null' }};
        const pickupLng = {{ $activeRide->pickup_lng ?? 'null' }};
        const dropoffLat = {{ $activeRide->dropoff_lat ?? 'null' }};
        const dropoffLng = {{ $activeRide->dropoff_lng ?? 'null' }};

        const mapCenter = pickupLat && pickupLng ? [pickupLat, pickupLng] : [30.3165, 78.0322];
        const map = L.map('map').setView(mapCenter, 14);

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
        }).addTo(map);

        if (pickupLat && pickupLng) {
            const pickupMarker = L.marker([pickupLat, pickupLng]).addTo(map)
                .bindPopup('<b>Pickup:</b><br>{{ $activeRide->pickup_location }}').openPopup();
            
            if (dropoffLat && dropoffLng) {
                const dropoffMarker = L.marker([dropoffLat, dropoffLng]).addTo(map)
                    .bindPopup('<b>Dropoff:</b><br>{{ $activeRide->dropoff_location }}');
                
                const latlngs = [[pickupLat, pickupLng], [dropoffLat, dropoffLng]];
                L.polyline(latlngs, {color: '#1faf4b', weight: 5, opacity: 0.7}).addTo(map);
                
                const group = new L.featureGroup([pickupMarker, dropoffMarker]);
                map.fitBounds(group.getBounds().pad(0.1));
            }
        }
    });
</script>
@endif
@endsection
@endsection
