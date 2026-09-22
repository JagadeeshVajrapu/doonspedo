@extends('layouts.app')

@section('title', 'Partner Login - Doonspedo')

@section('body_class', 'clean-login-bg')

@section('content')
<div class="login-container container d-flex align-items-center justify-content-center" style="min-height: 100vh;" id="main-content">
    <div class="row justify-content-center w-100 py-5">
        <div class="col-md-5 col-lg-4">
            <div class="clean-card p-4 p-md-5">
                <div class="text-center mb-4">
                    <a href="/">
                        <img src="{{ asset($sys_settings['app_logo'] ?? 'uploads/logo/logo.webp') }}" alt="{{ $sys_settings['app_name'] ?? 'Doonspedo' }}" class="img-fluid mb-3" style="max-height: 60px;">
                    </a>
                    <h1 class="h2 text-dark-custom fw-bold mb-1">Partner Login</h1>
                    <p class="text-muted small">Enter your registered mobile number to continue</p>
                </div>

                @if(session('error'))
                    <div class="alert alert-danger border-0 bg-danger bg-opacity-10 text-danger rounded-3 small mb-3" role="alert">
                        {{ session('error') }}
                    </div>
                @endif

                <form action="{{ route('driver.login.sendOtp') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label text-dark-custom small fw-bold" for="driver-branch">Select Branch</label>
                        <select id="driver-branch" name="branch_id" class="form-select clean-input" required>
                            <option value="" disabled selected>Choose Branch</option>
                            @forelse($branches as $branch)
                                <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                            @empty
                                <option value="" disabled>No branches available yet</option>
                            @endforelse
                        </select>
                        <div class="form-text small text-muted mt-1">Select your registered partner branch to continue.</div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label text-dark-custom small fw-bold" for="driver-mobile">Mobile Number</label>
                        <input type="tel" id="driver-mobile" name="mobile" class="form-control clean-input" placeholder="+91 00000 00000" required autocomplete="tel">
                    </div>

                    <div class="d-grid mt-4">
                        <button type="submit" class="btn btn-clean py-3 shadow active-scale">
                            Partner Login <i class="bi bi-arrow-right ms-2" aria-hidden="true"></i>
                        </button>
                    </div>
                </form>

                <div class="text-center mt-4">
                    <p class="text-muted small mb-3">Don't have an account? <a href="{{ route('driver.register') }}" class="text-dark-custom text-decoration-none fw-bold">Register Now</a></p>
                    <a href="{{ url('/') }}" class="text-muted text-decoration-none small">← Back to website</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
