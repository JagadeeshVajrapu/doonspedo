<div class="sidebar ds-sidebar d-none d-lg-flex flex-column p-3 p-xl-4 shadow sticky-top" style="z-index: 1050; max-height: 100vh; overflow-y: auto;">
    <div class="mb-3 px-2 text-center">
        <a href="{{ route('driver.dashboard') }}" class="d-inline-block text-decoration-none">
            @if(!empty($sys_settings['app_logo']))
                @include('partials.ui.brand-logo', ['size' => 'sm'])
            @else
                <h3 class="text-brand mb-0 fw-bold fs-4">{{ $sys_settings['app_name'] ?? 'Doonspedo' }}</h3>
            @endif
        </a>
        <div class="mt-1 small text-white-50 fw-semibold">Captain Panel</div>
    </div>

    <div class="text-center mb-3 pb-3 border-bottom border-secondary border-opacity-25">
        @if($driver->profile_image)
            <img src="{{ media_url($driver->profile_image) }}" class="rounded-circle shadow-sm border border-2 border-brand mb-2" style="width: 64px; height: 64px; object-fit: cover;" alt="{{ $driver->name }}">
        @else
            <div class="bg-brand-soft rounded-circle d-flex align-items-center justify-content-center mx-auto mb-2" style="width: 64px; height: 64px;">
                <i class="bi bi-person text-brand fs-3"></i>
            </div>
        @endif
        <h6 class="fw-bold mb-0" style="color:#1c2208;">{{ $driver->name }}</h6>
        <div class="small fw-semibold mb-1" style="color:#1c2208;">Partner {{ $driver->displayReference() }}</div>
        <div class="d-flex justify-content-center gap-2 flex-wrap">
            <span class="ds-badge {{ $driver->status == 'approved' ? 'ds-badge-success' : 'ds-badge-warning' }}">{{ ucfirst($driver->status) }}</span>
            <span id="driver-presence-badge" class="ds-badge {{ $driver->is_online ? 'ds-badge-success' : 'ds-badge-neutral' }}">{{ $driver->is_online ? 'Online' : 'Offline' }}</span>
        </div>
    </div>

    <ul class="nav nav-pills flex-column mb-auto">
        <li class="nav-section-label">Overview</li>
        <li class="nav-item">
            <a href="{{ route('driver.dashboard') }}" class="nav-link {{ request()->routeIs('driver.dashboard') ? 'active' : '' }} py-2 px-3">
                <i class="bi bi-speedometer2 me-2"></i> Dashboard
            </a>
        </li>
        <li class="nav-item">
            <a href="{{ route('driver.availability') }}" class="nav-link {{ request()->routeIs('driver.availability') ? 'active' : '' }} py-2 px-3">
                <i class="bi bi-broadcast me-2"></i> Availability
            </a>
        </li>

        <li class="nav-section-label">Operations</li>
        <li class="nav-item">
            <a href="{{ route('driver.rides.index') }}" class="nav-link {{ request()->routeIs('driver.rides.*') ? 'active' : '' }} py-2 px-3">
                <i class="bi bi-briefcase me-2"></i> My Rides
            </a>
        </li>
        <li class="nav-item">
            <a href="{{ route('driver.bids.index') }}" class="nav-link {{ request()->routeIs('driver.bids.*') ? 'active' : '' }} py-2 px-3">
                <i class="bi bi-cash-stack me-2"></i> Ride Requests / Bids
            </a>
        </li>
        <li class="nav-item">
            <a href="{{ route('driver.vehicles.index') }}" class="nav-link {{ request()->routeIs('driver.vehicles.*') ? 'active' : '' }} py-2 px-3">
                <i class="bi bi-car-front me-2"></i> Vehicle
            </a>
        </li>

        <li class="nav-section-label">Finance</li>
        <li class="nav-item">
            <a href="{{ route('driver.earnings') }}" class="nav-link {{ request()->routeIs('driver.earnings') ? 'active' : '' }} py-2 px-3">
                <i class="bi bi-graph-up-arrow me-2"></i> Earnings
            </a>
        </li>
        <li class="nav-item">
            <a href="{{ route('driver.wallet') }}" class="nav-link {{ request()->routeIs('driver.wallet*') ? 'active' : '' }} py-2 px-3">
                <i class="bi bi-wallet2 me-2"></i> Wallet
            </a>
        </li>
        @if(config('taxi.subscriptions_enabled', true))
        <li class="nav-item">
            <a href="{{ route('driver.subscriptions') }}" class="nav-link {{ request()->routeIs('driver.subscriptions') ? 'active' : '' }} py-2 px-3">
                <i class="bi bi-calendar-check me-2"></i> Subscriptions
            </a>
        </li>
        @endif

        <li class="nav-section-label">Account</li>
        <li class="nav-item">
            <a href="{{ route('driver.kyc') }}" class="nav-link {{ request()->routeIs('driver.kyc') ? 'active' : '' }} py-2 px-3">
                <i class="bi bi-shield-check me-2"></i> KYC / Documents
            </a>
        </li>
        <li class="nav-item">
            <a href="{{ route('driver.profile') }}" class="nav-link {{ request()->routeIs('driver.profile') ? 'active' : '' }} py-2 px-3">
                <i class="bi bi-person-circle me-2"></i> Profile
            </a>
        </li>
        <li class="nav-item">
            <a href="{{ route('driver.notifications.index') }}" class="nav-link {{ request()->routeIs('driver.notifications.*') ? 'active' : '' }} py-2 px-3 position-relative">
                <i class="bi bi-bell me-2"></i> Notifications
                @php
                    $unreadNotifications = \App\Models\DriverNotification::where('driver_id', $driver->id)->where('is_read', false)->count();
                @endphp
                @if($unreadNotifications > 0)
                    <span class="position-absolute top-50 end-0 translate-middle-y me-3 badge rounded-pill bg-danger" style="font-size: 0.7rem;">
                        {{ $unreadNotifications }}
                    </span>
                @endif
            </a>
        </li>
        <li class="nav-item">
            <a href="{{ route('driver.ratings') }}" class="nav-link {{ request()->routeIs('driver.ratings') ? 'active' : '' }} py-2 px-3">
                <i class="bi bi-star-half me-2"></i> Ratings
            </a>
        </li>
        <li class="nav-item">
            <a href="{{ route('driver.support.index') }}" class="nav-link {{ request()->routeIs('driver.support.*') ? 'active' : '' }} py-2 px-3">
                <i class="bi bi-headset me-2"></i> Support
            </a>
        </li>
        <li class="nav-item">
            <a href="{{ route('driver.settings') }}" class="nav-link {{ request()->routeIs('driver.settings') ? 'active' : '' }} py-2 px-3">
                <i class="bi bi-gear-fill me-2"></i> Settings
            </a>
        </li>
    </ul>

    <div class="mt-3 pt-3 border-top border-secondary border-opacity-25">
        <form action="{{ route('driver.logout') }}" method="POST">
            @csrf
            <button type="submit" class="btn btn-outline-danger w-100 py-2 rounded-pill">
                <i class="bi bi-box-arrow-right me-2"></i> Logout
            </button>
        </form>
    </div>
</div>
