<button type="button" class="app-drawer-toggle" data-bs-toggle="offcanvas" data-bs-target="#appDrawer" aria-controls="appDrawer" aria-label="Open menu">
    <i class="bi bi-list fs-4" aria-hidden="true"></i>
</button>

<div class="offcanvas offcanvas-start" tabindex="-1" id="appDrawer" aria-labelledby="appDrawerLabel">
    <div class="offcanvas-header border-bottom">
        <h2 class="offcanvas-title h5 fw-bold mb-0" id="appDrawerLabel">Doonspedo</h2>
        <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close menu"></button>
    </div>
    <nav class="offcanvas-body" aria-label="App menu">
        <ul class="list-unstyled d-grid gap-1 mb-0">
            @if(session()->has('driver_id'))
                <li><a class="app-drawer-link {{ request()->routeIs('driver.rides.*') ? 'is-active' : '' }}" href="{{ route('driver.rides.index') }}"><i class="bi bi-briefcase" aria-hidden="true"></i> Rides</a></li>
                <li><a class="app-drawer-link {{ request()->routeIs('driver.wallet*') ? 'is-active' : '' }}" href="{{ route('driver.wallet') }}"><i class="bi bi-wallet2" aria-hidden="true"></i> Wallet</a></li>
                <li><a class="app-drawer-link {{ request()->routeIs('driver.earnings') ? 'is-active' : '' }}" href="{{ route('driver.earnings') }}"><i class="bi bi-graph-up-arrow" aria-hidden="true"></i> Earnings</a></li>
                <li><a class="app-drawer-link {{ request()->routeIs('driver.profile') ? 'is-active' : '' }}" href="{{ route('driver.profile') }}"><i class="bi bi-person" aria-hidden="true"></i> Profile</a></li>
                <li>
                    <form action="{{ route('driver.logout') }}" method="POST">
                        @csrf
                        <button type="submit" class="app-drawer-link app-drawer-link--button text-danger"><i class="bi bi-box-arrow-right" aria-hidden="true"></i> Logout</button>
                    </form>
                </li>
            @elseif(auth()->check())
                <li><a class="app-drawer-link {{ request()->routeIs('rider.app') ? 'is-active' : '' }}" href="{{ route('rider.app') }}"><i class="bi bi-geo-alt" aria-hidden="true"></i> Book Ride</a></li>
                <li><a class="app-drawer-link {{ request()->routeIs('rider.bookings.*') ? 'is-active' : '' }}" href="{{ route('rider.bookings.index') }}"><i class="bi bi-clock-history" aria-hidden="true"></i> My Rides</a></li>
                <li><a class="app-drawer-link {{ request()->routeIs('profile.*') ? 'is-active' : '' }}" href="{{ route('profile.edit') }}"><i class="bi bi-person" aria-hidden="true"></i> Profile</a></li>
                <li>
                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button type="submit" class="app-drawer-link app-drawer-link--button text-danger"><i class="bi bi-box-arrow-right" aria-hidden="true"></i> Logout</button>
                    </form>
                </li>
            @else
                <li><a class="app-drawer-link {{ request()->is('/') ? 'is-active' : '' }}" href="{{ url('/') }}"><i class="bi bi-house" aria-hidden="true"></i> Home</a></li>
                <li><a class="app-drawer-link {{ request()->routeIs('login') ? 'is-active' : '' }}" href="{{ route('login') }}"><i class="bi bi-person" aria-hidden="true"></i> Customer Login</a></li>
                <li><a class="app-drawer-link {{ request()->routeIs('driver.login') ? 'is-active' : '' }}" href="{{ route('driver.login') }}"><i class="bi bi-briefcase" aria-hidden="true"></i> Partner Login</a></li>
                <li><a class="app-drawer-link {{ request()->routeIs('driver.register') ? 'is-active' : '' }}" href="{{ route('driver.register') }}"><i class="bi bi-people" aria-hidden="true"></i> Partner with Us</a></li>
                <li><a class="app-drawer-link" href="{{ url('/#services') }}"><i class="bi bi-grid" aria-hidden="true"></i> Services</a></li>
                <li><a class="app-drawer-link" href="{{ url('/#about') }}"><i class="bi bi-info-circle" aria-hidden="true"></i> About</a></li>
                <li><a class="app-drawer-link" href="{{ url('/#faq') }}"><i class="bi bi-question-circle" aria-hidden="true"></i> FAQ</a></li>
                <li><a class="app-drawer-link" href="{{ url('/#contact') }}"><i class="bi bi-envelope" aria-hidden="true"></i> Contact</a></li>
            @endif
        </ul>
    </nav>
</div>
<script>
(function () {
    var drawer = document.getElementById('appDrawer');
    if (!drawer || drawer.dataset.bound) return;
    drawer.dataset.bound = '1';
    var ignoreHide = false;

    drawer.addEventListener('shown.bs.offcanvas', function () {
        if (!(history.state && history.state.appDrawer)) {
            history.pushState({ appDrawer: 1 }, '');
        }
    });

    drawer.addEventListener('hide.bs.offcanvas', function () {
        if (ignoreHide) return;
        if (history.state && history.state.appDrawer) {
            ignoreHide = true;
            history.back();
        }
    });

    window.addEventListener('popstate', function () {
        ignoreHide = true;
        if (drawer.classList.contains('show')) {
            var instance = window.bootstrap && bootstrap.Offcanvas.getInstance(drawer);
            if (instance) instance.hide();
        }
        ignoreHide = false;
    });

    drawer.querySelectorAll('a').forEach(function (link) {
        link.addEventListener('click', function () {
            ignoreHide = true;
            if (history.state && history.state.appDrawer) {
                history.replaceState({}, '');
            }
            var instance = window.bootstrap && bootstrap.Offcanvas.getInstance(drawer);
            if (instance) instance.hide();
        });
    });
})();
</script>
