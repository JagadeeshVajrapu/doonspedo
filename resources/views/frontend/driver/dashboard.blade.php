@extends('layouts.driver')

@section('title', 'Driver Dashboard - Doonspedo')

@section('content')
@php
    $isOnline = (bool) ($driver->is_online ?? false);
    $currencySymbol = $driverCurrency->symbol ?? '₹';
    $rate = $driverCurrency->exchange_rate ?? 1.0;
@endphp

<div class="drv-page-header">
    <div>
        <h1>Welcome, {{ $driver->name }}</h1>
        <p class="text-muted mb-0 small">Your command center for today's rides.</p>
    </div>
    <div class="d-none d-md-flex flex-column align-items-end">
        <div class="fw-bold fs-5">{{ $currencySymbol }}{{ number_format(($stats['today_earnings'] ?? 0) * $rate, 2) }}</div>
        <div class="small text-success fw-bold">Today's earnings</div>
    </div>
</div>

@if(session('success'))
    @include('partials.ui.alert', ['variant' => 'success', 'message' => session('success')])
@endif
@if(session('error'))
    @include('partials.ui.alert', ['variant' => 'danger', 'message' => session('error')])
    @if(session('needs_wallet'))
        <a href="{{ route('driver.wallet.add') }}" class="btn btn-dark rounded-pill mb-3">Add money</a>
    @endif
@endif

@if($driver->status == 'pending')
<div class="drv-card mb-4 p-4 d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 border-start border-4 border-warning">
    <div class="d-flex align-items-center gap-3">
        <div class="rounded-circle bg-warning bg-opacity-10 p-3">
            <i class="bi bi-hourglass-split text-warning fs-4"></i>
        </div>
        <div>
            <h2 class="h5 fw-bold mb-1">Account under review</h2>
            <p class="mb-0 small text-muted">Your KYC documents are being verified by our team.</p>
        </div>
    </div>
    <a href="{{ route('driver.kyc') }}" class="btn btn-dark rounded-pill px-4 fw-bold">
        <i class="bi bi-upload me-1"></i> Update documents
    </a>
</div>
@elseif($driver->status == 'rejected')
<div class="drv-card mb-4 p-4 d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 border-start border-4 border-danger">
    <div class="d-flex align-items-center gap-3">
        <div class="rounded-circle bg-danger bg-opacity-10 p-3">
            <i class="bi bi-x-circle text-danger fs-4"></i>
        </div>
        <div>
            <h2 class="h5 fw-bold mb-1">Account rejected</h2>
            <p class="mb-0 small text-muted">Please re-upload your documents or contact support.</p>
        </div>
    </div>
    <a href="{{ route('driver.kyc') }}" class="btn btn-danger rounded-pill px-4 fw-bold">
        <i class="bi bi-arrow-repeat me-1"></i> Fix documents
    </a>
</div>
@endif

{{-- STAT CARDS (existing metrics only) --}}
<div class="row g-3 mb-4">
    <div class="col-6 col-lg-3">
        <div class="drv-stat">
            <div class="drv-stat-label">Today's earnings</div>
            <div class="drv-stat-value">{{ $currencySymbol }}{{ number_format(($stats['today_earnings'] ?? 0) * $rate, 0) }}</div>
            <div class="small text-success fw-semibold mt-1"><i class="bi bi-graph-up me-1"></i>Today</div>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="drv-stat">
            <div class="drv-stat-label">Total rides</div>
            <div class="drv-stat-value">{{ $stats['total_rides'] }}</div>
            <div class="small text-muted mt-1">Completed trips</div>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="drv-stat">
            <div class="drv-stat-label">Rating</div>
            <div class="drv-stat-value">{{ number_format($stats['rating'], 1) }}</div>
            <div class="small text-muted mt-1">Average rating</div>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <a href="{{ route('driver.bids.index') }}" class="text-decoration-none d-block h-100">
            <div class="drv-stat">
                <div class="drv-stat-label">Total bids</div>
                <div class="drv-stat-value text-dark">{{ $stats['total_bids'] }}</div>
                <div class="small text-muted mt-1">My active offers</div>
            </div>
        </a>
    </div>
</div>

{{-- QUICK ACTIONS --}}
<div class="drv-quick-actions mb-4">
    <a href="{{ route('driver.bids.index') }}" class="drv-quick-action">
        <i class="bi bi-lightning-charge"></i>
        Ride requests
    </a>
    <a href="{{ route('driver.earnings') }}" class="drv-quick-action">
        <i class="bi bi-graph-up-arrow"></i>
        Earnings
    </a>
    <a href="{{ route('driver.wallet') }}" class="drv-quick-action">
        <i class="bi bi-wallet2"></i>
        Wallet
    </a>
    <a href="{{ route('driver.rides.index') }}" class="drv-quick-action">
        <i class="bi bi-map"></i>
        Active rides
    </a>
