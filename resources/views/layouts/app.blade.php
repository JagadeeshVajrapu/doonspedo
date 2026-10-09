<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ session('direction', 'ltr') }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Doonspedo')</title>
    @include('partials.ui.favicon')
    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    {{-- Critical origin hints (fonts + Bootstrap CDN) --}}
    <link rel="preconnect" href="https://cdn.jsdelivr.net" crossorigin>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="dns-prefetch" href="https://cdn.jsdelivr.net">

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <!-- Google Fonts — Outfit (display=swap; trimmed weights) -->
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600;700&display=swap" rel="stylesheet">

    @hasSection('needs_maps')
        <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin=""/>
        @if(($sys_settings['map_provider'] ?? 'google') == 'google' && !empty($sys_settings['google_maps_key']))
            <link rel="dns-prefetch" href="https://maps.googleapis.com">
        @endif
    @endif

    <!-- Doonspedo Design System + Frontend -->
    <link rel="stylesheet" href="{{ asset('css/design-system.css') }}?v={{ filemtime(public_path('css/design-system.css')) }}">
    <link rel="stylesheet" href="{{ asset('css/frontend.css') }}?v={{ filemtime(public_path('css/frontend.css')) }}">
    <!-- PWA Manifest -->
    <link rel="manifest" href="{{ asset('manifest.json') }}">
    <meta name="theme-color" content="{{ session('theme') == 'light' ? '#ffffff' : ($sys_settings['theme_color'] ?? '#cddc29') }}">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">

    <style>
        @php
            $rawThemeColor = (string) ($sys_settings['theme_color'] ?? '#cddc29');
            $hex = ltrim($rawThemeColor, '#');
            if (strlen($hex) === 3) {
                $hex = $hex[0].$hex[0].$hex[1].$hex[1].$hex[2].$hex[2];
            }
            $brandSafe = $rawThemeColor;
            if (strlen($hex) === 6 && ctype_xdigit($hex)) {
                $r = hexdec(substr($hex, 0, 2));
                $g = hexdec(substr($hex, 2, 2));
                $b = hexdec(substr($hex, 4, 2));
                $luma = (0.299 * $r) + (0.587 * $g) + (0.114 * $b);
                // Ultra-light yellow/lime (e.g. #ffff66) is unreadable on white — use teal accents
                if ($luma >= 180) {
                    $brandSafe = '#0F766E';
                }
            }
        @endphp
        :root {
            --ds-brand: {{ $brandSafe }};
            --ds-brand-hover: {{ $brandSafe === '#0F766E' ? '#0d9488' : '#d8e640' }};
            --ds-brand-pressed: {{ $brandSafe === '#0F766E' ? '#115E59' : '#b8c61f' }};
            --ds-brand-soft: {{ $brandSafe === '#0F766E' ? 'rgba(15, 118, 110, 0.12)' : 'rgba(205, 220, 41, 0.12)' }};
            --ds-brand-ring: {{ $brandSafe === '#0F766E' ? 'rgba(15, 118, 110, 0.28)' : 'rgba(205, 220, 41, 0.28)' }};
            --ds-brand-ink: {{ $brandSafe === '#0F766E' ? '#ffffff' : '#111111' }};
            --ds-text-on-brand: {{ $brandSafe === '#0F766E' ? '#ffffff' : '#111111' }};
            --primary-color: var(--ds-brand);
            --bg-base: {{ session('theme') == 'light' ? '#f3f5f7' : '#0b0d10' }};
            --bg-card: {{ session('theme') == 'light' ? '#ffffff' : '#1c2128' }};
            --text-base: {{ session('theme') == 'light' ? '#12151a' : '#f4f5f7' }};
            --text-secondary: {{ session('theme') == 'light' ? '#5c6570' : '#a8b0bc' }};
            --border-color: {{ session('theme') == 'light' ? '#e2e6eb' : 'rgba(255,255,255,0.1)' }};
        }
        body { background-color: var(--bg-base) !important; color: var(--text-base) !important; }
        .bg-dark-custom { background-color: var(--bg-card) !important; }
        .border-secondary { border-color: var(--border-color) !important; }
        .text-white { color: var(--text-base) !important; }
        .text-secondary { color: var(--text-secondary) !important; }

        @if(session('direction') == 'rtl')
            body { text-align: right; }
            .ms-auto { margin-right: auto !important; margin-left: 0 !important; }
            .me-3 { margin-left:1rem !important; margin-right: 0 !important; }
        @endif
    </style>

    @stack('head')
    @yield('styles')
