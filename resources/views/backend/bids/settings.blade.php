@extends('layouts.admin')

@section('title', 'Bidding Settings')
@section('page_title', 'Configure Bidding System')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h5 class="fw-bold mb-0 text-dark"><i class="bi bi-gear-wide-connected text-brand me-2"></i> Bidding Module Settings</h5>
</div>

<div class="row g-4">
    <div class="col-md-8 mx-auto">
        <div class="admin-card">
            <div class="card-body p-5 text-center">
                <i class="bi bi-hammer text-muted mb-4" style="font-size: 4rem;"></i>
                <h4 class="fw-bold text-dark mb-3">Bidding System Controls</h4>
                <p class="text-muted mb-4 px-md-5">Configure how drivers bid on rides, freight, or parcel bookings. Here you can set auto-approval limits, bidding time windows, and threshold margins.</p>
                
                <form action="{{ route('admin.bids.settings.update') }}" method="POST" class="text-start">
                    @csrf
                    <div class="row g-4 mb-4">
                        <div class="col-md-6">
                            <label class="form-label fw-bold small text-muted">Enable Driver Bidding</label>
                            <div class="form-check form-switch fs-5">
                                <input class="form-check-input" type="checkbox" id="enableBidding" name="enable_driver_bidding" value="1" {{ (isset($settings['enable_driver_bidding']) && $settings['enable_driver_bidding'] == '1') ? 'checked' : '' }}>
                                <label class="form-check-label fs-6 ms-2" for="enableBidding">Active</label>
                            </div>
                            <div class="form-text small">Toggle the bidding feature on or off across the platform.</div>
                        </div>
                        
                        <div class="col-md-6">
                            <label class="form-label fw-bold small text-muted">Auto-Approve Lowest Bid</label>
                            <div class="form-check form-switch fs-5">
                                <input class="form-check-input" type="checkbox" id="autoApprove" name="auto_approve_lowest_bid" value="1" {{ (isset($settings['auto_approve_lowest_bid']) && $settings['auto_approve_lowest_bid'] == '1') ? 'checked' : '' }}>
                                <label class="form-check-label fs-6 ms-2" for="autoApprove">Inactive</label>
                            </div>
                            <div class="form-text small">If enabled, the lowest bid will be accepted automatically after timeout.</div>
                        </div>
                    </div>
                    
                    <div class="mb-4">
                        <label class="form-label fw-bold small text-muted">Bidding Time Window (minutes)</label>
                        <input type="number" class="form-control" name="bidding_timeout" value="{{ $settings['bidding_timeout'] ?? '15' }}">
                        <div class="form-text small">How long a booking request remains open for driver bids.</div>
                    </div>
                    
                    <div class="mb-4">
                        <label class="form-label fw-bold small text-muted">Maximum Bid Deviation (%)</label>
                        <input type="number" class="form-control" name="bid_deviation" value="{{ $settings['bid_deviation'] ?? '20' }}">
                        <div class="form-text small">Maximum percentage a driver can bid above the suggested base fare.</div>
                    </div>
                    
                    <div class="text-end pt-3 border-top border-secondary border-opacity-10">
                        <button type="submit" class="btn btn-primary rounded-pill px-5 fw-bold shadow-sm">
                            <i class="bi bi-save me-1"></i> Save Configuration
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
