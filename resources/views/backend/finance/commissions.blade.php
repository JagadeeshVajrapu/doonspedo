@extends('layouts.admin')

@section('title', 'Commission Rates')
@section('page_title', 'Manage Commission Rates')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h5 class="fw-bold mb-0 text-dark"><i class="bi bi-percent text-brand me-2"></i> Configure Commission Rates</h5>
</div>

@if(session('success'))
    @include('partials.ui.alert', ['variant' => 'success', 'message' => session('success')])
@endif

<div class="row g-4">
    <div class="col-md-8 mx-auto">
        <div class="admin-card">
            <div class="card-body p-5 text-center">
                <i class="bi bi-currency-dollar text-muted mb-4" style="font-size: 4rem;"></i>
                <h4 class="fw-bold text-dark mb-3">Global Commission Settings</h4>
                <p class="text-muted mb-4 px-md-5">Set the default percentage the platform takes from each booking. Individual drivers can have custom commissions configured on their profiles.</p>
                
                <form action="{{ route('admin.finance.commissions.update') }}" method="POST" class="text-start">
                    @csrf
                    
                    <div class="mb-4">
                        <label class="form-label fw-bold small text-muted">Platform Fee Type</label>
                        <select name="type" class="form-select">
                            <option value="percentage" selected>Percentage (%)</option>
                            <option value="fixed">Fixed Amount ({{ $default_currency->symbol ?? '₹' }})</option>
                        </select>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-bold small text-muted">Global Commission Rate / Amount</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0 text-secondary"><i class="bi bi-percent"></i></span>
                            <input type="number" class="form-control" name="commission_rate" value="15.00" step="0.01">
                        </div>
                        <div class="form-text small">e.g., 15% will deduct exactly {{ $default_currency->symbol ?? '₹' }}1.50 from a {{ $default_currency->symbol ?? '₹' }}10.00 ride.</div>
                    </div>
                    
                    <div class="mb-4">
                        <label class="form-label fw-bold small text-muted">Apply To Services</label>
                        <div class="d-flex gap-4">
                            <div class="form-check form-switch fs-5">
                                <input class="form-check-input" type="checkbox" id="rideService" checked>
                                <label class="form-check-label fs-6 ms-2" for="rideService">Rides</label>
                            </div>
                            <div class="form-check form-switch fs-5">
                                <input class="form-check-input" type="checkbox" id="parcelService" checked>
                                <label class="form-check-label fs-6 ms-2" for="parcelService">Parcel</label>
                            </div>
                            <div class="form-check form-switch fs-5">
                                <input class="form-check-input" type="checkbox" id="freightService" checked>
                                <label class="form-check-label fs-6 ms-2" for="freightService">Freight</label>
                            </div>
                        </div>
                    </div>
                    
                    <div class="text-end pt-3 border-top border-secondary border-opacity-10">
                        <button type="submit" class="btn btn-primary rounded-pill px-5 fw-bold shadow-sm">
                            <i class="bi bi-save me-1"></i> Update Commission Rules
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
