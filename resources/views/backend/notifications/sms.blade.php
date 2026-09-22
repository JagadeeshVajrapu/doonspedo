@extends('layouts.admin')

@section('title', 'SMS Gateways')
@section('page_title', 'SMS Gateways')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h5 class="fw-bold mb-0 text-dark"><i class="bi bi-chat-text-fill text-brand me-2"></i> SMS Gateways</h5>
</div>

@if(session('success'))
    @include('partials.ui.alert', ['variant' => 'success', 'message' => session('success')])
@endif

<div class="row g-4">
    <!-- Twilio Card -->
    <div class="col-md-6">
        <div class="card border-0 shadow-sm rounded-4 h-100">
            <div class="card-body p-4 position-relative">
                <div class="position-absolute top-0 end-0 m-3">
                    <div class="form-check form-switch fs-5">
                        <input class="form-check-input" type="checkbox" checked>
                    </div>
                </div>
                <div class="mb-3">
                    <div class="bg-primary bg-opacity-10 d-inline-block p-3 rounded-circle text-primary mb-3">
                        <i class="bi bi-phone-vibrate" style="font-size: 1.5rem;"></i>
                    </div>
                    <h5 class="fw-bold mb-1">Twilio Gateway</h5>
                    <p class="text-muted small">Send global OTPs and Alerts</p>
                </div>
                
                <form action="{{ route('admin.notifications.sms.store') }}" method="POST" class="mt-4">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-muted">Account SID</label>
                        <input type="text" class="form-control" value="AC01************************" disabled>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-muted">Auth Token</label>
                        <input type="password" class="form-control" value="************************" disabled>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-muted">Sender Number (From)</label>
                        <input type="text" class="form-control" value="+18005550199" disabled>
                    </div>
                    <button type="submit" class="btn btn-outline-primary w-100 rounded-pill mt-3 shadow-sm">Save Twilio Config</button>
                </form>
            </div>
        </div>
    </div>
    
    <!-- Vonage / Nexmo Card -->
    <div class="col-md-6">
        <div class="card border-0 shadow-sm rounded-4 h-100">
            <div class="card-body p-4 position-relative">
                <div class="position-absolute top-0 end-0 m-3">
                    <div class="form-check form-switch fs-5">
                        <input class="form-check-input" type="checkbox">
                    </div>
                </div>
                <div class="mb-3">
                    <div class="bg-info bg-opacity-10 d-inline-block p-3 rounded-circle text-info mb-3">
                        <i class="bi bi-globe" style="font-size: 1.5rem;"></i>
                    </div>
                    <h5 class="fw-bold mb-1">Vonage (Nexmo)</h5>
                    <p class="text-muted small">Secondary routing channel</p>
                </div>
                
                <form action="{{ route('admin.notifications.sms.store') }}" method="POST" class="mt-4">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-muted">API Key</label>
                        <input type="text" class="form-control" placeholder="Enter Vonage API Key">
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-muted">API Secret</label>
                        <input type="password" class="form-control" placeholder="Enter API Secret">
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-muted">Brand Name / Sender ID</label>
                        <input type="text" class="form-control" placeholder="e.g. DOONSPD">
                    </div>
                    <button type="submit" class="btn btn-outline-primary w-100 rounded-pill mt-3 shadow-sm">Save Vonage Config</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
