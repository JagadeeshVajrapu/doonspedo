@extends('layouts.app')

@section('title', 'Verify Deletion Request - Doonspedo')
@section('body_class', 'clean-login-bg')

@section('content')
@include('layouts.header')
<div class="container py-5" id="main-content">
    <div class="row justify-content-center">
        <div class="col-md-6 col-lg-5">
            <div class="clean-card p-4 p-md-5">
                <h1 class="h3 fw-bold mb-2">Verify your request</h1>
                <p class="text-muted small">Reference: <strong>{{ $requestRecord->reference_no }}</strong></p>
                <p class="small">Enter the OTP sent to your registered mobile number. This confirms you control the contact details used for the request.</p>

                @if(session('success'))
                    <div class="alert alert-success small">{{ session('success') }}</div>
                @endif
                @if(session('error'))
                    <div class="alert alert-danger small">{{ session('error') }}</div>
                @endif
                @if(session('local_deletion_otp') && app()->environment(['local', 'testing']))
                    <div class="alert alert-warning small">Local OTP: {{ session('local_deletion_otp') }}</div>
                @endif

                <form method="POST" action="{{ route('account.deletion.verify.submit', $requestRecord->reference_no) }}">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label fw-bold" for="otp">Verification code</label>
                        <input type="text" id="otp" name="otp" class="form-control clean-input" inputmode="numeric" autocomplete="one-time-code" required>
                    </div>
                    <button type="submit" class="btn btn-clean w-100 py-3 fw-bold">Verify &amp; Continue</button>
                </form>
            </div>
        </div>
    </div>
</div>
@include('layouts.footer')
@endsection
