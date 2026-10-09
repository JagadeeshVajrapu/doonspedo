{{--
  Driver mobile bottom navigation
  @param string|null $active home|rides|wallet|earnings|profile
--}}
@php $active = $active ?? ''; @endphp
<nav class="driver-bottom-nav" aria-label="Driver primary">
    <div class="driver-bottom-nav-row">
    <a href="{{ route('driver.dashboard') }}" class="driver-nav-item {{ $active === 'home' || request()->routeIs('driver.dashboard') ? 'is-active' : '' }}" @if($active === 'home' || request()->routeIs('driver.dashboard')) aria-current="page" @endif>
        <i class="bi bi-house-door{{ ($active === 'home' || request()->routeIs('driver.dashboard')) ? '-fill' : '' }}" aria-hidden="true"></i>
        <span>Home</span>
    </a>
    <a href="{{ route('driver.rides.index') }}" class="driver-nav-item {{ $active === 'rides' || request()->routeIs('driver.rides.*') ? 'is-active' : '' }}" @if($active === 'rides' || request()->routeIs('driver.rides.*')) aria-current="page" @endif>
        <i class="bi bi-briefcase{{ ($active === 'rides' || request()->routeIs('driver.rides.*')) ? '-fill' : '' }}" aria-hidden="true"></i>
        <span>Rides</span>
    </a>
    <a href="{{ route('driver.wallet') }}" class="driver-nav-item {{ $active === 'wallet' || request()->routeIs('driver.wallet*') ? 'is-active' : '' }}" @if($active === 'wallet' || request()->routeIs('driver.wallet*')) aria-current="page" @endif>
        <i class="bi bi-wallet2" aria-hidden="true"></i>
        <span>Wallet</span>
    </a>
    <a href="{{ route('driver.earnings') }}" class="driver-nav-item {{ $active === 'earnings' || request()->routeIs('driver.earnings') ? 'is-active' : '' }}" @if($active === 'earnings' || request()->routeIs('driver.earnings')) aria-current="page" @endif>
        <i class="bi bi-graph-up-arrow" aria-hidden="true"></i>
        <span>Earnings</span>
    </a>
    <a href="{{ route('driver.profile') }}" class="driver-nav-item {{ $active === 'profile' || request()->routeIs('driver.profile') ? 'is-active' : '' }}" @if($active === 'profile' || request()->routeIs('driver.profile')) aria-current="page" @endif>
        <i class="bi bi-person{{ ($active === 'profile' || request()->routeIs('driver.profile')) ? '-fill' : '' }}" aria-hidden="true"></i>
        <span>Profile</span>
    </a>
    </div>
    @include('partials.ui.developer-credit')
</nav>
