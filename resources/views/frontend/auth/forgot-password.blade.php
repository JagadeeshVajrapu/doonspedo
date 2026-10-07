@extends('layouts.app')

@section('title', 'Forgot Password - Doonspedo')
@section('body_class', 'rider-auth-page')

@section('content')
<div class="rider-auth-card mx-auto" id="main-content">
    <div class="text-center mb-4">
        <a href="{{ url('/') }}">
            <span class="d-inline-block mb-3">@include('partials.ui.brand-logo', ['size' => 'md'])</span>
        </a>
        <h1 class="h3 fw-bold mb-1 text-dark-custom">Reset password</h1>
        <p class="text-muted small mb-0">Enter your email and we'll send a reset link.</p>
    </div>

    @if(session('status'))
        <div class="alert alert-success border-0 bg-success bg-opacity-10 text-success rounded-3 small mb-4" role="alert">
            {{ session('status') }}
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger border-0 bg-danger bg-opacity-10 text-danger rounded-3 small mb-4" role="alert">
            <ul class="mb-0 ps-3">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('password.email') }}" method="POST">
        @csrf
        <div class="mb-4">
            <label class="form-label text-dark-custom small fw-bold" for="forgot-email">Email Address</label>
            <input type="email" id="forgot-email" name="email" class="form-control clean-input" placeholder="your@email.com" required value="{{ old('email') }}" autocomplete="email">
        </div>

        <button type="submit" class="btn btn-clean w-100 py-3 fw-bold shadow active-scale mb-3">
            Send Reset Link <i class="bi bi-send ms-2" aria-hidden="true"></i>
        </button>
    </form>

    <div class="text-center">
        <a href="{{ route('login') }}" class="text-muted text-decoration-none small fw-bold">
            <i class="bi bi-arrow-left me-1" aria-hidden="true"></i> Back to Login
        </a>
    </div>
</div>
@endsection
