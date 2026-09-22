@extends('layouts.admin')

@section('title', 'Payment Gateways')
@section('page_title', 'Manage Payment Gateways')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h5 class="fw-bold mb-0 text-dark"><i class="bi bi-credit-card text-brand me-2"></i> Payment Gateways</h5>
</div>

<div class="row g-4">
    <!-- Stripe -->
    <div class="col-md-6 col-lg-4">
        <div class="card border-0 shadow-sm rounded-4 h-100">
            <div class="card-body p-4 position-relative">
                <div class="position-absolute top-0 end-0 m-3">
                    <div class="form-check form-switch fs-5">
                        <input class="form-check-input" type="checkbox" checked>
                    </div>
                </div>
                <div class="mb-3">
                    <div class="bg-primary bg-opacity-10 d-inline-block p-3 rounded-circle text-primary mb-3">
                        <i class="bi bi-stripe" style="font-size: 1.5rem;"></i>
                    </div>
                    <h5 class="fw-bold mb-1">Stripe</h5>
                    <p class="text-muted small">Credit card processing</p>
                </div>
                
                <form action="#" method="POST" class="mt-4">
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-muted">Public Key</label>
                        <input type="text" class="form-control form-control-sm" value="pk_test_**************************" disabled>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-muted">Secret Key</label>
                        <input type="password" class="form-control form-control-sm" value="sk_test_**************************" disabled>
                    </div>
                    <button type="button" class="btn btn-outline-primary btn-sm w-100 rounded-pill">Edit Configuration</button>
                </form>
            </div>
        </div>
    </div>
    
    <!-- PayPal -->
    <div class="col-md-6 col-lg-4">
        <div class="card border-0 shadow-sm rounded-4 h-100">
            <div class="card-body p-4 position-relative">
                <div class="position-absolute top-0 end-0 m-3">
                    <div class="form-check form-switch fs-5">
                        <input class="form-check-input" type="checkbox" checked>
                    </div>
                </div>
                <div class="mb-3">
                    <div class="bg-info bg-opacity-10 d-inline-block p-3 rounded-circle text-info mb-3">
                        <i class="bi bi-paypal" style="font-size: 1.5rem;"></i>
                    </div>
                    <h5 class="fw-bold mb-1">PayPal</h5>
                    <p class="text-muted small">Digital wallet & payments</p>
                </div>
                
                <form action="#" method="POST" class="mt-4">
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-muted">Client ID</label>
                        <input type="text" class="form-control form-control-sm" value="Aex*****************************" disabled>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-muted">Secret</label>
                        <input type="password" class="form-control form-control-sm" value="EPA*****************************" disabled>
                    </div>
                    <button type="button" class="btn btn-outline-primary btn-sm w-100 rounded-pill">Edit Configuration</button>
                </form>
            </div>
        </div>
    </div>
    
    <!-- Cash -->
    <div class="col-md-6 col-lg-4">
        <div class="card border-0 shadow-sm rounded-4 h-100 border-primary">
            <div class="card-body p-4 position-relative text-center d-flex flex-column justify-content-center">
                <div class="position-absolute top-0 end-0 m-3">
                    <div class="form-check form-switch fs-5">
                        <input class="form-check-input" type="checkbox" checked disabled>
                    </div>
                </div>
                <div class="mb-3">
                    <div class="bg-success bg-opacity-10 d-inline-block p-4 rounded-circle text-success mb-3">
                        <i class="bi bi-cash-stack" style="font-size: 3rem;"></i>
                    </div>
                    <h5 class="fw-bold mb-1">Cash on Delivery</h5>
                    <p class="text-muted small">Default fallback method.</p>
                </div>
                <div class="mt-auto">
                    <span class="badge bg-success w-100 py-2 rounded-pill">System Default (Always Active)</span>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
