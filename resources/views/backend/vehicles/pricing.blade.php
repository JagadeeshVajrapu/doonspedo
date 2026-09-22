@extends('layouts.admin')

@section('title', 'Pricing Rules')
@section('page_title', 'Pricing Rules & Fares')

@section('content')
@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show rounded-4 border-0 shadow-sm mb-4" role="alert">
        <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif
<div class="d-flex justify-content-between align-items-center mb-4">
    <h5 class="fw-bold mb-0 text-dark"><i class="bi bi-tag text-brand me-2"></i> Peak Pricing & Custom Rules</h5>
    <button class="btn btn-brand btn-sm px-3 rounded-pill fw-bold shadow-sm">
        <i class="bi bi-plus-lg me-1"></i> Add Rule
    </button>
</div>

<!-- Base Pricing Info -->
<div class="alert alert-info border-0 shadow-sm rounded-4 mb-4 d-flex align-items-center">
    <i class="bi bi-info-circle-fill fs-4 me-3 text-primary"></i>
    <div>
        <strong>Note:</strong> Default base rates are configured inside <a href="{{ route('admin.settings.index') }}" class="fw-bold alert-link text-decoration-underline">Settings > Fares configuration</a>. Add custom rules here to override those default fares.
    </div>
</div>

<!-- AC vs Non-AC Configuration -->
<form action="{{ route('admin.vehicles.pricing.update') }}" method="POST">
    @csrf
    <!-- AC vs Non-AC Configuration -->
    <div class="row mb-5">
        <div class="col-12">
            <h6 class="fw-bold px-2 py-3 text-muted text-uppercase mb-0 border-bottom mb-3"><i class="bi bi-fan me-2"></i> AC vs Non-AC Configuration</h6>
            
            <div class="row g-4 position-relative">
                <!-- AC Price Panel -->
                <div class="col-md-5">
                    <div class="card bg-white p-4 rounded-4 shadow-sm h-100 border-0 border-top border-info border-5 text-center">
                        <div class="card-body p-0">
                            <i class="bi bi-snow text-info mb-3 d-inline-block p-3 rounded-circle bg-info bg-opacity-10" style="font-size: 2.5rem;"></i>
                            <h5 class="fw-bold text-dark mb-2">AC Conditioned</h5>
                            <p class="text-muted small mb-4 px-3">Set the base per KM rate for comfortable, air-conditioned rides.</p>
                            
                            <div class="input-group mb-3 px-md-3">
                                <span class="input-group-text bg-light border-end-0 fw-bold border-info text-info">{{ $default_currency->symbol ?? '₹' }}</span>
                                <input type="number" name="ac_rate_per_km" step="0.5" class="form-control border-start-0 border-info text-center fw-bold fs-5" placeholder="Rate" value="{{ $settings['ac_rate_per_km'] ?? '15.00' }}">
                                <span class="input-group-text bg-light border-info">/KM</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- VS Badge -->
                <div class="col-md-2 d-flex align-items-center justify-content-center">
                    <div class="rounded-circle shadow-sm border border-light border-5 bg-white d-flex align-items-center justify-content-center fw-bold text-muted fs-4" style="width: 70px; height: 70px;">VS</div>
                </div>

                <!-- Non-AC Price Panel -->
                <div class="col-md-5">
                    <div class="card bg-white p-4 rounded-4 shadow-sm h-100 border-0 border-top border-warning border-5 text-center">
                        <div class="card-body p-0">
                            <i class="bi bi-window text-warning mb-3 d-inline-block p-3 rounded-circle bg-warning bg-opacity-10" style="font-size: 2.5rem;"></i>
                            <h5 class="fw-bold text-dark mb-2">Non-AC Basic</h5>
                            <p class="text-muted small mb-4 px-3">Set the standard budget-friendly per KM rate without AC.</p>
                            
                            <div class="input-group mb-3 px-md-3">
                                <span class="input-group-text bg-light border-end-0 fw-bold border-warning text-warning">{{ $default_currency->symbol ?? '₹' }}</span>
                                <input type="number" name="non_ac_rate_per_km" step="0.5" class="form-control border-start-0 border-warning text-center fw-bold fs-5" placeholder="Rate" value="{{ $settings['non_ac_rate_per_km'] ?? '10.00' }}">
                                <span class="input-group-text bg-light border-warning">/KM</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4 mt-2">
        <div class="col-12"><h6 class="fw-bold px-2 text-muted text-uppercase mb-0 border-bottom pb-2"><i class="bi bi-graph-up-arrow me-2"></i> Dynamic Modifiers & Surge</h6></div>

        <!-- Surge Rule 1 -->
        <div class="col-md-6">
            <div class="card shadow-sm border-0 rounded-4 h-100">
                <div class="card-header bg-white py-3 border-0 d-flex justify-content-between align-items-center">
                    <h6 class="mb-0 fw-bold"><i class="bi bi-cloud-rain me-2 px-2 py-1 bg-primary text-white rounded"></i> Weather Conditions</h6>
                    <div class="form-check form-switch m-0">
                        <input class="form-check-input" type="checkbox" name="rain_surge_enabled" value="1" {{ ($settings['rain_surge_enabled'] ?? '0') == '1' ? 'checked' : '' }}>
                    </div>
                </div>
                <div class="card-body bg-light rounded-bottom-4 p-4">
                    <h6 class="fw-bold text-dark">Heavy Rain Surge</h6>
                    <p class="text-muted small">Automatically multiplies base fare by multiplier during extreme rain conditions.</p>
                    
                    <div class="d-flex align-items-center mt-3 bg-white p-2 rounded shadow-sm border border-primary border-opacity-25">
                        <div class="bg-primary bg-opacity-10 text-primary p-2 rounded px-3 me-3"><i class="bi bi-x-lg fw-bold"></i></div>
                        <div class="flex-grow-1">
                            <div class="text-muted" style="font-size: 0.70rem; font-weight: bold;">PRICE MULTIPLIER</div>
                            <input type="number" name="rain_surge_multiplier" step="0.1" class="form-control form-control-sm border-0 fw-bold fs-5 p-0 bg-transparent shadow-none" value="{{ $settings['rain_surge_multiplier'] ?? '1.5' }}">
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Surge Rule 2 -->
        <div class="col-md-6">
            <div class="card shadow-sm border-0 rounded-4 h-100">
                <div class="card-header bg-white py-3 border-0 d-flex justify-content-between align-items-center">
                    <h6 class="mb-0 fw-bold"><i class="bi bi-moon-stars me-2 px-2 py-1 bg-dark text-white rounded"></i> Late Night Premium</h6>
                    <div class="form-check form-switch m-0">
                        <input class="form-check-input" type="checkbox" name="night_premium_enabled" value="1" {{ ($settings['night_premium_enabled'] ?? '0') == '1' ? 'checked' : '' }}>
                    </div>
                </div>
                <div class="card-body bg-light rounded-bottom-4 p-4">
                    <h6 class="fw-bold text-dark">Midnight to 5:00 AM</h6>
                    <p class="text-muted small">Adds a fixed premium flat cost to every ride occurring late at night.</p>
                    
                    <div class="d-flex align-items-center mt-3 bg-white p-2 rounded shadow-sm border border-dark border-opacity-25">
                        <div class="bg-dark bg-opacity-10 text-dark p-2 rounded px-3 me-3"><i class="bi bi-plus-lg fw-bold"></i></div>
                        <div class="flex-grow-1">
                            <div class="text-muted" style="font-size: 0.70rem; font-weight: bold;">FIXED PREMIUM ADDITION</div>
                            <div class="d-flex align-items-center">
                                <span class="fw-bold fs-5 me-1">{{ $default_currency->symbol ?? '₹' }}</span>
                                <input type="number" name="night_premium_amount" step="1" class="form-control form-control-sm border-0 fw-bold fs-5 p-0 bg-transparent shadow-none" value="{{ $settings['night_premium_amount'] ?? '5.00' }}">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="text-center mt-5">
        <button type="submit" class="btn btn-brand px-5 py-3 rounded-pill fw-bold shadow-lg">
            <i class="bi bi-check2-circle me-2"></i> SAVE ALL PRICING RULES
        </button>
    </div>
</form>

@endsection
