@extends('layouts.app')

@section('title', 'Customer KYC - Doonspedo')
@section('body_class', 'rider-shell')

@section('content')
@php
    $canEdit = !$submission || $submission->status !== 'approved';
    $selectedType = old('document_type', $submission->document_type ?? 'aadhaar');
@endphp
<div class="rider-shell-page rider-kyc-page">
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
        @if ($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0 ps-3">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="rider-card mb-3">
            <p class="small text-muted mb-1">Status</p>
            @if(!$submission)
                <span class="badge bg-secondary">Not submitted</span>
                <p class="small text-muted mt-2 mb-0">Submit the details below. You can update them until an admin approves the KYC.</p>
            @elseif($submission->status === 'pending')
                <span class="badge bg-warning text-dark">Pending review</span>
                <p class="small text-muted mt-2 mb-0">Your documents are with the admin. You can still replace them if something is wrong.</p>
            @elseif($submission->status === 'approved')
                <span class="badge bg-success">Approved</span>
                <p class="small text-muted mt-2 mb-0">Your KYC is approved. No further update is needed.</p>
            @else
                <span class="badge bg-danger">Rejected</span>
                @if($submission->rejection_reason)
                    <p class="small text-danger mt-2 mb-0">{{ $submission->rejection_reason }}</p>
                @endif
                <p class="small text-muted mt-2 mb-0">Upload a clearer copy using the form below.</p>
            @endif
        </div>

        <div class="rider-card mb-3">
            <h2 class="h6 fw-bold mb-3">What you need</h2>
            <ul class="list-unstyled mb-0 d-flex flex-column gap-2">
                <li class="d-flex gap-2 small">
                    <i class="bi bi-check-circle-fill text-success mt-1"></i>
                    <span><strong>Full name</strong> as printed on your ID.</span>
                </li>
                <li class="d-flex gap-2 small">
                    <i class="bi bi-check-circle-fill text-success mt-1"></i>
                    <span><strong>Aadhaar</strong> is the required ID. Use PAN or a driving licence only if you do not have Aadhaar.</span>
                </li>
                <li class="d-flex gap-2 small">
                    <i class="bi bi-check-circle-fill text-success mt-1"></i>
                    <span><strong>ID number</strong> — 12 digits for Aadhaar, or the number printed on PAN / licence.</span>
                </li>
                <li class="d-flex gap-2 small">
                    <i class="bi bi-check-circle-fill text-success mt-1"></i>
                    <span><strong>Clear photo or PDF</strong> of that ID. JPG, PNG, or PDF, up to 5 MB. All four corners should be visible.</span>
                </li>
            </ul>
        </div>

        @if($canEdit)
        <form class="rider-card" method="POST" action="{{ route('rider.kyc.store') }}" enctype="multipart/form-data">
            @csrf
            <div class="mb-3">
                <label class="form-label" for="kyc-name">Full name</label>
                <input id="kyc-name" name="full_name" class="form-control" value="{{ old('full_name', $submission->full_name ?? auth()->user()->name) }}" required autocomplete="name">
            </div>
            <div class="mb-3">
                <label class="form-label" for="kyc-type">Document</label>
                <select id="kyc-type" name="document_type" class="form-select" required>
                    <option value="aadhaar" @selected($selectedType === 'aadhaar')>Aadhaar (required)</option>
                    <option value="pan" @selected($selectedType === 'pan')>PAN</option>
                    <option value="driving_licence" @selected($selectedType === 'driving_licence')>Driving licence</option>
                </select>
            </div>
            <div class="mb-3">
                <label class="form-label" for="kyc-number">Document number</label>
                <input id="kyc-number" name="document_number" class="form-control" value="{{ old('document_number', $submission->document_number ?? '') }}" required inputmode="text" autocomplete="off" placeholder="Aadhaar is 12 digits">
            </div>
            <div class="mb-3">
                <label class="form-label" for="kyc-file">Upload document</label>
                <input id="kyc-file" name="document" type="file" class="form-control" accept=".jpg,.jpeg,.png,.pdf,image/jpeg,image/png,application/pdf" @if(!$submission || $submission->status === 'rejected') required @endif>
                <p class="form-text mb-0">
                    @if($submission && $submission->status === 'pending')
                        A file is already on record. Choose a new file only if you want to replace it.
                    @else
                        Take a photo or choose a file from your phone.
                    @endif
                </p>
            </div>
            <button class="btn btn-brand w-100" type="submit">{{ $submission ? 'Update KYC' : 'Submit KYC' }}</button>
        </form>
        @elseif($submission)
        <div class="rider-card">
            <p class="small text-muted mb-1">Submitted name</p>
            <p class="fw-bold mb-3">{{ $submission->full_name }}</p>
            <p class="small text-muted mb-1">Document</p>
            <p class="mb-0">{{ ucfirst(str_replace('_', ' ', $submission->document_type)) }} · {{ $submission->document_number }}</p>
        </div>
        @endif
    </main>
</div>
@endsection
