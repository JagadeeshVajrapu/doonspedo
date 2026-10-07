@extends('layouts.app')

@section('title', 'Branch Login - Doonspedo')

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
                    <h1 class="h2 text-dark-custom fw-bold mb-1">Branch Portal</h1>
                    <p class="text-muted small">Enter branch login ID and password</p>
                </div>

                @if($errors->any())
                    <div class="alert alert-danger border-0 bg-danger bg-opacity-10 text-danger rounded-3 small mb-3" role="alert">
                        {{ $errors->first() }}
                    </div>
                @endif

                <form action="{{ route('branch.login.submit') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label text-dark-custom small fw-bold" for="branch-login-id">Branch Login ID</label>
                        <input type="text" id="branch-login-id" name="login_id" class="form-control clean-input" placeholder="Enter branch ID" required autocomplete="username">
                    </div>

                    <div class="mb-3">
                        <label class="form-label text-dark-custom small fw-bold" for="branch-password">Password</label>
                        <input type="password" id="branch-password" name="password" class="form-control clean-input" placeholder="Enter password" required autocomplete="current-password">
                    </div>

                    <div class="d-grid mt-4">
                        <button type="submit" class="btn btn-clean py-3 shadow active-scale mb-4">
                            LOGIN NOW <i class="bi bi-box-arrow-in-right ms-2"></i>
                        </button>
                    </div>
                </form>

                <div class="text-center">
                    <p class="text-muted small mb-0">Don't have a branch? <a href="{{ route('branch.register') }}" class="text-dark-custom text-decoration-none fw-bold">Register Now</a></p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
