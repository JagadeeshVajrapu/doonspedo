@extends('layouts.app')

@section('title', 'Verify OTP - Doonspedo')

@section('body_class', 'clean-login-bg')

@section('content')
<div class="login-container container d-flex align-items-center justify-content-center" style="min-height: 100vh;">
    <div class="row justify-content-center w-100 py-5">
        <div class="col-md-5 col-lg-4">
            <div class="clean-card p-4 p-md-5">
                <div class="text-center mb-4">
                    <div class="mb-3">
                        @include('partials.ui.brand-logo', ['size' => 'md'])
                    </div>
                    <h2 class="text-dark-custom fw-bold mb-1">Verify OTP</h2>
                    <p class="text-muted small">Sent to <strong>{{ session('mobile') }}</strong></p>
                </div>

                @if(session('error'))
                    <div class="alert alert-danger border-0 bg-danger bg-opacity-10 text-danger rounded-3 small mb-3" role="alert">
                        {{ session('error') }}
                    </div>
                @endif
                @if(session('success'))
                    <div class="alert alert-success border-0 bg-success bg-opacity-10 text-success rounded-3 small mb-3" role="alert">
                        {{ session('success') }}
                    </div>
                @endif

                <form action="{{ route('driver.login.verifyOtp') }}" method="POST">
                    @csrf
                    <div class="mb-4 text-center">
                        <label class="form-label text-dark-custom small fw-bold d-block mb-3">Enter 6-Digit OTP</label>
                        <input type="text" name="otp" class="form-control clean-input text-center fs-2 fw-bold tracking-widest p-3" 
                               maxlength="6" placeholder="000000" required autofocus autocomplete="one-time-code">
                        <input type="hidden" name="mobile" value="{{ session('mobile') }}">
                    </div>

                    <div class="d-grid">
                        <button type="submit" class="btn btn-clean py-3 shadow active-scale mb-4">
                            VERIFY & LOGIN <i class="bi bi-shield-lock ms-2"></i>
                        </button>
                    </div>
                </form>

                <div class="text-center">
                    <p class="text-muted small mb-3">Didn't receive code?</p>
                    <form action="{{ route('driver.login.sendOtp') }}" method="POST">
                        @csrf
                        <input type="hidden" name="mobile" value="{{ session('mobile') }}">
                        <input type="hidden" name="branch_id" value="{{ session('branch_id') }}">
                        <button type="submit" class="btn btn-link text-dark-custom text-decoration-none fw-bold p-0 small">Resend OTP</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.tracking-widest {
    letter-spacing: 0.3em;
}
</style>
@endsection
