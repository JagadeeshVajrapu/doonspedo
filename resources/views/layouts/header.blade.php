{{-- Public site navbar — preserves auth/role links and existing routes --}}
<nav class="navbar navbar-expand-lg navbar-light public-nav sticky-top py-3" aria-label="Primary">
    @if(session('error'))
        <div class="position-absolute w-100" style="top: 0; left: 0; z-index: 1050;">
            <div class="alert alert-danger bg-danger text-white border-0 text-center m-0 rounded-0 fs-6 py-2 shadow-sm" role="alert">
                <i class="bi bi-exclamation-triangle-fill me-2" aria-hidden="true"></i> {{ session('error') }}
                <button type="button" class="btn-close btn-close-white float-end" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        </div>
    @endif

    <div class="container">
        <a class="navbar-brand fw-bold fs-3 mb-0" href="{{ url('/') }}">
            @if(!empty($sys_settings['app_logo']))
                <img class="public-logo" src="{{ asset($sys_settings['app_logo']) }}" alt="{{ $sys_settings['app_name'] ?? 'Doonspedo' }}" width="148" height="44" decoding="async">
            @else
                <span class="text-brand">{{ $sys_settings['app_name'] ?? 'Doonspedo' }}</span>
            @endif
        </a>

        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
                aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav mx-lg-auto align-items-lg-center mt-3 mt-lg-0">
                <li class="nav-item">
                    <a class="nav-link {{ request()->is('/') ? 'is-active' : '' }}" href="{{ url('/') }}" @if(request()->is('/')) aria-current="page" @endif>Home</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ url('/#services') }}">Services</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ url('/#about') }}">About</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ url('/#faq') }}">FAQ</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ url('/#contact') }}">Contact</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('driver.register') }}">Partner with Us</a>
                </li>
            </ul>

            <ul class="navbar-nav align-items-lg-center gap-lg-2 mt-2 mt-lg-0">
                @if(auth('admin')->check())
                    <li class="nav-item">
                        <a class="nav-link text-warning fw-bold" href="{{ route('admin.dashboard') }}">Admin Dashboard</a>
                    </li>
                @elseif(session()->has('driver_id'))
                    <li class="nav-item">
                        <a class="nav-link text-brand fw-bold" href="{{ route('driver.dashboard') }}">Driver Dashboard</a>
                    </li>
                @elseif(auth()->check())
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false" aria-haspopup="true" id="riderAccountMenu">
                            {{ auth()->user()->name }}
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end shadow" aria-labelledby="riderAccountMenu">
                            <li><a class="dropdown-item" href="{{ route('rider.app') }}">My Rides</a></li>
                            <li><a class="dropdown-item" href="{{ route('profile.edit') }}">Profile</a></li>
                            <li><hr class="dropdown-divider border-secondary"></li>
                            <li>
                                <form action="{{ route('logout') }}" method="POST">
                                    @csrf
                                    <button type="submit" class="dropdown-item text-danger">Logout</button>
                                </form>
                            </li>
                        </ul>
                    </li>
                @else
                    <li class="nav-item">
                        <a class="nav-link fw-semibold" href="{{ route('login') }}">Customer Login</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link fw-semibold" href="{{ route('driver.login') }}">Partner Login</a>
                    </li>
                @endif

                <li class="nav-item">
                    <a class="btn btn-brand btn-book-cta px-4" href="{{ route('rider.app') }}">Book Now</a>
                </li>
            </ul>
        </div>
    </div>
</nav>
