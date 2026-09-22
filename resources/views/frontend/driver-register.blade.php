@extends('layouts.app')

@section('title', 'Register as Partner - Doonspedo')

@section('body_class', 'clean-login-bg')

@section('content')
<div class="login-container container d-flex align-items-center justify-content-center" style="min-height: 100vh;">
    <div class="row justify-content-center w-100 py-5">
        <div class="col-lg-8">
            <div class="clean-card p-4 p-md-5">
                <div class="text-center mb-5">
                    <a href="/">
                        <img src="{{ asset($sys_settings['app_logo'] ?? 'uploads/logo/logo.webp') }}" alt="Logo" class="img-fluid mb-3" style="max-height: 80px;">
                    </a>
                    <h2 class="text-dark-custom fw-bold mb-1 display-6">Partner With Us</h2>
                    <p class="text-muted fs-5">Join our fleet of professional partners and start earning today.</p>
                </div>

                @if(session('success'))
                    <div class="alert alert-success rounded-3 border-0 bg-success bg-opacity-10 text-success mb-4">
                        <i class="bi bi-check-circle me-2"></i> {{ session('success') }}
                    </div>
                @endif

                <form action="{{ route('driver.register.submit') }}" method="POST">
                    @csrf

                    <div class="row g-4">
                        <div class="col-12">
                            <label class="form-label text-dark-custom small fw-bold">Assign Branch</label>
                            <select name="branch_id" class="form-select clean-input" required>
                                <option value="" disabled selected>Select Branch</option>
                                @foreach($branches as $branch)
                                    <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label text-dark-custom small fw-bold">Full Name</label>
                            <input type="text" name="name" class="form-control clean-input" placeholder="Your full name" required>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label text-dark-custom small fw-bold">Mobile Number</label>
                            <input type="tel" name="mobile" class="form-control clean-input" placeholder="+91 00000 00000" required>
                        </div>

                        <div class="col-12">
                            <label class="form-label text-dark-custom small fw-bold">Email Address</label>
                            <input type="email" name="email" class="form-control clean-input" placeholder="you@example.com" required>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label text-dark-custom small fw-bold">Vehicle Type</label>
                            <select name="vehicle_type" class="form-select clean-input" required>
                                <option value="" disabled selected>Select vehicle</option>
                                @foreach($categories as $category)
                                    <option value="{{ strtolower($category->name) }}">{{ $category->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label text-dark-custom small fw-bold">Vehicle Number</label>
                            <input type="text" name="vehicle_number" class="form-control clean-input" placeholder="DL 01 AB 1234" required>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label text-dark-custom small fw-bold">Driving License</label>
                            <input type="text" name="license_number" class="form-control clean-input" placeholder="License number" required>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label text-dark-custom small fw-bold">City</label>
                            <input type="text" name="city" class="form-control clean-input" placeholder="Your city" required>
                        </div>

                        <div class="col-12 mt-4">
                            <button type="submit" class="btn btn-clean w-100 py-3 shadow active-scale">
                                SUBMIT APPLICATION <i class="bi bi-send ms-2"></i>
                            </button>
                        </div>
                    </div>
                </form>

                <div class="text-center mt-5">
                    <p class="text-muted small mb-3">Already a partner? <a href="{{ route('driver.login') }}" class="text-dark-custom text-decoration-none fw-bold">Login here</a></p>
                    <a href="/" class="text-muted text-decoration-none small">â† Back to website</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