</head>
<body class="@yield('body_class')">
    <a class="ds-skip-link" href="#main-content">Skip to main content</a>
    @yield('content')

    @php
        $curr = session('currency', ['code' => 'INR', 'symbol' => '?', 'rate' => 1.0]);
        if (!function_exists('formatCurrency')) {
            function formatCurrency($amount, $curr) {
                return $curr['symbol'] . number_format($amount * $curr['rate'], 2);
            }
        }
    @endphp

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    @hasSection('needs_maps')
        {{-- Leaflet JS: required for OSM provider and as Google failure fallback --}}
        <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
    @endif

    <script>
        if ('serviceWorker' in navigator) {
            window.addEventListener('load', function () {
                navigator.serviceWorker.register('/sw.js').catch(function () {});
            });
        }
    </script>
    @yield('scripts')

    @hasSection('needs_maps')
        @php $mapProvider = $sys_settings['map_provider'] ?? 'google'; @endphp
        @if($mapProvider == 'google' && !empty($sys_settings['google_maps_key']))
            {{-- Load after page scripts so window.initMap already exists. --}}
            <script>
                window.__bootGoogleMap = function () {
                    var started = Date.now();
                    (function tryInit() {
                        if (typeof window.initMap === 'function') {
                            window.initMap();
                            return;
                        }
                        if (Date.now() - started < 8000) {
                            setTimeout(tryInit, 40);
                        }
                    })();
                };
            </script>
            <script src="https://maps.googleapis.com/maps/api/js?key={{ $sys_settings['google_maps_key'] }}&libraries=places&callback=__bootGoogleMap" async defer></script>
        @endif
    @endif

    @include('partials.ui.a11y-enhancements')

    <!-- Popup Ad Modal -->
    @if(($sys_settings['popup_enabled'] ?? '0') == '1')
    <div class="modal fade" id="promoPopup" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content border-0 shadow-lg overflow-hidden rounded-4">
                <div class="modal-header border-0 pb-0 position-absolute end-0 top-0" style="z-index: 10;">
                    <button type="button" class="btn-close bg-white shadow-sm rounded-circle p-2" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-0">
                    <div class="row g-0">
                        @if(!empty($sys_settings['popup_image']))
                        <div class="col-md-12">
                            <a href="{{ $sys_settings['popup_link'] ?? '#' }}" target="_blank" rel="noopener noreferrer">
                                <img src="{{ asset($sys_settings['popup_image']) }}" alt="{{ $sys_settings['popup_title'] ?? 'Promotion' }}" class="img-fluid w-100 h-100 object-fit-cover" width="800" height="450" loading="lazy" decoding="async" style="min-height: 300px;">
                            </a>
                        </div>
                        @endif
                        @if(!empty($sys_settings['popup_title']))
                        <div class="col-md-12 p-4 text-center bg-white">
                            <h4 class="fw-bold mb-3">{{ $sys_settings['popup_title'] }}</h4>
                            @if(!empty($sys_settings['popup_link']))
                            <a href="{{ $sys_settings['popup_link'] }}" class="btn btn-brand px-5 py-2 rounded-pill fw-bold">Explore Now</a>
                            @endif
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const el = document.getElementById('promoPopup');
            if (!el || typeof bootstrap === 'undefined') return;
            const myModal = new bootstrap.Modal(el);
            myModal.show();
        });
    </script>
    <style>
        #promoPopup .btn-close {
            opacity: 1;
            margin-right: 15px;
            margin-top: 15px;
        }
    </style>
    @endif
</body>
</html>
