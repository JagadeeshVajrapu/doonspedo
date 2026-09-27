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
                <p class="text-muted mb-4 px-md-5">This commission is debited from the partner wallet only after a ride is completed. One active setting applies to every completed ride.</p>
                
                <div class="alert alert-light border text-start mb-4">
                    Current commission:
                    <strong>{{ $setting ? $setting->label() : 'Not configured' }}</strong>
                    {{ $setting && $setting->is_active ? '(active)' : '(inactive)' }}
                </div>
                <form action="{{ route('admin.finance.commissions.update') }}" method="POST" class="text-start">
                    @csrf
                    
                    <div class="mb-4">
                        <label class="form-label fw-bold small text-muted">Platform Fee Type</label>
                        <select name="type" class="form-select">
                            <option value="fixed" @selected(old('type', $setting?->type ?? 'fixed') === 'fixed')>Fixed amount per completed ride</option>
                            <option value="percentage" @selected(old('type', $setting?->type ?? '') === 'percentage')>Percentage of fare</option>
                        </select>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-bold small text-muted">Amount</label>
                        <input type="number" class="form-control" name="amount" value="{{ old('amount', $setting?->amount ?? '20.00') }}" step="0.01" min="0" required>
                        <div class="form-text small">Fixed example: 20 deducts ₹20 from the partner wallet after the ride is completed. This is not taken when the ride is only accepted.</div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-bold small text-muted">Low wallet warning (₹)</label>
                        <input type="number" class="form-control" name="low_balance_threshold" value="{{ old('low_balance_threshold', $setting?->low_balance_threshold ?? '100') }}" step="0.01" min="0" required>
                    </div>
                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label class="form-label fw-bold small text-muted">Minimum recharge</label>
                            <input type="number" class="form-control" name="min_recharge" value="{{ old('min_recharge', $setting?->min_recharge ?? '10') }}" step="0.01" min="1" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold small text-muted">Maximum recharge</label>
                            <input type="number" class="form-control" name="max_recharge" value="{{ old('max_recharge', $setting?->max_recharge ?? '50000') }}" step="0.01" required>
                        </div>
                    </div>
                    <div class="form-check mb-4">
                        <input class="form-check-input" type="checkbox" name="is_active" value="1" id="commission-active" @checked(old('is_active', $setting?->is_active ?? true))>
                        <label class="form-check-label" for="commission-active">Active. When off, completed rides keep the previous earnings credit and do not debit this commission.</label>
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
