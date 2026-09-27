@extends('layouts.app')

@section('title', 'Customer KYC - Doonspedo')
@section('body_class', 'rider-shell')

@section('content')
<div class="rider-shell-page">
    <header class="rider-topbar">
        <a href="{{ route('profile.edit') }}" class="rider-back-btn" aria-label="Back to profile">
            <i class="bi bi-arrow-left"></i>
        </a>
        <h1>KYC</h1>
    </header>
    <main class="rider-content">
        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif

        <div class="rider-card mb-3">
            <p class="small text-muted mb-1">Status</p>
            @if(!$submission)
                <span class="badge bg-secondary">Not submitted</span>
            @elseif($submission->status === 'pending')
                <span class="badge bg-warning text-dark">Pending</span>
            @elseif($submission->status === 'approved')
                <span class="badge bg-success">Approved</span>
            @else
                <span class="badge bg-danger">Rejected</span>
                @if($submission->rejection_reason)
                    <p class="small text-danger mt-2 mb-0">{{ $submission->rejection_reason }}</p>
                @endif
            @endif
        </div>

        @if(!$submission || $submission->status === 'rejected')
        <form class="rider-card" method="POST" action="{{ route('rider.kyc.store') }}" enctype="multipart/form-data">
            @csrf
            <div class="mb-3">
                <label class="form-label" for="kyc-name">Full name</label>
                <input id="kyc-name" name="full_name" class="form-control" value="{{ old('full_name', auth()->user()->name) }}" required>
            </div>
            <div class="mb-3">
                <label class="form-label" for="kyc-type">Document</label>
                <select id="kyc-type" name="document_type" class="form-select" required>
                    <option value="aadhaar">Aadhaar</option>
                    <option value="pan">PAN</option>
                    <option value="driving_licence">Driving licence</option>
                </select>
            </div>
            <div class="mb-3">
                <label class="form-label" for="kyc-number">Document number</label>
                <input id="kyc-number" name="document_number" class="form-control" value="{{ old('document_number') }}">
            </div>
            <div class="mb-3">
                <label class="form-label" for="kyc-file">Upload document</label>
                <input id="kyc-file" name="document" type="file" class="form-control" accept=".jpg,.jpeg,.png,.pdf" required>
            </div>
            <button class="btn btn-brand" type="submit">Submit KYC</button>
        </form>
        @endif
    </main>
</div>
@endsection
