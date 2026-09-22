@extends('layouts.app')

@section('title', 'Rider Login - Doonspedo')
@section('body_class', 'clean-login-bg')

@section('content')
<div class="login-container container d-flex align-items-center justify-content-center" style="min-height: 100vh;" id="main-content">
    <div class="row justify-content-center w-100 py-5">
        <div class="col-md-5 col-lg-4">
            <div class="clean-card p-4 p-md-5">
                <div class="text-center mb-4">
                    <a href="{{ url('/') }}">
                        <img src="{{ asset($sys_settings['app_logo'] ?? 'uploads/logo/logo.webp') }}" alt="{{ $sys_settings['app_name'] ?? 'Doonspedo' }}" class="img-fluid mb-3" style="max-height: 60px;">
                    </a>
                    <h1 class="h2 text-dark-custom fw-bold mb-1">Rider Login</h1>
                    <p class="text-muted small mb-0">Sign in to book your next ride</p>
                </div>

                @if(session('error'))
                    <div class="alert alert-danger border-0 bg-danger bg-opacity-10 text-danger rounded-3 small mb-3" role="alert">
                        {{ session('error') }}
                    </div>
                @endif

                <div class="rider-auth-tabs" role="tablist" aria-label="Login method">
                    <button type="button" onclick="toggleForm('otp')" id="otp-toggle" class="btn btn-clean flex-grow-1 py-2" role="tab" aria-selected="true" aria-controls="otp-form">Mobile OTP</button>
                    <button type="button" onclick="toggleForm('password')" id="password-toggle" class="btn btn-light flex-grow-1 py-2 text-muted border-0" role="tab" aria-selected="false" aria-controls="password-form">Password</button>
                </div>

                <form id="otp-form" action="{{ route('login.sendOtp') }}" method="POST" role="tabpanel" aria-labelledby="otp-toggle">
                    @csrf
                    <div class="mb-4">
                        <label class="form-label text-dark-custom small fw-bold" for="login-mobile">Mobile Number</label>
                        <input type="tel" id="login-mobile" name="mobile" class="form-control clean-input" placeholder="+91 00000 00000" required autocomplete="tel">
                    </div>
                    <div class="d-grid">
                        <button type="submit" class="btn btn-clean py-3 shadow active-scale mb-3">
                            Send OTP <i class="bi bi-arrow-right ms-2" aria-hidden="true"></i>
                        </button>
                    </div>
                </form>

                <form id="password-form" action="{{ route('login.submit') }}" method="POST" style="display: none;" role="tabpanel" aria-labelledby="password-toggle" hidden>
                    @csrf
                    <div class="mb-3">
                        <label class="form-label text-dark-custom small fw-bold" for="login-email">Email Address</label>
                        <input type="email" id="login-email" name="email" class="form-control clean-input" placeholder="your@email.com" required autocomplete="email">
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-dark-custom small fw-bold" for="login-password">Password</label>
                        <input type="password" id="login-password" name="password" class="form-control clean-input" placeholder="••••••••" required autocomplete="current-password">
                        <div class="text-end mt-2">
                            <a href="{{ route('password.request') }}" class="text-dark-custom text-decoration-none small fw-bold">Forgot Password?</a>
                        </div>
                    </div>
                    <div class="d-grid mt-4">
                        <button type="submit" class="btn btn-clean py-3 shadow active-scale mb-3">
                            Login <i class="bi bi-arrow-right ms-2" aria-hidden="true"></i>
                        </button>
                    </div>
                </form>

                <div class="text-center">
                    <p class="text-muted small mb-3">Don't have an account? <a href="{{ route('register') }}" class="text-dark-custom text-decoration-none fw-bold">Sign Up</a></p>
                    <a href="{{ url('/') }}" class="text-muted text-decoration-none small">← Back to website</a>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    function toggleForm(type) {
        const otpForm = document.getElementById('otp-form');
        const passwordForm = document.getElementById('password-form');
        const otpToggle = document.getElementById('otp-toggle');
        const passwordToggle = document.getElementById('password-toggle');

        if (type === 'password') {
            otpForm.style.display = 'none';
            otpForm.hidden = true;
            passwordForm.style.display = 'block';
            passwordForm.hidden = false;
            passwordToggle.classList.replace('btn-light', 'btn-clean');
            passwordToggle.classList.remove('text-muted');
            passwordToggle.setAttribute('aria-selected', 'true');
            otpToggle.classList.replace('btn-clean', 'btn-light');
            otpToggle.classList.add('text-muted');
            otpToggle.setAttribute('aria-selected', 'false');
        } else {
            otpForm.style.display = 'block';
            otpForm.hidden = false;
            passwordForm.style.display = 'none';
            passwordForm.hidden = true;
            otpToggle.classList.replace('btn-light', 'btn-clean');
            otpToggle.classList.remove('text-muted');
            otpToggle.setAttribute('aria-selected', 'true');
            passwordToggle.classList.replace('btn-clean', 'btn-light');
            passwordToggle.classList.add('text-muted');
            passwordToggle.setAttribute('aria-selected', 'false');
        }
    }
</script>
@endsection
