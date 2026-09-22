@extends('layouts.driver')

@section('title', 'Create Support Ticket - Doonspedo')

@section('content')
<div class="drv-page-header">
    <div>
        <h1>New support ticket</h1>
        <p class="text-muted mb-0 small">Provide details about your issue.</p>
    </div>
</div>

<div class="row">
    <div class="col-lg-8">
        <div class="drv-card overflow-hidden">
            <div class="p-4">
                <form action="{{ route('driver.support.store') }}" method="POST">
                    @csrf
                    <div class="row g-4">
                        <div class="col-12">
                            <label class="form-label small fw-bold text-muted text-uppercase" for="subject">Subject / issue title</label>
                            <input type="text" name="subject" id="subject" class="form-control rounded-3" placeholder="e.g. Issue with payment settlement" required>
                        </div>
                        
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-muted text-uppercase" for="category">Category</label>
                            <select name="category" id="category" class="form-select rounded-3" required>
                                <option value="payment">Payment & Earnings</option>
                                <option value="app_issue">App Technical Issue</option>
                                <option value="ride_issue">Ride Problem</option>
                                <option value="account">Account & KYC</option>
                                <option value="other" selected>Other</option>
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-muted text-uppercase" for="priority">Priority</label>
                            <select name="priority" id="priority" class="form-select rounded-3" required>
                                <option value="low">Low</option>
                                <option value="medium" selected>Medium</option>
                                <option value="high">High</option>
                                <option value="urgent">Urgent</option>
                            </select>
                        </div>

                        <div class="col-12">
                            <label class="form-label small fw-bold text-muted text-uppercase" for="description">Detailed description</label>
                            <textarea name="description" id="description" class="form-control rounded-3" rows="6" placeholder="Please explain your issue in detail so we can help you faster..." required></textarea>
                        </div>

                        <div class="col-12 mt-4 d-flex flex-wrap gap-2 justify-content-end">
                            <a href="{{ route('driver.support.index') }}" class="btn btn-light border rounded-pill px-4 fw-bold">Cancel</a>
                            <button type="submit" class="btn btn-brand rounded-pill px-5 fw-bold shadow-sm">CREATE TICKET</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm rounded-4 bg-dark text-white p-4 h-100">
            <div class="text-center mb-4">
                <i class="bi bi-info-circle text-brand display-4"></i>
            </div>
            <h5 class="fw-bold mb-3">Tips for Faster Resolution</h5>
            <ul class="list-unstyled mb-0">
                <li class="mb-3 d-flex align-items-start">
                    <i class="bi bi-check2-circle text-brand me-2 mt-1"></i>
                    <span class="small">Be specific with ride IDs if reporting a ride-related issue.</span>
                </li>
                <li class="mb-3 d-flex align-items-start">
                    <i class="bi bi-check2-circle text-brand me-2 mt-1"></i>
                    <span class="small">Explain the steps you took before the issue occurred.</span>
                </li>
                <li class="mb-0 d-flex align-items-start">
                    <i class="bi bi-check2-circle text-brand me-2 mt-1"></i>
                    <span class="small">Check our FAQ section first, as many common issues are already addressed.</span>
                </li>
            </ul>
        </div>
    </div>
</div>

<style>
.btn-brand {
    background-color: #cddc29;
    color: #000;
}
.btn-brand:hover {
    background-color: #b9c825;
}
.text-brand { color: #cddc29 !important; }
</style>
@endsection