</div>

<div class="row g-4">
    <div class="col-lg-8">
        <div class="drv-card h-100">
            <div class="d-flex justify-content-between align-items-center px-4 py-3 border-bottom">
                <h2 class="h5 fw-bold mb-0">Live status</h2>
                <div id="status-badge" class="badge {{ $isOnline ? 'bg-success' : 'bg-secondary' }} rounded-pill px-3 py-2" role="status" aria-live="polite">
                    {{ $isOnline ? 'ONLINE' : 'OFFLINE' }}
                </div>
            </div>
            <div class="drv-online-panel {{ $isOnline ? 'is-online' : 'is-offline' }} card-body">
                <div class="mb-4">
                    <div class="online-indicator position-relative d-inline-block">
                        <i id="car-icon" class="bi bi-car-front {{ $isOnline ? 'text-brand' : 'text-light' }} display-1" style="transition: color 0.3s ease;" aria-hidden="true"></i>
                        <span id="dot-indicator" class="position-absolute bottom-0 end-0 p-2 {{ $isOnline ? 'bg-success' : 'bg-secondary' }} border border-3 border-white rounded-circle shadow-sm" aria-hidden="true"></span>
                    </div>
                </div>
                <h3 id="status-text" class="fw-bold text-dark h4">{{ $isOnline ? 'Currently Online' : 'You are currently offline' }}</h3>
                <p id="sub-text" class="text-secondary opacity-75 mx-auto mb-4" style="max-width: 400px;">
                    {{ $isOnline ? 'Waiting for ride requests around your area...' : 'Change your status to online to start receiving ride requests from nearby customers.' }}
                </p>

                @if($driver->status == 'approved')
                <button type="button" id="toggle-online" class="btn {{ $isOnline ? 'btn-danger' : 'btn-brand' }} px-5 py-3 rounded-pill fw-bold shadow active-scale" aria-pressed="{{ $isOnline ? 'true' : 'false' }}" aria-describedby="status-text">
                    <i class="bi bi-power me-2" aria-hidden="true"></i> {{ $isOnline ? 'GO OFFLINE' : 'GO ONLINE NOW' }}
                </button>
                @else
                <div class="mt-2 p-3 bg-light rounded-4 d-inline-block">
                    <i class="bi bi-lock-fill text-muted me-2"></i>
                    <span class="small text-muted fw-bold">Online mode disabled until KYC approval</span>
                </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Ride Requests Modal / Container -->
    <div id="new-requests-container" class="position-fixed p-2 p-sm-3" style="z-index: 2000; top: 0.5rem; right: 0.5rem; left: auto; width: min(22rem, calc(100vw - 1rem)); pointer-events: none;">
        <!-- Requests will be injected here via JS -->
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const toggleBtn = document.getElementById('toggle-online');
        const statusBadge = document.getElementById('status-badge');
        const carIcon = document.getElementById('car-icon');
        const dotIndicator = document.getElementById('dot-indicator');
        const statusText = document.getElementById('status-text');
        const subText = document.getElementById('sub-text');

        if(toggleBtn) {
            toggleBtn.addEventListener('click', function() {
                toggleBtn.disabled = true;
                toggleBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span> Updating...';

                fetch('{{ route("driver.toggleStatus") }}', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Content-Type': 'application/json'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    toggleBtn.disabled = false;
                    if(data.success) {
                        const isOnline = data.is_online;
                        
                        // Update UI
                        statusBadge.className = `badge ${isOnline ? 'bg-success' : 'bg-secondary'} rounded-pill px-3 py-2`;
                        statusBadge.innerText = isOnline ? 'ONLINE' : 'OFFLINE';
                        
                        carIcon.className = `bi bi-car-front ${isOnline ? 'text-brand' : 'text-light'} display-1`;
                        dotIndicator.className = `position-absolute bottom-0 end-0 p-2 ${isOnline ? 'bg-success' : 'bg-secondary'} border border-3 border-white rounded-circle shadow-sm`;
                        
                        statusText.innerText = isOnline ? 'Currently Online' : 'You are currently offline';
                        subText.innerText = isOnline ? 'Waiting for ride requests around your area...' : 'Change your status to online to start receiving ride requests from nearby customers.';
                        
                        toggleBtn.className = `btn ${isOnline ? 'btn-danger' : 'btn-brand'} px-5 py-3 rounded-pill fw-bold shadow active-scale`;
                        toggleBtn.innerHTML = `<i class="bi bi-power me-2" aria-hidden="true"></i> ${isOnline ? 'GO OFFLINE' : 'GO ONLINE NOW'}`;
                        toggleBtn.setAttribute('aria-pressed', isOnline ? 'true' : 'false');

                        if(isOnline) startPolling();
                        else stopPolling();
                    } else {
                        alert(data.message);
                        toggleBtn.innerHTML = '<i class="bi bi-power me-2"></i> GO ONLINE NOW';
                    }
                })
                .catch(err => {
                    toggleBtn.disabled = false;
                    const currentlyOnline = statusBadge && statusBadge.innerText.trim() === 'ONLINE';
                    toggleBtn.innerHTML = currentlyOnline
                        ? '<i class="bi bi-power me-2" aria-hidden="true"></i> GO OFFLINE'
                        : '<i class="bi bi-power me-2" aria-hidden="true"></i> GO ONLINE NOW';
                    alert('Action failed. Try again.');
                });
            });
        }

        let pollInterval;
        let lastAlertedRequestId = null;
        function startPolling() {
            if(pollInterval) return;
            checkRequests();
            pollInterval = setInterval(checkRequests, 5000); // Check every 5 seconds
        }

        function stopPolling() {
            clearInterval(pollInterval);
            pollInterval = null;
            lastAlertedRequestId = null;
            const box = document.getElementById('new-requests-container');
            if (box) box.innerHTML = '';
        }

        function checkRequests() {
            fetch('{{ route("driver.rides.requests") }}', { headers: { 'Accept': 'application/json' } })
            .then(res => res.json())
            .then(data => {
                if(data.success && data.requests && data.requests.length > 0) {
                    const latest = data.requests[0];
                    if (latest.id !== lastAlertedRequestId) {
                        lastAlertedRequestId = latest.id;
                        showRequestAlert(latest);
                    }
                }
            })
            .catch(() => {});
        }

        // Synthesize a loud "Ding-Dong!" taxi bell ringtone natively with zero network delay
        function playBookingBell() {
            try {
                const AudioContext = window.AudioContext || window.webkitAudioContext;
                if (!AudioContext) return;
                const ctx = new AudioContext();
                
                let time = ctx.currentTime;
                // Ring the bell 3 times
                for (let i = 0; i < 3; i++) {
                    // High chime (A5 note)
                    let osc1 = ctx.createOscillator();
                    let gain1 = ctx.createGain();
                    osc1.type = 'sine';
                    osc1.frequency.setValueAtTime(880, time);
                    gain1.gain.setValueAtTime(0.4, time);
                    gain1.gain.exponentialRampToValueAtTime(0.01, time + 0.35);
                    osc1.connect(gain1);
                    gain1.connect(ctx.destination);
                    osc1.start(time);
                    osc1.stop(time + 0.35);

                    // Dual chime (C6 note) slightly offset
                    let osc2 = ctx.createOscillator();
                    let gain2 = ctx.createGain();
                    osc2.type = 'sine';
                    osc2.frequency.setValueAtTime(1046.5, time + 0.12);
                    gain2.gain.setValueAtTime(0.4, time + 0.12);
                    gain2.gain.exponentialRampToValueAtTime(0.01, time + 0.5);
                    osc2.connect(gain2);
                    gain2.connect(ctx.destination);
                    osc2.start(time + 0.12);
                    osc2.stop(time + 0.5);
                    
                    time += 0.8; // Repeat interval
                }
            } catch(e) {
                console.warn("Audio feedback block or unsupported browser:", e);
            }
        }

        // Distinct dual-pulse phone hardware vibration
        function triggerVibration() {
            if (navigator.vibrate) {
                // 500ms vibrate, 250ms pause, 500ms vibrate, 250ms pause, 500ms vibrate
                navigator.vibrate([500, 250, 500, 250, 500]);
            }
        }

        function showRequestAlert(request) {
            if(document.getElementById('ride-alert-' + request.id)) return;

            // Trigger bell and vibration
            playBookingBell();
            triggerVibration();

            const container = document.getElementById('new-requests-container');
            
            // Service type styling
            let typeLabel = "NEW RIDE REQUEST";
            let typeIcon = "bi-person-circle";
            let typeColor = "#cddc29"; // Default brand color
            let typeTextColor = "text-dark";

            if(request.service_type === 'parcel') {
                typeLabel = "PARCEL DELIVERY";
                typeIcon = "bi-box-seam-fill";
                typeColor = "#36b9cc"; // Info blue
                typeTextColor = "text-white";
            } else if(request.service_type === 'freight') {
                typeLabel = "FREIGHT JOB";
                typeIcon = "bi-truck";
                typeColor = "#f6c23e"; // Warning yellow
                typeTextColor = "text-dark";
            } else if(request.service_type === 'rental') {
                typeLabel = "RENTAL BOOKING";
                typeIcon = "bi-calendar-event";
                typeColor = "#4e73df"; // Primary blue
                typeTextColor = "text-white";
            }

            const currencySymbol = "{{ $driverCurrency->symbol ?? '₹' }}";
            const exchangeRate = {{ $driverCurrency->exchange_rate ?? 1.0 }};

            const alertHtml = `
                <div id="ride-alert-${request.id}" class="card border-0 shadow-lg mb-3 animate__animated animate__fadeInRight pulse-booking-card" 
                     style="background: #1a1a1a; color: white; border-radius: 15px; border-left: 5px solid ${typeColor}; pointer-events: auto;">
                    <div class="card-body p-3">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="badge rounded-pill" style="background: ${typeColor}; color: ${typeTextColor === 'text-white' ? '#fff' : '#000'};">
                                <i class="bi ${typeIcon} me-1"></i> ${typeLabel}
                            </span>
                            <span class="fw-bold text-white small">EST: ${currencySymbol}${(request.fare * exchangeRate).toFixed(2)}</span>
                        </div>
                        <div class="d-flex align-items-center mb-3">
                            <img src="https://ui-avatars.com/api/?name=${encodeURIComponent(request.user.name)}&background=${typeColor.replace('#', '')}&color=${typeTextColor === 'text-white' ? 'fff' : '000'}" class="rounded-circle me-2" style="width: 40px; height: 40px;">
                            <div>
                                <h6 class="fw-bold mb-0">${request.user.name}</h6>
                                <div class="extra-small opacity-75">${request.service_type.charAt(0).toUpperCase() + request.service_type.slice(1)} Service</div>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <div class="small mb-1"><i class="bi bi-geo-alt-fill text-brand"></i> <strong>From:</strong> ${request.pickup_location}</div>
                            <div class="small"><i class="bi bi-flag-fill text-brand"></i> <strong>To:</strong> ${request.dropoff_location}</div>
                            ${request.parcel_details ? `<div class="extra-small text-muted mt-2 p-2 bg-dark rounded border border-secondary border-opacity-25"><i class="bi bi-info-circle me-1"></i> ${request.parcel_details}</div>` : ''}
                        </div>

                        <div class="bg-dark p-2 rounded-3 mb-3 border border-secondary border-opacity-25">
                            <label class="extra-small text-muted fw-bold mb-1 d-block text-uppercase">Your Bid Amount (${currencySymbol})</label>
                            <input type="number" id="bid-amount-${request.id}" class="form-control form-control-sm bg-transparent border-0 text-white fw-bold shadow-none p-0" value="${((request.fare || 100) * exchangeRate).toFixed(0)}" style="font-size: 1.2rem;">
                        </div>

                        <div class="d-grid gap-2">
                            <button onclick="placeBid(${request.id})" id="bid-btn-${request.id}" class="btn btn-brand btn-sm w-100 rounded-pill fw-bold">PLACE BID</button>
                            <div class="d-flex gap-2">
                                <form action="/driver/rides/${request.id}/accept" method="POST" class="w-100">
                                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                    <button type="submit" class="btn btn-outline-light btn-sm w-100 rounded-pill fw-bold" style="font-size: 0.7rem;">INSTANT ACCEPT</button>
                                </form>
                                <button onclick="document.getElementById('ride-alert-${request.id}').remove()" class="btn btn-danger btn-sm w-100 rounded-pill fw-bold" style="font-size: 0.7rem;">REJECT</button>
                            </div>
                        </div>
                    </div>
                </div>
            `;
            container.insertAdjacentHTML('beforeend', alertHtml);
            
            // Auto hide after 60 seconds for bidding
            setTimeout(() => {
                const el = document.getElementById('ride-alert-' + request.id);
                if(el) el.remove();
            }, 60000);
        }

        window.placeBid = function(rideId) {
            const amount = document.getElementById('bid-amount-' + rideId).value;
            const btn = document.getElementById('bid-btn-' + rideId);
            
            btn.disabled = true;
            btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Sending...';

            fetch('{{ route("driver.bids.store") }}', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    booking_id: rideId,
                    bid_amount: amount / exchangeRate
                })
            })
            .then(async res => {
                const data = await res.json().catch(() => ({}));
                if (!res.ok) {
                    throw new Error(data.message || 'Unable to place bid');
                }
                return data;
            })
            .then(data => {
                const alertEl = document.getElementById('ride-alert-' + rideId);
                if(data.success) {
                    alertEl.innerHTML = `
                        <div class="card-body p-4 text-center">
                            <i class="bi bi-check-circle-fill text-brand display-6 mb-2 d-block"></i>
                            <h6 class="fw-bold text-white">BID PLACED!</h6>
                            <p class="extra-small text-muted mb-3">Wait for customer response.</p>
                            <a href="{{ route('driver.bids.index') }}" class="btn btn-brand btn-sm rounded-pill px-4 fw-bold">VIEW MY BIDS</a>
                        </div>
                    `;
                    setTimeout(() => alertEl.remove(), 10000);
                } else {
                    alert(data.message || 'Unable to place bid');
                    btn.disabled = false;
                    btn.innerHTML = 'PLACE BID';
                }
            })
            .catch(err => {
                btn.disabled = false;
                btn.innerHTML = 'PLACE BID';
                alert(err.message || 'Connection error. Try again.');
            });
        }

        @if($driver->is_online)
        startPolling();
        @endif
    });
    </script>


    <!-- Active Vehicle / Quick Actions -->
    <div class="col-lg-4">
        <div class="drv-card mb-4">
            <div class="bg-brand py-3 px-4 d-flex align-items-center justify-content-between">
                <h2 class="h6 fw-bold mb-0 text-dark">Active vehicle</h2>
                <a href="{{ route('driver.vehicles.index') }}" class="text-dark small" aria-label="Manage vehicles"><i class="bi bi-gear-fill"></i></a>
            </div>
            <div class="p-4">
                @if($activeVehicle)
                <div class="d-flex align-items-center mb-3 pb-3 border-bottom">
                    <div class="flex-grow-1">
                        <div class="small text-muted mb-1 text-uppercase fw-bold">Active car</div>
                        <div class="fw-bold text-dark">{{ $activeVehicle->brand }} {{ $activeVehicle->model }}</div>
                    </div>
                    <div class="badge bg-dark rounded-pill">{{ $activeVehicle->category->name }}</div>
                </div>
                <div class="d-flex align-items-center mb-0">
                    <div class="flex-grow-1">
                        <div class="small text-muted mb-1 text-uppercase fw-bold">Plate number</div>
                        <div class="fw-bold text-dark">{{ $activeVehicle->number_plate }}</div>
                    </div>
                    <div class="badge {{ $activeVehicle->verification_status == 'approved' ? 'bg-success' : 'bg-warning text-dark' }} rounded-pill px-2 py-1" style="font-size: 0.6rem;">
                        {{ strtoupper($activeVehicle->verification_status) }}
                    </div>
                </div>
                @else
                <div class="text-center py-3">
                    <i class="bi bi-car-front text-muted display-6 d-block mb-3"></i>
                    <p class="small text-muted fw-bold mb-3">No active vehicle selected</p>
                    <a href="{{ route('driver.vehicles.index') }}" class="btn btn-dark btn-sm rounded-pill px-3">Select vehicle</a>
                </div>
                @endif
            </div>
        </div>

        <div class="drv-card bg-dark text-white p-4">
            <div class="d-flex align-items-center">
                <div class="bg-brand text-dark p-3 rounded-circle me-3">
                    <i class="bi bi-headset fs-4"></i>
                </div>
                <div>
                    <h2 class="h6 fw-bold mb-1">Support center</h2>
                    <p class="small text-white-50 mb-2">Need help with a ride or technical issue?</p>
                    <a href="{{ route('driver.support.index') }}" class="text-brand small text-decoration-none fw-bold">Open support ticket <i class="bi bi-arrow-right"></i></a>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.bg-brand { background-color: #cddc29 !important; }
.active-scale:active { transform: scale(0.98); }
.btn-brand { background-color: #cddc29; color: #000; }
.btn-brand:hover { background-color: #b9c825; color: #000; }
.text-brand { color: #cddc29 !important; }
@keyframes pulse-border {
    0% { box-shadow: 0 4px 15px rgba(205, 220, 41, 0.4), 0 0 0 0 rgba(205, 220, 41, 0.7); }
    70% { box-shadow: 0 4px 15px rgba(205, 220, 41, 0.2), 0 0 0 15px rgba(205, 220, 41, 0); }
    100% { box-shadow: 0 4px 15px rgba(205, 220, 41, 0), 0 0 0 0 rgba(205, 220, 41, 0); }
}
.pulse-booking-card { animation: pulse-border 1.8s infinite; }
</style>
@endsection
