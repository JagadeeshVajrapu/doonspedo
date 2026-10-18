@extends('layouts.app')

@section('title', 'Deletion Request Received - Doonspedo')
@section('body_class', 'clean-login-bg')

@section('content')
@include('layouts.header')
<div class="container py-5" id="main-content">
    <div class="row justify-content-center">
        <div class="col-md-7 col-lg-6">
            <div class="clean-card p-4 p-md-5 text-center">
                <div class="mb-3">@include('partials.ui.brand-logo', ['size' => 'md'])</div>
                <h1 class="h3 fw-bold mb-3">Request received</h1>
                @if(session('success'))
                    <div class="alert alert-success small text-start">{{ session('success') }}</div>
                @endif
                <p class="mb-2">Your request reference number:</p>
                <p class="fs-4 fw-bold text-brand mb-3">{{ $requestRecord->reference_no }}</p>
                <p class="text-muted small mb-4">
                    Status: <strong>{{ str_replace('_', ' ', $requestRecord->status) }}</strong>.
                    Your account has not been deleted yet. After review and completion, personal profile data will be deleted or anonymized.
                    Ride and payment records required for legal, accounting, or fraud-prevention purposes may be retained with restricted access.
                </p>
                <div class="d-grid gap-2">
                    <a href="{{ route('policy.privacy') }}" class="btn btn-outline-dark rounded-pill">Privacy Policy</a>
                    <a href="{{ url('/') }}" class="btn btn-clean py-3">Back to website</a>
                </div>
            </div>
        </div>
    </div>
</div>
@include('layouts.footer')
@endsection
