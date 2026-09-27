@extends('layouts.driver')

@section('title', 'Ride Details - Doonspedo')
@section('needs_google_maps')1@endsection

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
        <h1>Ride request #{{ $ride->id }}</h1>
        <p class="text-muted mb-0 small">Review and manage this ride request.</p>
    </div>
    <a href="{{ route('driver.rides.index') }}" class="btn btn-light border rounded-pill px-4 fw-bold">
        <i class="bi bi-arrow-left me-1"></i> Back
    </a>
</div>

<div class="row">
    @if(session('error'))
        <div class="col-12">
            <div class="alert alert-danger">{{ session('error') }}</div>
        </div>
    @endif
    <div class="col-lg-12">
        <div class="drv-card overflow-hidden">
                <div class="row g-0">
                    <!-- Navigation Map -->
                    <div class="col-lg-7 position-relative" style="min-height: 400px;">
                        <div id="map"></div>
                        @if(!$ride->pickup_lat)
                        <div class="position-absolute top-50 start-50 translate-middle text-center w-100 p-4" style="z-index: 1000; background: rgba(255,255,255,0.8);">
                            <i class="bi bi-geo-alt-fill text-danger display-4 mb-2"></i>
                            <h2 class="h6 fw-bold">GPS coordinates missing</h2>
                            <p class="small text-muted mb-0">Navigation requires latitude/longitude data.</p>
                        </div>
                        @endif
                    </div>

                    <!-- Ride Details -->
                    <div class="col-lg-5 p-4">
                        <div class="d-flex justify-content-between align-items-start mb-4">
                            <span class="badge {{ $ride->status == 'completed' ? 'bg-success' : 'bg-brand text-dark' }} rounded-pill px-3 py-2 fw-bold">
                                {{ strtoupper($ride->status) }}
                            </span>
                            <div class="text-end">
                                <div class="small text-muted fw-bold">Estimated fare</div>
                                <div class="h3 fw-bold mb-0 text-dark">₹{{ number_format($ride->fare ?? 0, 2) }}</div>
                            </div>
                        </div>

                        <div class="bg-light p-4 rounded-4 mb-4">
                            <h2 class="h6 fw-bold mb-3 text-uppercase small text-muted">Passenger information</h2>
                            <div class="d-flex align-items-center mb-0">
                                <img src="https://ui-avatars.com/api/?name={{ urlencode($ride->user->name) }}&background=cddc29&color=000" class="rounded-circle me-3" style="width: 50px;" alt="">
                                <div class="flex-grow-1">
                                    <h3 class="h6 fw-bold mb-0">{{ $ride->user->name }}</h3>
                                    <div class="small text-muted">Passenger</div>
                                </div>
                                @if($ride->status != 'completed' && $ride->status != 'cancelled')
                                <div class="d-flex gap-2">
                                    <a href="tel:{{ $ride->user->mobile }}" class="btn btn-outline-dark rounded-circle p-2 d-flex align-items-center justify-content-center shadow-sm" style="width: 40px; height: 40px;" title="Call Customer">
                                        <i class="bi bi-telephone-fill"></i>
                                    </a>
                                    <a href="{{ route('driver.chat.index', $ride->id) }}" class="btn btn-dark rounded-circle p-2 d-flex align-items-center justify-content-center shadow-sm" style="width: 40px; height: 40px;" title="Chat with Customer">
                                        <i class="bi bi-chat-dots-fill"></i>
                                        @php
                                            $unread = \App\Models\ChatMessage::where('booking_id', $ride->id)->where('sender_type', 'user')->where('is_read', false)->count();
                                        @endphp
                                        @if($unread > 0)
                                            <span class="position-absolute translate-middle badge rounded-pill bg-danger" style="margin-top: -15px; margin-left: 20px; font-size: 0.6rem;">
                                                {{ $unread }}
                                            </span>
                                        @endif
                                    </a>
                                </div>
                                @endif
                            </div>
                        </div>

                        <div class="ride-path position-relative ps-4 mb-4 mt-2">
                            <div class="path-line position-absolute start-1 top-0 bottom-0 border-start border-2 border-brand border-dashed" style="left: 6px;"></div>
                            <div class="mb-4">
                                <div class="position-absolute start-0 bg-brand rounded-circle" style="width: 14px; height: 14px; left: 0;"></div>
                                <div class="small text-muted fw-bold text-uppercase mb-1" style="font-size: 0.6rem;">Pickup Address</div>
                                <div class="fw-bold">{{ $ride->pickup_location }}</div>
                            </div>
                            <div>
                                <div class="position-absolute start-0 bg-dark rounded-circle" style="width: 14px; height: 14px; left: 0;"></div>
                                <div class="small text-muted fw-bold text-uppercase mb-1" style="font-size: 0.6rem;">Destination Address</div>
                                <div class="fw-bold">{{ $ride->dropoff_location }}</div>
                            </div>
                        </div>

                        <div class="d-grid gap-2 mt-5">
                            @if($ride->status == 'accepted')
                                <form action="{{ route('driver.rides.pickup', $ride->id) }}" method="POST">
                                    @csrf
                                    <label class="form-label small fw-bold" for="ride-otp">Customer ride OTP</label>
                                    <input id="ride-otp" name="otp" inputmode="numeric" maxlength="6" pattern="[0-9]{6}" class="form-control text-center fw-bold mb-2" placeholder="6-digit OTP" required autocomplete="one-time-code">
                                    @error('otp')
                                        <div class="text-danger small mb-2">{{ $message }}</div>
                                    @enderror
                                    <button type="submit" class="btn btn-dark w-100 py-3 rounded-pill fw-bold shadow" data-loading-label="Verifying OTP...">
                                        Verify OTP and start trip
                                    </button>
                                </form>
                            @elseif($ride->status == 'ongoing')
                                <form action="{{ route('driver.rides.complete', $ride->id) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="btn btn-brand w-100 py-3 rounded-pill fw-bold shadow">
                                        COMPLETE RIDE & COLLECT FARE
                                    </button>
                                </form>
                            @elseif($ride->status == 'pending')
                                <form action="{{ route('driver.rides.accept', $ride->id) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="btn btn-brand w-100 py-3 rounded-pill fw-bold shadow">
                                        ACCEPT THIS RIDE
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>
                </div>
        </div>
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
</style>
@endsection

