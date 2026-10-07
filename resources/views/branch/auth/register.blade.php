@extends('layouts.app')

@section('title', 'Branch Registration - Doonspedo')

@section('body_class', 'clean-login-bg')

@section('content')
<div class="login-container container d-flex align-items-center justify-content-center" style="min-height: 100vh;" id="main-content">
    <div class="row justify-content-center w-100 py-5">
        <div class="col-lg-8">
            <div class="clean-card p-4 p-md-5">
                <div class="text-center mb-5">
                    <div class="mb-3">
                        @include('partials.ui.brand-logo', ['size' => 'md'])
                    </div>
                    <h1 class="h2 text-dark-custom fw-bold mb-1 display-6">Branch Registration</h1>
                    <p class="text-muted fs-5">Register your branch to join our network</p>
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

                <form action="{{ route('branch.register.submit') }}" method="POST">
                    @csrf
                    <div class="row g-4">
                        <div class="col-md-6">
                            <label class="form-label text-dark-custom small fw-bold" for="branch-name">Branch Name</label>
                            <input type="text" id="branch-name" name="name" class="form-control clean-input" placeholder="Enter branch name" required value="{{ old('name') }}" autocomplete="organization">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label text-dark-custom small fw-bold" for="branch-email">Email Address</label>
                            <input type="email" id="branch-email" name="email" class="form-control clean-input" placeholder="branch@example.com" required value="{{ old('email') }}" autocomplete="email">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label text-dark-custom small fw-bold" for="branch-phone">Phone Number</label>
                            <input type="tel" id="branch-phone" name="phone" class="form-control clean-input" placeholder="Phone number" required value="{{ old('phone') }}" autocomplete="tel">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label text-dark-custom small fw-bold" for="branch-reg-login-id">Login ID</label>
                            <input type="text" id="branch-reg-login-id" name="login_id" class="form-control clean-input" placeholder="Choose a login ID" required value="{{ old('login_id') }}" autocomplete="username">
                        </div>

                        <div class="col-12">
                            <label class="form-label text-dark-custom small fw-bold" for="branch-address">Address</label>
                            <textarea id="branch-address" name="address" class="form-control clean-input" placeholder="Branch full address" required rows="2" autocomplete="street-address">{{ old('address') }}</textarea>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label text-dark-custom small fw-bold" for="branch-reg-password">Password</label>
                            <input type="password" id="branch-reg-password" name="password" class="form-control clean-input" placeholder="••••••••" required autocomplete="new-password">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label text-dark-custom small fw-bold" for="branch-reg-password-confirm">Confirm Password</label>
                            <input type="password" id="branch-reg-password-confirm" name="password_confirmation" class="form-control clean-input" placeholder="••••••••" required autocomplete="new-password">
                        </div>

                        <div class="col-12 mt-4">
                            <button type="submit" class="btn btn-clean w-100 py-3 shadow active-scale">
                                REGISTER BRANCH <i class="bi bi-check2-circle ms-2" aria-hidden="true"></i>
                            </button>
                        </div>
                    </div>
                </form>

                <div class="text-center mt-4">
                    <p class="text-muted small mb-3">Already registered? <a href="{{ route('branch.login') }}" class="text-dark-custom text-decoration-none fw-bold">Login</a></p>
                    <a href="/" class="text-muted text-decoration-none small">← Back to website</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
