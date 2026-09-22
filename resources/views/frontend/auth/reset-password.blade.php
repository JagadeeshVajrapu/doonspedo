@extends('layouts.app')

@section('title', 'Reset Password - Doonspedo')
@section('body_class', 'rider-auth-page')

@section('content')
<div class="rider-auth-card mx-auto" id="main-content">
    <div class="text-center mb-4">
        <a href="{{ url('/') }}">
            <img src="{{ asset($sys_settings['app_logo'] ?? 'uploads/logo/logo.webp') }}" alt="{{ $sys_settings['app_name'] ?? 'Doonspedo' }}" class="auth-logo mb-3">
        </a>
        <h1 class="h3 fw-bold mb-1 text-dark-custom">Create new password</h1>
        <p class="text-muted small mb-0">Choose a strong password for your account.</p>
    </div>

    @if($errors->any())
        <div class="alert alert-danger border-0 bg-danger bg-opacity-10 text-danger rounded-3 small mb-4" role="alert">
            <ul class="mb-0 ps-3">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('password.update') }}" method="POST">
        @csrf
        <input type="hidden" name="token" value="{{ $token }}">

        <div class="mb-3">
            <label class="form-label text-dark-custom small fw-bold" for="reset-email">Email Address</label>
            <input type="email" id="reset-email" name="email" class="form-control clean-input" placeholder="your@email.com" required value="{{ old('email', $email) }}" autocomplete="email">
        </div>

        <div class="mb-3">
            <label class="form-label text-dark-custom small fw-bold" for="reset-password">New Password</label>
            <input type="password" id="reset-password" name="password" class="form-control clean-input" placeholder="••••••••" required autocomplete="new-password">
        </div>

        <div class="mb-4">
            <label class="form-label text-dark-custom small fw-bold" for="reset-password-confirm">Confirm New Password</label>
            <input type="password" id="reset-password-confirm" name="password_confirmation" class="form-control clean-input" placeholder="••••••••" required autocomplete="new-password">
        </div>

        <button type="submit" class="btn btn-clean w-100 py-3 fw-bold shadow active-scale mb-3">
            Apply New Password <i class="bi bi-check-circle ms-2" aria-hidden="true"></i>
        </button>
    </form>

    <div class="text-center">
        <a href="{{ route('login') }}" class="text-muted text-decoration-none small fw-bold">
            <i class="bi bi-arrow-left me-1" aria-hidden="true"></i> Back to Login
        </a>
    </div>
</div>
@endsection
