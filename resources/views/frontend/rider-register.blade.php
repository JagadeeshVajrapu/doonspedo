@extends('layouts.app')

@section('title', 'Customer Registration - Doonspedo')
@section('body_class', 'rider-auth-page')

@section('content')
<div class="rider-auth-card mx-auto">
    <div class="text-center mb-4">
        <a href="{{ url('/') }}">
            <span class="d-inline-block mb-3">@include('partials.ui.brand-logo', ['size' => 'md'])</span>
        </a>
        <h1 class="h3 fw-bold mb-1 text-dark-custom">Create account</h1>
        <p class="text-muted small mb-0">Join Doonspedo and start booking rides</p>
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

    <form action="{{ route('register.submit') }}" method="POST">
        @csrf
        <div class="mb-3">
            <label class="form-label text-dark-custom small fw-bold" for="reg-name">Full Name</label>
            <input type="text" id="reg-name" name="name" class="form-control clean-input" placeholder="Enter your name" required value="{{ old('name') }}" autocomplete="name">
        </div>

        <div class="mb-3">
            <label class="form-label text-dark-custom small fw-bold" for="reg-email">Email Address</label>
            <input type="email" id="reg-email" name="email" class="form-control clean-input" placeholder="name@example.com" required value="{{ old('email') }}" autocomplete="email">
        </div>

        <div class="mb-3">
            <label class="form-label text-dark-custom small fw-bold" for="reg-mobile">Mobile Number</label>
            <input type="tel" id="reg-mobile" name="mobile" class="form-control clean-input" placeholder="+91 00000 00000" required value="{{ old('mobile') }}" autocomplete="tel">
        </div>

        <div class="mb-3">
            <label class="form-label text-dark-custom small fw-bold" for="reg-password">Password</label>
            <input type="password" id="reg-password" name="password" class="form-control clean-input" placeholder="At least 8 characters" required minlength="8" autocomplete="new-password">
        </div>

        <div class="mb-4">
            <label class="form-label text-dark-custom small fw-bold" for="reg-password-confirm">Confirm Password</label>
            <input type="password" id="reg-password-confirm" name="password_confirmation" class="form-control clean-input" placeholder="Repeat your password" required minlength="8" autocomplete="new-password">
        </div>

        <div class="d-grid mt-4">
            <button type="submit" class="btn btn-clean py-3 shadow active-scale mb-3">
                Create Account <i class="bi bi-person-plus ms-2" aria-hidden="true"></i>
            </button>
        </div>
    </form>

    <div class="text-center">
        <p class="text-muted small mb-0">Already have an account? <a href="{{ route('login') }}" class="text-dark-custom text-decoration-none fw-bold">Login</a></p>
    </div>
</div>
@endsection
