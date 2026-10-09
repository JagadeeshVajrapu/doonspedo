@extends('layouts.app')

@section('title', 'Customer KYC - Doonspedo')
@section('body_class', 'rider-shell')

@section('content')
@php
    $submissions = $submissions ?? collect();
    $aadhaarStatus = $aadhaar->status ?? null;
    $aadhaarLabel = \App\Support\CustomerKycGate::statusLabel($aadhaarStatus, (bool) $aadhaar);
    $selectedType = old('document_type', 'aadhaar');
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
            <p class="small text-muted mb-1">Aadhaar verification</p>
            <span class="badge {{ $aadhaarLabel === 'Verified' ? 'bg-success' : ($aadhaarLabel === 'Rejected' ? 'bg-danger' : ($aadhaarLabel === 'Pending Verification' ? 'bg-warning text-dark' : 'bg-secondary')) }}">{{ $aadhaarLabel }}</span>
            @if($aadhaar && $aadhaar->aadhaar_last4)
                <p class="small text-muted mt-2 mb-0">Aadhaar ending {{ $aadhaar->aadhaar_last4 }}</p>
            @endif
            <p class="small text-muted mt-2 mb-0">OTP verification uses an authorized Aadhaar provider. It is not sent as a normal SMS login code.</p>
        </div>

        @if($aadhaarLabel !== 'Verified')
        <form class="rider-card mb-3" method="POST" action="{{ route('rider.kyc.aadhaar.otp') }}">
            @csrf
            <h2 class="h6 fw-bold mb-3">Start Aadhaar verification</h2>
            <label class="form-label" for="aadhaar-number">Aadhaar number</label>
            <input id="aadhaar-number" name="aadhaar_number" class="form-control mb-3" inputmode="numeric" autocomplete="off" maxlength="12" pattern="[0-9]{12}" placeholder="12 digits" required>
            <button class="btn btn-brand w-100" type="submit">Send Aadhaar OTP</button>
        </form>
        <form class="rider-card mb-3" method="POST" action="{{ route('rider.kyc.aadhaar.verify') }}">
            @csrf
            <label class="form-label" for="aadhaar-otp">Aadhaar OTP</label>
            <input id="aadhaar-otp" name="otp" class="form-control mb-3" inputmode="numeric" autocomplete="one-time-code" maxlength="8" required>
            <button class="btn btn-outline-dark w-100" type="submit">Verify OTP</button>
        </form>
        @endif

        <div class="rider-card mb-3">
            <h2 class="h6 fw-bold mb-3">Documents</h2>
            @forelse($submissions as $item)
                <div class="d-flex justify-content-between gap-3 py-2 border-bottom">
                    <div>
                        <div class="fw-bold">{{ $item->document_label ?: ucfirst(str_replace('_', ' ', $item->document_type)) }}</div>
                        <div class="small text-muted">{{ \App\Support\CustomerKycGate::mask($item->document_type, $item->document_number) }}</div>
                        @if($item->status === 'rejected' && $item->rejection_reason)
                            <div class="small text-danger">{{ $item->rejection_reason }}</div>
                        @endif
                    </div>
                    <span class="badge align-self-start {{ $item->status === 'approved' ? 'bg-success' : ($item->status === 'rejected' ? 'bg-danger' : 'bg-warning text-dark') }}">{{ \App\Support\CustomerKycGate::statusLabel($item->status) }}</span>
                </div>
            @empty
                <p class="small text-muted mb-0">Not Submitted</p>
            @endforelse
        </div>

        <form class="rider-card" method="POST" action="{{ route('rider.kyc.store') }}" enctype="multipart/form-data">
            @csrf
            <h2 class="h6 fw-bold mb-3">Add a document</h2>
            <div class="mb-3">
                <label class="form-label" for="kyc-name">Full name</label>
                <input id="kyc-name" name="full_name" class="form-control" value="{{ old('full_name', $submission->full_name ?? auth()->user()->name) }}" required autocomplete="name">
            </div>
            <div class="mb-3">
                <label class="form-label" for="kyc-type">Document type</label>
                <select id="kyc-type" name="document_type" class="form-select" required>
                    <option value="aadhaar" @selected($selectedType === 'aadhaar')>Aadhaar</option>
                    <option value="pan" @selected($selectedType === 'pan')>PAN</option>
                    <option value="driving_licence" @selected($selectedType === 'driving_licence')>Driving licence</option>
                    <option value="voter_id" @selected($selectedType === 'voter_id')>Voter ID</option>
                    <option value="other" @selected($selectedType === 'other')>Other document</option>
                </select>
            </div>
            <div class="mb-3">
                <label class="form-label" for="kyc-label">Other document name</label>
                <input id="kyc-label" name="document_label" class="form-control" value="{{ old('document_label') }}" maxlength="80" placeholder="Required only for Other document">
            </div>
            <div class="mb-3">
                <label class="form-label" for="kyc-number">Document number</label>
                <input id="kyc-number" name="document_number" class="form-control" value="{{ old('document_number') }}" required autocomplete="off">
            </div>
            <div class="mb-3">
                <label class="form-label" for="kyc-file">Upload document</label>
                <input id="kyc-file" name="document" type="file" class="form-control" accept=".jpg,.jpeg,.png,.pdf,image/jpeg,image/png,application/pdf" required>
                <p class="form-text mb-0">JPG, PNG, or PDF, up to 5 MB. The file is stored privately.</p>
            </div>
            <button class="btn btn-brand w-100" type="submit">Submit document</button>
        </form>
    </main>
</div>
@endsection
