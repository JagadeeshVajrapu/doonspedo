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

                        document.querySelectorAll('#driver-presence-badge, #driver-presence-label').forEach(function (badge) {
                            badge.textContent = isOnline ? 'Online' : 'Offline';
                            if (badge.id === 'driver-presence-badge') {
                                badge.classList.toggle('ds-badge-success', isOnline);
                                badge.classList.toggle('ds-badge-neutral', !isOnline);
                            }
                        });

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
        let pollInFlight = false;
        const submittingRideIds = new Set();
        const dismissedRideIds = new Set(readDismissedRides());

        function readDismissedRides() {
            try {
                const saved = JSON.parse(sessionStorage.getItem('doonspedo_dismissed_rides') || '[]');
                return Array.isArray(saved) ? saved.map(String) : [];
            } catch (e) {
                return [];
            }
        }

        function rememberDismissed(id) {
            dismissedRideIds.add(String(id));
            try {
                sessionStorage.setItem('doonspedo_dismissed_rides', JSON.stringify([...dismissedRideIds]));
            } catch (e) {}
        }

        function inrAmount(amount) {
            const n = Number(amount);
            return Number.isFinite(n) && n > 0 ? n : null;
        }

        function inrLabel(amount) {
            const n = inrAmount(amount);
            return n === null ? 'Fare unavailable' : '₹' + n.toFixed(2);
        }

        function startPolling() {
            if(pollInterval) return;
            checkRequests();
            pollInterval = setInterval(checkRequests, 5000);
        }

        function stopPolling() {
            clearInterval(pollInterval);
            pollInterval = null;
            const box = document.getElementById('new-requests-container');
            if (box) box.innerHTML = '';
        }

        function checkRequests() {
            if (pollInFlight) return;
            pollInFlight = true;
            fetch('{{ route("driver.rides.requests") }}', { headers: { 'Accept': 'application/json' } })
            .then(res => res.json())
            .then(data => {
                const box = document.getElementById('new-requests-container');
                if (!box) return;
                if (data.needs_location) {
                    box.innerHTML = '<div class="alert alert-warning small mb-0 shadow" style="pointer-events:auto;">Allow location to receive nearby ride requests.</div>';
                    return;
                }
                if (data.documents_pending) {
                    box.innerHTML = '<div class="alert alert-warning small mb-0 shadow" style="pointer-events:auto;">Your documents are not verified yet. You can receive rides after admin verifies them.</div>';
                    return;
                }
                const requests = (data.success && Array.isArray(data.requests)) ? data.requests : [];
                const activeIds = new Set();
                requests.forEach(request => {
                    const id = String(request.id);
                    if (dismissedRideIds.has(id)) return;
                    activeIds.add(id);
                    const existing = document.getElementById('ride-alert-' + id);
                    if (existing) updateRequestAlert(existing, request);
                    else showRequestAlert(request);
                });
                box.querySelectorAll('[data-ride-request]').forEach(card => {
                    const id = card.getAttribute('data-ride-request');
                    if (!activeIds.has(id) && !submittingRideIds.has(id)) card.remove();
                });
            })
            .catch(() => {})
            .finally(() => { pollInFlight = false; });
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

        function updateRequestAlert(card, request) {
            const fare = card.querySelector('.js-est-fare');
            if (fare) fare.textContent = 'Offer ' + inrLabel(request.fare);
            const offer = card.querySelector('.js-customer-offer');
            if (offer) offer.textContent = inrLabel(request.fare);
            const from = card.querySelector('.js-ride-from');
            if (from) from.textContent = (request.pickup_location || '') + (request.distance_km != null ? ' · ' + request.distance_km + ' km away' : '');
            const to = card.querySelector('.js-ride-to');
            if (to) to.textContent = request.dropoff_location || '';
        }

        function announcedRides() {
            try {
                const saved = JSON.parse(sessionStorage.getItem('doonspedo_announced_rides') || '[]');
                return new Set(Array.isArray(saved) ? saved.map(String) : []);
            } catch (e) {
                return new Set();
            }
        }

        function markAnnounced(id) {
            const ids = announcedRides();
            ids.add(String(id));
            try {
                sessionStorage.setItem('doonspedo_announced_rides', JSON.stringify([...ids].slice(-100)));
            } catch (e) {}
        }

        function showRequestAlert(request) {
            const rideId = String(request.id);
            if(document.getElementById('ride-alert-' + rideId)) return;

            if (!announcedRides().has(rideId)) {
                markAnnounced(rideId);
                playBookingBell();
                triggerVibration();
            }

            const container = document.getElementById('new-requests-container');
            if (!container) return;
            
            let typeLabel = "NEW RIDE REQUEST";
            let typeIcon = "bi-person-circle";
            let typeColor = "#cddc29";
            let typeTextColor = "text-dark";

            if(request.service_type === 'parcel') {
                typeLabel = "PARCEL DELIVERY";
                typeIcon = "bi-box-seam-fill";
                typeColor = "#36b9cc";
                typeTextColor = "text-white";
            } else if(request.service_type === 'freight') {
                typeLabel = "FREIGHT JOB";
                typeIcon = "bi-truck";
                typeColor = "#f6c23e";
                typeTextColor = "text-dark";
            } else if(request.service_type === 'rental') {
                typeLabel = "RENTAL BOOKING";
                typeIcon = "bi-calendar-event";
                typeColor = "#4e73df";
                typeTextColor = "text-white";
            }

            const customerName = (request.user && request.user.name) ? request.user.name : 'Customer';
            const serviceName = (request.service_type || 'ride');

            const alertHtml = `
                <div id="ride-alert-${rideId}" data-ride-request="${rideId}" class="card border-0 shadow-lg mb-3 animate__animated animate__fadeInRight pulse-booking-card" 
                     style="background: #1a1a1a; color: white; border-radius: 15px; border-left: 5px solid ${typeColor}; pointer-events: auto;">
                    <div class="card-body p-3">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="badge rounded-pill" style="background: ${typeColor}; color: ${typeTextColor === 'text-white' ? '#fff' : '#000'};">
                                <i class="bi ${typeIcon} me-1"></i> ${typeLabel}
                            </span>
                            <span class="js-est-fare fw-bold text-white small">Offer ${inrLabel(request.fare)}</span>
                        </div>
                        <div class="d-flex align-items-center mb-3">
                            <img src="https://ui-avatars.com/api/?name=${encodeURIComponent(customerName)}&background=${typeColor.replace('#', '')}&color=${typeTextColor === 'text-white' ? 'fff' : '000'}" class="rounded-circle me-2" style="width: 40px; height: 40px;">
                            <div>
                                <h6 class="fw-bold mb-0">${customerName}</h6>
                                <div class="extra-small opacity-75">${serviceName.charAt(0).toUpperCase() + serviceName.slice(1)} Service</div>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <div class="small mb-1"><i class="bi bi-geo-alt-fill text-brand"></i> <strong>From:</strong> <span class="js-ride-from">${request.pickup_location || ''}${request.distance_km != null ? ' · ' + request.distance_km + ' km away' : ''}</span></div>
                            <div class="small"><i class="bi bi-flag-fill text-brand"></i> <strong>To:</strong> <span class="js-ride-to">${request.dropoff_location || ''}</span></div>
                            ${request.parcel_details ? `<div class="extra-small text-muted mt-2 p-2 bg-dark rounded border border-secondary border-opacity-25"><i class="bi bi-info-circle me-1"></i> ${request.parcel_details}</div>` : ''}
                        </div>

                        <div class="bg-dark p-3 rounded-3 mb-3 border border-secondary border-opacity-25">
                            <div class="extra-small text-white-50 fw-bold text-uppercase mb-1">Customer offer</div>
                            <div class="js-customer-offer fw-bold text-white" style="font-size: 1.35rem;">${inrLabel(request.fare)}</div>
                            <div class="extra-small text-white-50 mt-1">This is the amount the customer is offering for this ride.</div>
                        </div>

                        <div class="d-grid gap-2">
                            <form action="/driver/rides/${rideId}/accept" method="POST" onsubmit="return submitInstantAccept(this, '${rideId}')">
                                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                <button type="submit" class="btn btn-brand btn-sm w-100 rounded-pill fw-bold">ACCEPT</button>
                            </form>
                            <button type="button" onclick="dismissRideRequest('${rideId}')" class="btn btn-danger btn-sm w-100 rounded-pill fw-bold">REJECT</button>
                        </div>
                    </div>
                </div>
            `;
            container.insertAdjacentHTML('beforeend', alertHtml);
        }

        window.dismissRideRequest = function(rideId) {
            rememberDismissed(rideId);
            const el = document.getElementById('ride-alert-' + rideId);
            if (el) el.remove();
            fetch('/driver/rides/' + encodeURIComponent(rideId) + '/reject', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            }).catch(function () {});
        };

        window.submitInstantAccept = function(form, rideId) {
            const id = String(rideId);
            if (submittingRideIds.has(id)) return false;
            submittingRideIds.add(id);
            const card = document.getElementById('ride-alert-' + id);
            if (card) card.querySelectorAll('button').forEach(button => { button.disabled = true; });
            const submit = form.querySelector('button[type="submit"]');
            if (submit) submit.innerHTML = 'Accepting...';
            return true;
        };

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
