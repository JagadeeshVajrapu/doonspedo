{{--
  Authenticated customer bottom navigation.
  Home, Book Ride, My Rides, and Profile use existing routes only.
--}}
@php
    $active = $active ?? '';
    if ($active === 'activity') {
        $active = 'rides';
    } elseif ($active === 'account') {
        $active = 'profile';
    }
    if ($active === '') {
        if (request()->routeIs('rider.app')) {
            $active = 'book';
        } elseif (request()->routeIs('rider.bookings.*')) {
            $active = 'rides';
        } elseif (request()->routeIs('profile.*')) {
            $active = 'profile';
        } elseif (request()->is('/')) {
            $active = 'home';
        }
    }
@endphp
<nav class="rider-bottom-nav apk-bottom-nav" aria-label="Customer navigation">
    <div class="rider-bottom-nav-row">
        <a href="{{ route('rider.app') }}" class="rider-nav-item {{ $active === 'book' ? 'is-active' : '' }}" @if($active === 'book') aria-current="page" @endif>
            <i class="bi bi-geo-alt{{ $active === 'book' ? '-fill' : '' }}" aria-hidden="true"></i>
            <span>Book Ride</span>
        </a>
        <a href="{{ route('rider.bookings.index') }}" class="rider-nav-item {{ $active === 'rides' ? 'is-active' : '' }}" @if($active === 'rides') aria-current="page" @endif>
            <i class="bi bi-clock-history" aria-hidden="true"></i>
            <span>My Rides</span>
        </a>
        <a href="{{ route('profile.edit') }}" class="rider-nav-item {{ $active === 'profile' ? 'is-active' : '' }}" @if($active === 'profile') aria-current="page" @endif>
            <i class="bi bi-person{{ $active === 'profile' ? '-fill' : '' }}" aria-hidden="true"></i>
            <span>Profile</span>
        </a>
    </div>
</nav>
