@extends('layouts.app')

@section('title', 'Verify OTP - Doonspedo')
@section('body_class', 'rider-auth-page')

@section('content')
<div class="rider-auth-card mx-auto">
    <div class="text-center mb-4">
        <a href="{{ url('/') }}">
            <img src="{{ asset($sys_settings['app_logo'] ?? 'uploads/logo/logo.webp') }}" alt="{{ $sys_settings['app_name'] ?? 'Doonspedo' }}" class="auth-logo mb-3">
        </a>
        <h1 class="h3 fw-bold mb-1 text-dark-custom">Verify OTP</h1>
        <p class="text-muted small mb-0">Sent to <strong>{{ session('rider_mobile') }}</strong></p>
    </div>

    @if(session('error'))
        <div class="alert alert-danger border-0 bg-danger bg-opacity-10 text-danger rounded-3 small mb-3" role="alert">
            {{ session('error') }}
        </div>
    @endif

    <form action="{{ route('login.verifyOtp') }}" method="POST">
        @csrf
        <div class="mb-4 text-center">
            <label class="form-label text-dark-custom small fw-bold d-block mb-3" for="otp-input">Enter 6-Digit OTP</label>
            <input type="text" id="otp-input" name="otp" class="form-control clean-input text-center fs-2 fw-bold tracking-widest p-3"
                   maxlength="6" placeholder="000000" required autofocus autocomplete="one-time-code" inputmode="numeric">
            <input type="hidden" name="mobile" value="{{ session('rider_mobile') }}">
        </div>

        <div class="d-grid">
            <button type="submit" class="btn btn-clean py-3 shadow active-scale mb-3">
                Verify &amp; Login <i class="bi bi-shield-lock ms-2" aria-hidden="true"></i>
            </button>
        </div>
    </form>

    <div class="text-center">
        <p class="text-muted small mb-2">Didn't receive code?</p>
        <form action="{{ route('login.sendOtp') }}" method="POST">
            @csrf
            <input type="hidden" name="mobile" value="{{ session('rider_mobile') }}">
            <button type="submit" class="btn btn-link text-dark-custom text-decoration-none fw-bold p-0 small">Resend OTP</button>
        </form>
        <div class="mt-4">
            <p class="text-muted small mb-0">Didn't receive code? Use Resend OTP above.</p>
        </div>
    </div>
</div>

<style>
.tracking-widest { letter-spacing: 0.3em; }
</style>
@endsection
