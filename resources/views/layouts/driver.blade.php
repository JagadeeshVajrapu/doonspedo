<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ $dir ?? 'ltr' }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Driver - Doonspedo')</title>

    <link rel="preconnect" href="https://cdn.jsdelivr.net" crossorigin>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

    @if(($dir ?? 'ltr') == 'rtl')
    <!-- RTL Bootstrap -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.rtl.min.css">
    @else
    <!-- LTR Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    @endif

    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- PWA Manifest -->
    <link rel="manifest" href="{{ asset('manifest.json') }}">
    <meta name="theme-color" content="#cddc29">
    
    <!-- Doonspedo Design System + Backend -->
    <link rel="stylesheet" href="{{ asset('css/design-system.css') }}?v={{ filemtime(public_path('css/design-system.css')) }}">
    <link rel="stylesheet" href="{{ asset('css/backend.css') }}?v={{ filemtime(public_path('css/backend.css')) }}">
    
    @if(!empty($sys_settings['theme_color']))
    <style>
        :root {
            --ds-brand: {{ $sys_settings['theme_color'] }};
            --admin-primary: var(--ds-brand);
            --primary-color: var(--ds-brand);
        }
    </style>
    @endif
    
    <style>
        body {
            background-color: var(--ds-page-light, #f3f5f7);
            font-family: var(--ds-font, 'Outfit', sans-serif);
            transition: background-color 0.3s ease;
        }

        .main-content {
            flex-grow: 1;
            padding: 40px;
            min-height: 100vh;
            overflow-x: clip;
        }
        @media (max-width: 991.98px) {
            .main-content {
                padding: 15px;
            }
        }

        /* Sidebar / drawer behavior (functional — keep in layout) */
        .nav-link {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            border-radius: 12px !important;
        }
        .nav-link:hover {
            transform: translateX(3px);
            background: rgba(255, 255, 255, 0.06) !important;
            color: var(--ds-text-on-dark, #f4f5f7) !important;
        }
        .nav-link.active {
            background: var(--ds-brand, var(--admin-primary, #cddc29)) !important;
            color: #111 !important;
            box-shadow: none;
        }
        .offcanvas {
            border-right: 1px solid rgba(255, 255, 255, 0.08);
            box-shadow: 10px 0 30px rgba(0, 0, 0, 0.45);
            z-index: 10000 !important;
            transition: transform 0.3s ease-in-out !important;
            background: var(--ds-charcoal, #14171c) !important;
            color: var(--ds-text-on-dark, #f4f5f7) !important;
        }
        .offcanvas.manual-show {
            transform: none !important;
            visibility: visible !important;
            display: flex !important;
            left: 0 !important;
        }
        #mobileMenuToggle {
            cursor: pointer !important;
            z-index: 3000 !important;
            position: relative;
        }
        .offcanvas.manual-show .offcanvas-overlay {
            display: block !important;
        }

        @media (max-width: 991px) {
            #mobileMenu {
                display: flex !important;
            }
            #mobileMenu:not(.manual-show):not(.show) {
                transform: translateX(-100%) !important;
            }
        }
    </style>
    @yield('styles')
</head>
    <body class="driver-shell {{ ($theme ?? 'light') == 'dark' ? 'dark-mode' : '' }}">
    <!-- Relocated Mobile Menu to Top of Body for perfect visibility -->
    <div class="offcanvas offcanvas-start d-lg-none" tabindex="-1" id="mobileMenu" role="dialog" aria-modal="true" aria-labelledby="driverMobileMenuLabel" style="z-index: 999999; transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);">
        <div class="offcanvas-overlay" onclick="(function(){var m=document.getElementById('mobileMenu');var t=document.getElementById('mobileMenuToggle');if(m){m.classList.remove('manual-show');}if(t){t.setAttribute('aria-expanded','false');}})()" style="position: fixed; top: 0; left: 0; width: 100vw; height: 100vh; background: rgba(0,0,0,0.7); z-index: -1; display: none; backdrop-filter: blur(2px);"></div>
        <div class="offcanvas-header border-bottom border-secondary border-opacity-25 p-4">
            <h2 class="offcanvas-title h5 text-brand fw-bold mb-0" id="driverMobileMenuLabel">Driver Menu</h2>
            <button type="button" class="btn-close btn-close-white" aria-label="Close menu" onclick="(function(){var m=document.getElementById('mobileMenu');var t=document.getElementById('mobileMenuToggle');if(m){m.classList.remove('manual-show');}if(t){t.setAttribute('aria-expanded','false');t.focus();}})()"></button>
        </div>
        <div class="offcanvas-body p-4">
            <div class="text-center mb-4 pb-3 border-bottom border-secondary border-opacity-25">
                @if($driver->profile_image ?? false)
                    <img src="{{ asset('storage/' . $driver->profile_image) }}" class="rounded-circle border border-2 border-brand mb-2" style="width: 56px; height: 56px; object-fit: cover;" alt="{{ $driver->name }}">
                @else
                    <div class="bg-brand-soft rounded-circle d-flex align-items-center justify-content-center mx-auto mb-2" style="width: 56px; height: 56px;">
                        <i class="bi bi-person text-brand fs-4"></i>
                    </div>
                @endif
                <div class="fw-bold text-white">{{ $driver->name ?? 'Captain' }}</div>
                <div class="small text-white-50">{{ ($driver->is_online ?? false) ? 'Online' : 'Offline' }}</div>
            </div>
            <ul class="nav flex-column gap-1 mb-4">
                <li class="nav-section-label text-white-50 px-3 mb-1">Overview</li>
                <li class="nav-item">
                    <a href="{{ route('driver.dashboard') }}" class="nav-link {{ request()->routeIs('driver.dashboard') ? 'active' : '' }} fw-bold p-3 rounded-4 mb-1">
                        <i class="bi bi-speedometer2 me-2"></i> Dashboard
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('driver.availability') }}" class="nav-link {{ request()->routeIs('driver.availability') ? 'active' : '' }} fw-bold p-3 rounded-4 mb-1">
                        <i class="bi bi-broadcast me-2"></i> Availability
                    </a>
                </li>
                <li class="nav-section-label text-white-50 px-3 mb-1 mt-2">Operations</li>
                <li class="nav-item">
                    <a href="{{ route('driver.rides.index') }}" class="nav-link {{ request()->routeIs('driver.rides.*') ? 'active' : '' }} fw-bold p-3 rounded-4 mb-1">
                        <i class="bi bi-briefcase me-2"></i> My Rides
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('driver.bids.index') }}" class="nav-link {{ request()->routeIs('driver.bids.*') ? 'active' : '' }} fw-bold p-3 rounded-4 mb-1">
                        <i class="bi bi-lightning-charge me-2"></i> Ride Requests / Bids
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('driver.vehicles.index') }}" class="nav-link {{ request()->routeIs('driver.vehicles.*') ? 'active' : '' }} fw-bold p-3 rounded-4 mb-1">
                        <i class="bi bi-car-front me-2"></i> Vehicle
                    </a>
                </li>
                <li class="nav-section-label text-white-50 px-3 mb-1 mt-2">Finance</li>
                <li class="nav-item">
                    <a href="{{ route('driver.earnings') }}" class="nav-link {{ request()->routeIs('driver.earnings') ? 'active' : '' }} fw-bold p-3 rounded-4 mb-1">
                        <i class="bi bi-graph-up-arrow me-2"></i> Earnings
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('driver.wallet') }}" class="nav-link {{ request()->routeIs('driver.wallet*') ? 'active' : '' }} fw-bold p-3 rounded-4 mb-1">
                        <i class="bi bi-wallet2 me-2"></i> Wallet
                    </a>
                </li>
                @if(config('taxi.subscriptions_enabled', true))
                <li class="nav-item">
                    <a href="{{ route('driver.subscriptions') }}" class="nav-link {{ request()->routeIs('driver.subscriptions') ? 'active' : '' }} fw-bold p-3 rounded-4 mb-1">
                        <i class="bi bi-calendar-check me-2"></i> Subscriptions
                    </a>
                </li>
                @endif
                <li class="nav-section-label text-white-50 px-3 mb-1 mt-2">Account</li>
                <li class="nav-item">
                    <a href="{{ route('driver.kyc') }}" class="nav-link {{ request()->routeIs('driver.kyc') ? 'active' : '' }} fw-bold p-3 rounded-4 mb-1">
                        <i class="bi bi-shield-check me-2"></i> KYC / Documents
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('driver.profile') }}" class="nav-link {{ request()->routeIs('driver.profile') ? 'active' : '' }} fw-bold p-3 rounded-4 mb-1">
                        <i class="bi bi-person me-2"></i> Profile
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('driver.notifications.index') }}" class="nav-link {{ request()->routeIs('driver.notifications.*') ? 'active' : '' }} fw-bold p-3 rounded-4 mb-1">
                        <i class="bi bi-bell me-2"></i> Notifications
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('driver.support.index') }}" class="nav-link {{ request()->routeIs('driver.support.*') ? 'active' : '' }} fw-bold p-3 rounded-4 mb-1">
                        <i class="bi bi-headset me-2"></i> Support
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('driver.settings') }}" class="nav-link {{ request()->routeIs('driver.settings') ? 'active' : '' }} fw-bold p-3 rounded-4 mb-1">
                        <i class="bi bi-gear-fill me-2"></i> Settings
                    </a>
                </li>
            </ul>
            <form action="{{ route('driver.logout') }}" method="POST" class="mt-4">
                @csrf
                <button type="submit" class="btn btn-outline-danger w-100 py-3 rounded-pill shadow-sm fw-bold">Logout</button>
            </form>
        </div>
    </div>
    <div class="d-flex">
        <!-- Sidebar -->
        @include('frontend.driver.inc.sidebar')

        <!-- Main Content Area -->
        <a class="ds-skip-link" href="#main-content">Skip to main content</a>
        <main class="main-content" id="main-content">
            <!-- Mobile Toggle Header (Mobile only) -->
            <div class="d-lg-none mb-3 d-flex justify-content-between align-items-center">
                <div>
                    <a href="{{ route('driver.dashboard') }}" class="text-decoration-none h5 fw-bold text-dark mb-0 d-block">{{ $sys_settings['app_name'] ?? 'Doonspedo' }}</a>
                    <span class="x-small text-muted">Captain</span>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <a href="{{ route('driver.notifications.index') }}" class="btn btn-light border rounded-circle p-2" aria-label="Notifications">
                        <i class="bi bi-bell" aria-hidden="true"></i>
                    </a>
                    <button class="btn btn-dark p-2 rounded-circle shadow" type="button"
                            id="mobileMenuToggle"
                            onclick="(function(){var menu=document.getElementById('mobileMenu');var t=document.getElementById('mobileMenuToggle');if(menu){menu.classList.add('manual-show');}if(t){t.setAttribute('aria-expanded','true');}})()"
                            aria-label="Open menu" aria-controls="mobileMenu" aria-expanded="false">
                        <i class="bi bi-list fs-4" aria-hidden="true"></i>
                    </button>
                </div>
            </div>

            <!-- Permission Status Widget -->
            <div id="permission-bar" class="mb-4 d-none" style="transition: all 0.5s ease-in-out;">
                <div class="row g-2">
                    <!-- Location -->
                    <div class="col-12" id="loc-permission-card">
                        <div class="card border-0 shadow-sm rounded-4 p-3 bg-white" style="transition: all 0.3s ease;">
                            <div class="d-flex align-items-center justify-content-between">
                                <div class="d-flex align-items-center">
                                    <div class="p-2 rounded-circle bg-danger bg-opacity-10 text-danger me-3" id="loc-icon-bg">
                                        <i class="bi bi-geo-alt-fill fs-5" id="loc-icon"></i>
                                    </div>
                                    <div>
                                        <h6 class="fw-bold mb-0 text-dark" style="font-size:0.9rem;">Location</h6>
                                        <p class="small text-muted mb-0" id="loc-status-text" style="font-size:0.75rem;">Required for ride matching</p>
                                    </div>
                                </div>
                                <button type="button" class="btn btn-danger btn-sm rounded-pill px-3 fw-bold" id="btn-request-location" onclick="requestLocationPermission()">
                                    ALLOW
                                </button>
                            </div>
                        </div>
                    </div>
                    <!-- Camera -->
                    <div class="col-6" id="cam-permission-card">
                        <div class="card border-0 shadow-sm rounded-4 p-3 bg-white" style="transition: all 0.3s ease;">
                            <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
                                <div class="d-flex align-items-center">
                                    <div class="p-2 rounded-circle bg-warning bg-opacity-10 text-warning me-2" id="cam-icon-bg">
                                        <i class="bi bi-camera-fill fs-5" id="cam-icon"></i>
                                    </div>
                                    <div>
                                        <h6 class="fw-bold mb-0 text-dark" style="font-size:0.85rem;">Camera</h6>
                                        <p class="small text-muted mb-0" id="cam-status-text" style="font-size:0.7rem;">For KYC & profile photo</p>
                                    </div>
                                </div>
                                <button type="button" class="btn btn-warning text-dark btn-sm rounded-pill px-2 fw-bold" id="btn-request-camera" onclick="requestCameraPermission()">
                                    ALLOW
                                </button>
                            </div>
                        </div>
                    </div>
                    <!-- Call -->
                    <div class="col-6" id="call-permission-card">
                        <div class="card border-0 shadow-sm rounded-4 p-3 bg-white" style="transition: all 0.3s ease;">
                            <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
                                <div class="d-flex align-items-center">
                                    <div class="p-2 rounded-circle bg-success bg-opacity-10 text-success me-2" id="call-icon-bg">
                                        <i class="bi bi-telephone-fill fs-5" id="call-icon"></i>
                                    </div>
                                    <div>
                                        <h6 class="fw-bold mb-0 text-dark" style="font-size:0.85rem;">Phone Call</h6>
                                        <p class="small text-muted mb-0" id="call-status-text" style="font-size:0.7rem;">Call passengers directly</p>
                                    </div>
                                </div>
                                <span class="badge bg-success rounded-pill px-2" id="call-granted-badge" style="font-size:0.7rem;">READY</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Content -->
            @yield('content')
        </main>
    </div>

    @include('partials.ui.driver-bottom-nav')

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const myOffcanvas = document.getElementById('mobileMenu');
            if (myOffcanvas) {
                bootstrap.Offcanvas.getOrCreateInstance(myOffcanvas);
            }
            
            // Auto-detect all permissions on page load
            autoDetectLocation();
            checkCameraPermission();
            // Phone call is always "ready" on mobile (tel: links work natively)
            markCallReady();
        });

        /* ── LOCATION ── */
        function autoDetectLocation() {
            if (!navigator.geolocation) {
                updateLocationUI('unsupported');
                checkHideBar();
                return;
            }
            navigator.geolocation.getCurrentPosition(
                function(position) {
                    updateLocationUI('granted');
                    localStorage.setItem('location_allowed', 'true');
                    fetch("{{ route('driver.updateLocation') }}", {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                        body: JSON.stringify({ latitude: position.coords.latitude, longitude: position.coords.longitude })
                    }).catch(function() {});
                },
                function(error) {
                    updateLocationUI(error.code === error.PERMISSION_DENIED ? 'denied' : 'prompt');
                },
                { enableHighAccuracy: true, timeout: 8000, maximumAge: 30000 }
            );
        }

        /* ── CAMERA ── */
        function checkCameraPermission() {
            if (!navigator.mediaDevices || !navigator.mediaDevices.getUserMedia) {
                updateCameraUI('granted'); // No media API = not needed, hide silently
                return;
            }
            if (localStorage.getItem('camera_allowed') === 'true') {
                updateCameraUI('granted');
                return;
            }
            if (navigator.permissions && navigator.permissions.query) {
                navigator.permissions.query({ name: 'camera' }).then(function(result) {
                    updateCameraUI(result.state);
                }).catch(function() { updateCameraUI('prompt'); });
            } else {
                updateCameraUI('prompt');
            }
        }

        function requestCameraPermission() {
            navigator.mediaDevices.getUserMedia({ video: true })
                .then(function(stream) {
                    stream.getTracks().forEach(function(t) { t.stop(); });
                    localStorage.setItem('camera_allowed', 'true');
                    updateCameraUI('granted');
                })
                .catch(function() { updateCameraUI('denied'); });
        }

        /* ── PHONE CALL ── */
        function markCallReady() {
            // tel: links always work on mobile — mark as ready
            updateCallUI('granted');
        }

        function checkPermissions() {
            autoDetectLocation();
            checkCameraPermission();
            markCallReady();
        }

        /* ── UI UPDATERS ── */
        function updateLocationUI(state) {
            var iconBg = document.getElementById('loc-icon-bg');
            var text   = document.getElementById('loc-status-text');
            var btn    = document.getElementById('btn-request-location');

            if (state === 'granted' || state === 'unsupported') {
                document.getElementById('loc-permission-card').classList.add('d-none');
            } else if (state === 'denied') {
                iconBg.className = 'p-2 rounded-circle bg-danger bg-opacity-10 text-danger me-3';
                text.innerText   = 'Blocked — enable in browser settings';
                btn.className    = 'btn btn-danger btn-sm rounded-pill px-3 fw-bold';
                btn.innerText    = 'FIX';
                btn.disabled     = false;
            } else {
                iconBg.className = 'p-2 rounded-circle bg-warning bg-opacity-10 text-warning me-3';
                text.innerText   = 'Tap ALLOW for ride matching';
                btn.className    = 'btn btn-danger btn-sm rounded-pill px-3 fw-bold';
                btn.innerText    = 'ALLOW';
                btn.disabled     = false;
            }
            checkHideBar();
        }

        function updateCameraUI(state) {
            var iconBg = document.getElementById('cam-icon-bg');
            var text   = document.getElementById('cam-status-text');
            var btn    = document.getElementById('btn-request-camera');

            if (state === 'granted') {
                document.getElementById('cam-permission-card').classList.add('d-none');
            } else if (state === 'denied') {
                iconBg.className = 'p-2 rounded-circle bg-danger bg-opacity-10 text-danger me-2';
                text.innerText   = 'Blocked — enable in settings';
                btn.className    = 'btn btn-danger btn-sm rounded-pill px-2 fw-bold';
                btn.innerText    = 'FIX';
            } else {
                iconBg.className = 'p-2 rounded-circle bg-warning bg-opacity-10 text-warning me-2';
                text.innerText   = 'For KYC & profile photo';
                btn.className    = 'btn btn-warning text-dark btn-sm rounded-pill px-2 fw-bold';
                btn.innerText    = 'ALLOW';
            }
            checkHideBar();
        }

        function updateCallUI(state) {
            if (state === 'granted') {
                document.getElementById('call-permission-card').classList.add('d-none');
            }
            checkHideBar();
        }

        function checkHideBar() {
            var locHidden  = document.getElementById('loc-permission-card').classList.contains('d-none');
            var camHidden  = document.getElementById('cam-permission-card').classList.contains('d-none');
            var callHidden = document.getElementById('call-permission-card').classList.contains('d-none');
            var permBar    = document.getElementById('permission-bar');

            if (locHidden && camHidden && callHidden) {
                permBar.style.opacity   = '0';
                permBar.style.transform = 'translateY(-20px)';
                setTimeout(function() { permBar.classList.add('d-none'); }, 500);
            } else {
                permBar.classList.remove('d-none');
                permBar.style.opacity   = '1';
                permBar.style.transform = 'translateY(0)';
            }
        }

        function requestLocationPermission() {
            navigator.geolocation.getCurrentPosition(
                function(position) {
                    updateLocationUI('granted');
                    localStorage.setItem('location_allowed', 'true');
                    fetch("{{ route('driver.updateLocation') }}", {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                        body: JSON.stringify({ latitude: position.coords.latitude, longitude: position.coords.longitude })
                    }).catch(function() {});
                },
                function() {
                    alert('Location blocked. Please tap the lock icon in your browser URL bar and enable Location.');
                    updateLocationUI('denied');
                },
                { enableHighAccuracy: true, timeout: 10000 }
            );
        }
    </script>
    @hasSection('needs_google_maps')
        @if(($sys_settings['map_provider'] ?? 'google') == 'google' && !empty($sys_settings['google_maps_key']))
            <script src="https://maps.googleapis.com/maps/api/js?key={{ $sys_settings['google_maps_key'] }}&libraries=places"></script>
        @endif
    @endif
    @yield('scripts')
    @include('partials.ui.a11y-enhancements')
</body>
</html>