@section('scripts')
@if(($sys_settings['map_provider'] ?? 'google') == 'osm')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
@endif

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const pickupLat = {{ $ride->pickup_lat ?? 'null' }};
        const pickupLng = {{ $ride->pickup_lng ?? 'null' }};
        const dropoffLat = {{ $ride->dropoff_lat ?? 'null' }};
        const dropoffLng = {{ $ride->dropoff_lng ?? 'null' }};
        const provider = "{{ $sys_settings['map_provider'] ?? 'google' }}";
        const status = "{{ $ride->status }}";
        let map = null;

        if (provider === 'google' && typeof google !== 'undefined') {
            map = new google.maps.Map(document.getElementById('map'), {
                zoom: 13,
                center: { lat: pickupLat || 30.3165, lng: pickupLng || 78.0322 },
                disableDefaultUI: true,
                styles: [
                    { "elementType": "geometry", "stylers": [{ "color": "#f5f5f5" }] },
                    { "featureType": "road", "elementType": "geometry", "stylers": [{ "color": "#ffffff" }] },
                    { "featureType": "water", "elementType": "geometry", "stylers": [{ "color": "#e9e9e9" }] }
                ]
            });
        } else if (typeof L !== 'undefined') {
            const mapCenter = pickupLat && pickupLng ? [pickupLat, pickupLng] : [30.3165, 78.0322];
            map = L.map('map').setView(mapCenter, 13);
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png').addTo(map);
        }

        function drawRoadmap(startLat, startLng, endLat, endLng, startLabel, endLabel) {
            if (provider === 'google' && typeof google !== 'undefined') {
                if (window.directionsRenderer) window.directionsRenderer.setMap(null);
                
                window.directionsRenderer = new google.maps.DirectionsRenderer({
                    map: map,
                    suppressMarkers: false
                });

                const directionsService = new google.maps.DirectionsService();
                directionsService.route({
                    origin: { lat: startLat, lng: startLng },
                    destination: { lat: endLat, lng: endLng },
                    travelMode: google.maps.TravelMode.DRIVING
                }, (response, status) => {
                    if (status === 'OK') {
                        window.directionsRenderer.setDirections(response);
                    }
                });
            } else if (typeof L !== 'undefined') {
                // Clear old layers
                if (window.routePath) map.removeLayer(window.routePath);
                if (window.markerStart) map.removeLayer(window.markerStart);
                if (window.markerEnd) map.removeLayer(window.markerEnd);

                const greenIcon = L.icon({
                    iconUrl: 'https://cdn-icons-png.flaticon.com/512/8065/8065067.png', // Green pin for start
                    iconSize: [35, 35],
                    iconAnchor: [17, 35],
                    popupAnchor: [0, -35]
                });
                const redIcon = L.icon({
                    iconUrl: 'https://cdn-icons-png.flaticon.com/512/8065/8065065.png', // Red pin for destination
                    iconSize: [35, 35],
                    iconAnchor: [17, 35],
                    popupAnchor: [0, -35]
                });

                window.markerStart = L.marker([startLat, startLng], { icon: greenIcon }).addTo(map).bindPopup(startLabel).openPopup();
                window.markerEnd = L.marker([endLat, endLng], { icon: redIcon }).addTo(map).bindPopup(endLabel);

                // Fetch route using OSRM
                fetch(`https://router.project-osrm.org/route/v1/driving/${startLng},${startLat};${endLng},${endLat}?overview=full&geometries=geojson`)
                .then(res => res.json())
                .then(data => {
                    if (data.routes && data.routes.length > 0) {
                        const coordinates = data.routes[0].geometry.coordinates.map(c => [c[1], c[0]]);
                        window.routePath = L.polyline(coordinates, {color: '#cddc29', weight: 6, opacity: 0.9}).addTo(map);
                        map.fitBounds(window.routePath.getBounds().pad(0.1));
                    } else {
                        window.routePath = L.polyline([[startLat, startLng], [endLat, endLng]], {color: '#cddc29', weight: 4, dashArray: '5,5'}).addTo(map);
                        const group = new L.featureGroup([window.markerStart, window.markerEnd]);
                        map.fitBounds(group.getBounds().pad(0.1));
                    }
                })
                .catch(err => {
                    console.warn("OSRM error, drawing straight polyline fallback", err);
                    window.routePath = L.polyline([[startLat, startLng], [endLat, endLng]], {color: '#cddc29', weight: 4, dashArray: '5,5'}).addTo(map);
                    const group = new L.featureGroup([window.markerStart, window.markerEnd]);
                    map.fitBounds(group.getBounds().pad(0.1));
                });
            }
        }

        // Location reporter and dynamic route updater
        if (navigator.geolocation) {
            let initialRouteDrawn = false;

            function processPosition(position) {
                const lat = position.coords.latitude;
                const lng = position.coords.longitude;

                // 1. Report location to database for rider tracking
                fetch("{{ route('driver.updateLocation') }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ latitude: lat, longitude: lng })
                })
                .then(res => res.json())
                .then(data => console.log("Live location synced:", lat, lng))
                .catch(err => console.error("Sync error:", err));

                // 2. Draw roadmap dynamically based on status
                if (!initialRouteDrawn) {
                    if (status === 'accepted') {
                        // Accepted: Show road route from Driver's Live Location -> Passenger Pickup
                        drawRoadmap(lat, lng, pickupLat, pickupLng, "My Location (Driver)", "Passenger Pickup");
                    } else if (status === 'ongoing') {
                        // Ongoing: Show road route from Driver's Live Location -> Passenger Dropoff
                        drawRoadmap(lat, lng, dropoffLat, dropoffLng, "Current Position", "Dropoff Destination");
                    } else {
                        // Pre-accept / pending / completed states: show static Pickup -> Dropoff route
                        if (pickupLat && pickupLng && dropoffLat && dropoffLng) {
                            drawRoadmap(pickupLat, pickupLng, dropoffLat, dropoffLng, "Pickup Point", "Destination");
                        }
                    }
                    initialRouteDrawn = true;
                }
            }

            // Get current location immediately to draw correct route
            navigator.geolocation.getCurrentPosition(processPosition, err => {
                console.warn("High-accuracy location fail, fallback to static", err);
                if (pickupLat && pickupLng && dropoffLat && dropoffLng) {
                    drawRoadmap(pickupLat, pickupLng, dropoffLat, dropoffLng, "Pickup Point", "Destination");
                }
            }, { enableHighAccuracy: true });

            // Watch position continuously for live database tracking as driver moves
            navigator.geolocation.watchPosition(processPosition, err => console.warn(err), {
                enableHighAccuracy: true,
                maximumAge: 10000,
                timeout: 10000
            });
        } else {
            // No Geolocation support: show static route
            if (pickupLat && pickupLng && dropoffLat && dropoffLng) {
                drawRoadmap(pickupLat, pickupLng, dropoffLat, dropoffLng, "Pickup Point", "Destination");
            }
        }
    });
</script>
@endsection
