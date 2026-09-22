<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Admin - Doonspedo')</title>
    <link rel="preconnect" href="https://cdn.jsdelivr.net" crossorigin>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600;700&display=swap" rel="stylesheet">
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

    @yield('styles')
</head>
<body class="admin-shell">
    <a class="ds-skip-link" href="#main-content">Skip to main content</a>
@if(!isset($noSidebar))
    {{-- Mobile drawer --}}
    <div class="offcanvas offcanvas-start admin-offcanvas sidebar ds-sidebar" tabindex="-1" id="adminMobileMenu" aria-labelledby="adminMobileMenuLabel" aria-modal="true" role="dialog">
        <div class="offcanvas-header border-bottom border-secondary border-opacity-25">
            <h2 class="offcanvas-title h5 text-brand fw-bold mb-0" id="adminMobileMenuLabel">
                {{ auth('branch')->check() ? 'Branch Menu' : 'Admin Menu' }}
            </h2>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas" aria-label="Close menu"></button>
        </div>
        <div class="offcanvas-body p-0">
            @include('backend.inc.sidebar', ['sidebarVariant' => 'mobile'])
        </div>
    </div>
@endif

    <div class="d-flex">
        @if(!isset($noSidebar))
            <div class="d-none d-lg-flex">
                @include('backend.inc.sidebar', ['sidebarVariant' => 'desktop'])
            </div>
        @endif

        <div class="admin-main flex-grow-1 min-vh-100 d-flex flex-column" id="main-content">
            @if(!isset($noSidebar))
                <div class="admin-content flex-grow-1">
                    <div class="admin-mobile-bar">
                        <div>
                            <div class="fw-bold text-dark">{{ $sys_settings['app_name'] ?? 'Doonspedo' }}</div>
                            <div class="extra-small text-muted">{{ auth('branch')->check() ? 'Branch panel' : 'Operations panel' }}</div>
                        </div>
                        <button class="btn btn-dark rounded-circle p-2 shadow-sm" type="button"
                                data-bs-toggle="offcanvas" data-bs-target="#adminMobileMenu"
                                aria-controls="adminMobileMenu" aria-expanded="false" aria-label="Open navigation">
                            <i class="bi bi-list fs-4" aria-hidden="true"></i>
                        </button>
                    </div>
                    @include('backend.inc.header')
                    @yield('content')
                </div>
                @include('backend.inc.footer')
            @else
                @yield('content')
            @endif
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    @yield('scripts')
    @include('partials.ui.a11y-enhancements')
</body>
</html>
