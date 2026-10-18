@extends('layouts.app')

@section('title', 'Request Account Deletion - Doonspedo')
@section('body_class', 'clean-login-bg')

@section('content')
@include('layouts.header')
<div class="container py-5" id="main-content">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="clean-card p-4 p-md-5">
                <div class="text-center mb-4">
                    @include('partials.ui.brand-logo', ['size' => 'md'])
                    <h1 class="h2 fw-bold mt-3 mb-2">Request Account Deletion</h1>
                    <p class="text-muted mb-0">Submit a verified request to delete your Doonspedo customer or partner account.</p>
                </div>

                <div class="alert alert-light border small mb-4">
                    <p class="mb-2"><strong>Important:</strong> Submitting this form does not delete your account immediately.</p>
                    <ul class="mb-0 ps-3">
                        <li>We verify that you control the registered email or mobile number.</li>
                        <li>An administrator reviews the verified request.</li>
                        <li>Personal profile data is then deleted or anonymized.</li>
                        <li>Ride history, payments, and legally required records may be retained with restricted access for compliance, accounting, fraud prevention, and dispute resolution (typically up to 7 years where required).</li>
                        <li>Typical processing time is up to 30 days after successful verification.</li>
                    </ul>
                </div>

                @if($errors->any())
                    <div class="alert alert-danger small" role="alert">
                        <ul class="mb-0 ps-3">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('account.deletion.store') }}" novalidate>
                    @csrf
                    <div class="mb-3">
                        <label class="form-label fw-bold" for="account_type">Account type</label>
                        <select id="account_type" name="account_type" class="form-select clean-input" required>
                            <option value="" disabled {{ old('account_type') ? '' : 'selected' }}>Select account type</option>
                            <option value="customer" @selected(old('account_type') === 'customer')>Customer / Rider</option>
                            <option value="partner" @selected(old('account_type') === 'partner')>Partner / Driver</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold" for="email">Registered email address</label>
                        <input type="email" id="email" name="email" value="{{ old('email') }}" class="form-control clean-input" placeholder="you@example.com" autocomplete="email">
                        <div class="form-text">Provide email and/or mobile. At least one is required.</div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-bold" for="mobile">Registered phone number</label>
                        <input type="tel" id="mobile" name="mobile" value="{{ old('mobile') }}" class="form-control clean-input" placeholder="+91 00000 00000" autocomplete="tel">
                    </div>

                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-clean py-3 fw-bold">Submit Deletion Request</button>
                        <a href="{{ route('policy.privacy') }}" class="btn btn-outline-dark rounded-pill">Privacy Policy</a>
                        <a href="{{ url('/') }}" class="btn btn-link text-muted">Back to website</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@include('layouts.footer')
@endsection
