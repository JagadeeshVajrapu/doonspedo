{{--
  Rider bottom navigation
  @param string|null $active  home|activity|wallet|account
--}}
@php
    $active = $active ?? '';
@endphp
<nav class="rider-bottom-nav" aria-label="Customer navigation">
    <a href="{{ route('rider.app') }}" class="rider-nav-item {{ $active === 'home' ? 'is-active' : '' }}" @if($active === 'home') aria-current="page" @endif>
        <i class="bi bi-house-door{{ $active === 'home' ? '-fill' : '' }}" aria-hidden="true"></i>
        <span>Home</span>
    </a>
    <a href="{{ route('rider.bookings.index') }}" class="rider-nav-item {{ $active === 'activity' ? 'is-active' : '' }}" @if($active === 'activity') aria-current="page" @endif>
        <i class="bi bi-clock-history" aria-hidden="true"></i>
        <span>Activity</span>
    </a>
    <a href="{{ route('rider.wallet.index') }}" class="rider-nav-item {{ $active === 'wallet' ? 'is-active' : '' }}" @if($active === 'wallet') aria-current="page" @endif>
        <i class="bi bi-wallet2" aria-hidden="true"></i>
        <span>Wallet</span>
    </a>
    <a href="{{ route('profile.edit') }}" class="rider-nav-item {{ $active === 'account' ? 'is-active' : '' }}" @if($active === 'account') aria-current="page" @endif>
        <i class="bi bi-person{{ $active === 'account' ? '-fill' : '' }}" aria-hidden="true"></i>
        <span>Account</span>
    </a>
</nav>
