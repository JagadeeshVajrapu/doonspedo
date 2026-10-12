@extends('layouts.app')

@section('title', 'Login - Doonspedo')
@section('body_class', 'clean-login-bg')

@section('content')
<div class="login-container container d-flex align-items-center justify-content-center" style="min-height: 100vh;" id="main-content">
    <div class="row justify-content-center w-100 py-5">
        <div class="col-md-5 col-lg-4">
            <div class="clean-card p-4 p-md-5">
                <div class="text-center mb-4">
                    <div class="mb-3">
                        @include('partials.ui.brand-logo', ['size' => 'md'])
                    </div>
                    <h1 class="h2 text-dark-custom fw-bold mb-1">Welcome</h1>
                    <p class="text-muted small mb-0">Choose how you want to continue</p>
                </div>

                <div class="d-grid gap-3">
                    <a href="{{ route('login') }}" class="btn btn-clean py-3 shadow active-scale">
                        <i class="bi bi-person me-2" aria-hidden="true"></i> Customer Login
                    </a>
                    <a href="{{ route('driver.login') }}" class="btn btn-outline-dark py-3 rounded-pill fw-bold">
                        <i class="bi bi-briefcase me-2" aria-hidden="true"></i> Partner Login
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
